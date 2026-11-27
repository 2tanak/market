<?php

namespace Modules\Chat\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request)
    {
        // Твоя каноничная проверка имейла
        if (auth()->user()?->email) {
            $email = auth()->user()->email;
        } else {
            $email = 'test@mail.ru';
        }

        return [
            'id' => $this->id,
            'server_id' => $this->id, // Твой ключ для синхронизации ID
            'channels' => 'action',
            'email' => $email,
            'uuid' => $this->uuid,
            'chat_id' => $this->chat_id, // Добавили вместо receiver_id
            'sender_id' => $this->sender_id,
            'text' => $this->text,
            'status' => (int) $this->status, // Твой статус сообщения (1, 2, 3)
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,

            // Твой оригинальный объект превью сообщения, который ждет фронтенд
            'last_message' => $this->text ? [
                'id' => $this->id,
                'text' => $this->text,
                'last_msg_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
                'status' => (int) $this->status,
                'time' => $this->created_at ? $this->created_at->format('H:i') : null,
                'date' => $this->created_at ? $this->created_at->format('d.m.Y') : null,
                'timestamp' => $this->created_at ? $this->created_at->timestamp : null,
            ] : null,

            'sender' => $this->whenLoaded('sender'),
            'seller_id' => $this->additional['seller_id'] ?? null,
        ];
    }
}
