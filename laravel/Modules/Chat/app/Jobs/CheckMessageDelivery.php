<?php

namespace Modules\Chat\Jobs; // Путь должен совпадать с папками

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Modules\Chat\Events\Privates;
use Modules\Chat\Model\Messages\Model as Messages;
use Modules\Chat\Resources\MessageResource;

class CheckMessageDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;

    protected $messageId;

    public function __construct($messageId)
    {
        $this->messageId = $messageId;
    }

    public function handle()
    {
        $redisKey = "msg_status:{$this->messageId}";
        $status = Redis::get($redisKey);

        // 1. Если клиент подтвердил получение (статус 2)
        if ($status == 2 || $status == 3) {
            Redis::del($redisKey);

            // Вот теперь действительно DONE
            return;
        }

        $message = Messages::with(['sender', 'chat'])->find($this->messageId);
        if (! $message) {
            Redis::del($redisKey);

            return;
        }

        $senderId = $message->sender_id;
        $tokenKey = "user:{$senderId}:token";

        // Проверяем, есть ли токен в Редисе для Node.js
        if (! Redis::exists($tokenKey) && $message->sender) {
            $sender = $message->sender; // Берем самого юзера через связь
            $sender->tokens()->where('name', 'web-access')->delete();
            $newToken = $sender->createToken('web-access')->plainTextToken;
            Redis::setex($tokenKey, 86400, $newToken);
            dump("Токен для юзера {$senderId} пересоздан прямо в Job");
        }

        $receiverId = $message->chat
            ? ((int) $message->chat->buyer_id === (int) $message->sender_id
                ? $message->chat->seller_id
                : $message->chat->buyer_id)
            : null;

        // Формируем ресурс (как в контроллере) и пушим событие с seller_id получателя
        $resource = (new MessageResource($message))->additional([
            'seller_id' => $receiverId,
            'unread_count' => 1,
        ]);

        // Это снова кинет данные в Redis для Node.js
        event(new Privates($resource->resolve()));

        dump("Повторный выстрел сокета для ID: {$this->messageId}");

        // 2. Если статус всё еще 1 (сообщение не дошло) — откладываем повторную проверку
        // $this->sendPushNotification();
        $this->release(now()->addSeconds(30));
    }

    public function failed(\Throwable $exception)
    {
        // Это сообщение появится в консоли, где запущен php artisan queue:work
        dump("Задача UUID {$this->messageId} провалена: ".$exception->getMessage());
    }

    protected function sendPushNotification()
    {
        // Твой код для Firebase...
    }

    public function retryUntil()
    {
        // Пытаться доставить в течение 3 дней с момента создания
        return now()->addDays(3);
    }
}
