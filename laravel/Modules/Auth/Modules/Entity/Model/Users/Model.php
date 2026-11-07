<?php

namespace Modules\Entity\Model\Users;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\database\factories\UserFactory; 

class Model extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'users';
    
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'name',
        'phone',
        'blocked',
        'password',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Переопределяем привязку фабрики.
     * Исправлен синтаксис инициализации фабрики модуля.
     */
    protected static function newFactory(): UserFactory
    {
        return new UserFactory();
    }
}
