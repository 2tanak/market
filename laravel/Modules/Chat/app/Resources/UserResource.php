<?php

namespace Modules\Chat\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        // Превращаем строку даты из БД в объект Carbon для форматирования
        $lastMsgAt = $this->last_message_text ? Carbon::parse($this->last_message_text_at) : null;

        return [
            'id'           => $this->id, // ID собеседника (партнера по чату)
            'name'         => $this->name,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'online'       => 'offline', // Для твоих редюсеров реалтайма
            'typing'       => false,     // Для твоих редюсеров реалтайма
            
            // Берем счетчик прямо из того, что посчитала база
            'unread_count' => (int) ($this->unread_count_custom ?? 0),

            // Собираем объект сообщения из виртуальных полей, которые мы прокинем в контроллере
            'last_message' => $this->last_message_text ? [
                'id'          => $this->last_message_id,
                'status'      => $this->last_message_status, // Твой статус (1, 2, 3)
                'last_msg_at' => $this->last_message_text_at,
                'text'        => $this->last_message_text,
                'time'        => $lastMsgAt ? $lastMsgAt->format('H:i') : null,
                'date'        => $lastMsgAt ? $lastMsgAt->format('d.m.Y') : null,
                'timestamp'   => $lastMsgAt ? $lastMsgAt->timestamp : null,
            ] : null,
                
            'created_at'   => $this->created_at ? $this->created_at->format('d.m.Y') : now()->format('d.m.Y'),
            
            // Бонус: отдаем chat_id на самый верх, чтобы фронтенд знал номер комнаты при клике на карточку!
            'chat_id'   => $this->current_chat_id,
			//'product_id' => $this->product_id,
			
        ];
    }
}
