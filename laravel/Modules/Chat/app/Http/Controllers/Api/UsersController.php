<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Chat\Model\Chats\Model as Chat;
use Modules\Chat\Resources\UserResource;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $myId = auth()->id();
        // $myId = 1;
        $page = $request->page;
        // $page = 1;

        $chats = Chat::with([
            'buyer',
            'seller',
            'messages' => function ($query) {
                $query->latest('id'); // Превью последнего сообщения
            },
        ])->paginate(20);

        return response()->json(['data' => $chats]);

        $chats = Chat::with([
            'buyer',
            'seller',
            'messages' => function ($query) {
                $query->latest('id'); // Превью последнего сообщения
            },
        ])
            ->where(function ($query) use ($myId) {
                $query->where('buyer_id', $myId)
                    ->orWhere('seller_id', $myId);
            })
            // 1. СОЗДАЕМ КОЛОНКУ ТУТ (через чистый SQL подзапрос)
            ->selectRaw(
                'chats.*,
                (
                    SELECT COUNT(*) FROM messages m
                    WHERE m.chat_id = chats.id
                      AND m.sender_id != ?
                      AND m.status = 2
                ) as unread_count,
                (
                    SELECT MAX(created_at) FROM messages m
                    WHERE m.chat_id = chats.id
                ) as last_msg_at',
                [$myId]
            )
            ->orderByRaw('CASE WHEN unread_count > 0 THEN 1 ELSE 0 END DESC')
            ->orderByRaw('CASE WHEN last_msg_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('last_msg_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        // 2. Трансформируем (маппим) данные в плоскую структуру под твой старый Redux
        $usersCollection = collect($chats->items())->map(function ($chat) use ($myId) {
            // Определяем, кто твой собеседник в этой комнате
            $partner = $chat->buyer_id === $myId ? $chat->seller : $chat->buyer;

            // Если собеседника почему-то нет в БД, пропускаем (защита от битых данных)
            if (!$partner) {
                return null;
            }

            // Вытаскиваем последнее сообщение, подгруженное через Eloquent связь
            $lastMessage = $chat->messages->first();

            // product_id
            $partner->product_id = $chat->product_id;
            // unread_count
            $partner->unread_count_custom = $chat->unread_count;
            // chat_id
            $partner->current_chat_id = $chat->id;
            // last_message_id
            $partner->last_message_id = $lastMessage ? $lastMessage->id : null;
            // last_message_text
            $partner->last_message_text = $lastMessage ? $lastMessage->text : null;
            // last_message_status
            $partner->last_message_status = $lastMessage ? $lastMessage->status : null;
            // last_message_created_at
            $partner->last_message_text_at = $lastMessage ? $lastMessage->created_at->toDateTimeString() : null;

            return $partner;
        })->filter();

        return response()->json([
            'data' => UserResource::collection($usersCollection),
            'pagination' => [
                'current_page' => $chats->currentPage(),
                'has_more' => $chats->hasMorePages(),
                'page' => (int) $page,
            ],
        ]);
    }

    public function totalUnread()
    {
        $myId = auth()->id();

        // Считаем все сообщения, где получатель Я и статус 2 (не прочитано)
        $count = Model::where('receiver_id', $myId)
            ->where('status', 2)
            ->count();

        return response()->json([
            'total_unread' => $count,
        ]);
    }
}

