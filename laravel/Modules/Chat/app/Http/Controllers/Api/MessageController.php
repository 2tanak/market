<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Modules\Chat\Events\Privates;
use Modules\Chat\Jobs\CheckMessageDelivery;
use Modules\Chat\Model\Chats\Model as Chat;
use Modules\Chat\Model\Messages\Model as Messages;
use Modules\Chat\Resources\MessageResource;

class MessageController extends Controller
{
    public function privatIndex(Request $request)
    {
        return view('chat::privat');
    }

    public function message(Request $request)
    {
        // Валидация под новую структуру комнат
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|string|max:191',
            'text' => 'required|string|max:5000',
            'chat_id' => 'nullable|integer|exists:chats,id',
            'listing_id' => 'required_without:chat_id|nullable|integer|exists:listings,id',
            'seller_id' => 'required_without:chat_id|nullable|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $validatedData = $validator->validated();
        $myId = auth()->id();
        $chatId = $validatedData['chat_id'] ?? null;

        if (! $chatId) {
            if ((int) $myId === (int) $validatedData['seller_id']) {
                return response()->json(['status' => 'error', 'message' => 'Нельзя писать самому себе'], 400);
            }

            $chat = Chat::firstOrCreate([
                'listing_id' => $validatedData['listing_id'],
                'buyer_id' => $myId,
                'seller_id' => $validatedData['seller_id'],
            ]);
            $chatId = $chat->id;
        } else {
            // Если chat_id пришел — просто проверяем, что юзер состоит в этом чате
            $chat = Chat::where('id', $chatId)
                ->where(function ($query) use ($myId) {
                    $query->where('buyer_id', $myId)->orWhere('seller_id', $myId);
                })
                ->firstOrFail();
        }

        $messageData = [
            'uuid' => $validatedData['uuid'],
            'chat_id' => $chatId,
            'sender_id' => $myId,
            'text' => $validatedData['text'],
            'status' => 1, // Статус: отправлено на сервер
        ];

        try {
            $message = Messages::create($messageData)->load('sender');
        } catch (QueryException $e) {
            Log::channel('chat')->error('Ошибка в чате: '.$e->getMessage(), [
                'uuid' => $messageData['uuid'],
                'trace' => $e->getTraceAsString(),
            ]);
            if ($e->errorInfo[1] == 1062) {

                $messageData['uuid'] = $messageData['uuid'].'_dup_'.now()->format('H-i-s');

                $message = Messages::create($messageData)->load('sender');
            } else {
                throw $e;
            }
        }
        // 3. ОБЯЗАТЕЛЬНО: обновляем updated_at у чата, чтобы он поднялся в топ списков чатов
        $chat->touch();

        $receiverId = $validatedData['seller_id']
            ?? ((int) $chat->buyer_id === (int) $myId ? $chat->seller_id : $chat->buyer_id);

        $resource = (new MessageResource($message))->additional(['seller_id' => $receiverId]);

        event(new Privates($resource->resolve()));

        return response()->json([
            'status' => 'success',
            'message' => $resource,
        ]);

    }

    public function forceQueue(Request $request)
    {
        $msgId = $request->message_id;
        $redisKey = "msg_status:{$msgId}";

        // 1. Проверяем статус в Redis
        $currentStatus = Redis::get($redisKey);
        if (is_null($currentStatus)) {
            // Ставим статус 1 (запускаем процесс отслеживания)
            Redis::setex($redisKey, 86400, 1);

            Log::channel('ping_socket')->info("Первый оффлайн! Создаем Job для ID: {$msgId}");

            // Запускаем Job ОДИН РАЗ
            CheckMessageDelivery::dispatch($msgId)->delay(now()->addSeconds(30));

            return response()->json(['status' => 'job_started']);
        }

        // 3. Если статус уже 1 или 2 — ничего не делаем, Job уже либо в пути, либо не нужен
        Log::channel('ping_socket')->info("Дублирующий запрос для ID: {$msgId}, статус уже: {$currentStatus}");

        return response()->json(['status' => 'already_processed']);

    }

    public function updateStatus(Request $request)
    {
        // Валидация, чтобы не прилетело пустоты
        $validator = Validator::make($request->all(), [
            'message_id' => 'required|integer',
            'status' => 'required|integer|in:2,3', // 2-доставлено, 3-прочитано
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid data'], 422);
        }

        $msgId = $request->message_id;
        $newStatus = (int) $request->status;
        $redisKey = "msg_status:{$msgId}";

        dispatch(function () use ($msgId, $redisKey, $newStatus) {

            // 1. Проверяем текущий статус в Redis
            $currentStatus = (int) Redis::get($redisKey);

            // 2. Обновляем ТОЛЬКО если новый статус выше текущего
            // (защита от того, чтобы "прочитано" не затерлось "доставлено")
            if ($newStatus > $currentStatus) {

                // Обновляем Redis (чтобы Job в Laravel увидел это и DONE)
                Redis::setex($redisKey, 86400, $newStatus);

                // Обновляем MySQL (для истории и отображения галочек)
                Messages::where('id', $msgId)
                    ->where('status', '<', $newStatus) // Доп. защита на уровне SQL
                    ->update(['status' => $newStatus]);

                Log::channel('ping_socket')->info("Статус сообщения {$msgId} изменен на {$newStatus}");
            }
        })->afterResponse();

        return response()->json(['status' => $newStatus]);
    }

    public function readStatus(Request $request)
    {
        // прочитано юзером после открытия чата

        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|integer',
            'chat_id' => 'required|integer',
            'status' => 'required|integer|in:2,3',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid data'], 422);
        }
        $newStatus = (int) $request->status;
        $sender_id = $request->sender_id;
        $chat_id = $request->chat_id;
        $ids = $request->message_ids;
        $myId = auth()->id();
        $channels = $request->channels;
        dispatch(function () use ($newStatus, $sender_id, $myId, $ids, $channels, $chat_id) {
            Messages::whereIn('id', $ids)
                ->where('chat_id', $chat_id)
                ->where('sender_id', $sender_id)
                ->update(['status' => $newStatus]);

            event(new Privates([
                'event_type' => 'messages_read',
                'message_ids' => $ids,
                'sender_id' => $sender_id,
                'seller_id' => $myId,
                'reader_id' => $myId,
                'status' => $newStatus,
                'channels' => $channels,
            ]));
        })->afterResponse();

        return response()->json(['status' => $newStatus, 'ids' => $ids]);
    }
}
