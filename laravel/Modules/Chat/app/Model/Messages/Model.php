<?php

namespace Modules\Chat\Model\Messages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Model\Users\Model as User;
use Modules\Chat\Database\Factories\MessageFactory;
use Modules\Chat\Model\Chats\Model as Chat;
use Modules\Entity\Model\Translation\Model as Translation;
use Modules\Entity\ModelParent;

class Model extends ModelParent
{
    use HasFactory;

    protected $filter_class = Filter::class;

    protected $table = 'messages';

    protected $fillable = [
        'uuid',
        'sender_id',
        // 'receiver_id',
        'email',
        'text',
        'status',
        'chat_id',
        'created_at',
        'updated_at',
        'is_read',
    ];

    // Сообщение принадлежит конкретной комнате
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    // Автор сообщения
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Переопределяем фабрику для работы внутри модуля
     */
    protected static function newFactory()
    {
        return MessageFactory::new();
    }

    // Здесь уже работают твои FilterModel и UniversalLogic из родителя
    public function relTrans()
    {
        return $this->morphMany(Translation::class, 'transable');
    }

    // Связь с получателем (User)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
