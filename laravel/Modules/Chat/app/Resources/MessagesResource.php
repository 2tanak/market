<?php

namespace Modules\Chat\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessagesResource extends JsonResource
{
    public function toArray($request)
    {
        if (auth()->user()?->email) {
            $email = auth()->user()->email;
        } else {
            $email = 'test@mail.ru';
        }

        return [
            'id' => $this->id,
            'channels' => 'action',
            'server_id' => $this->id,
            'email' => $email,
            'uuid' => $this->uuid,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'text' => $this->text,
            'chat_id' => $this->chat_id,
            'last_message' => $this->text ? [
                'text' => $this->text,
                // Убрал лишние двоеточия после методов format()
                'time' => $this->created_at ? $this->created_at->format('H:i') : null,
                'date' => $this->created_at ? $this->created_at->format('d.m.Y') : null,
                'timestamp' => $this->created_at ? $this->created_at->timestamp : null,
            ] : null,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
