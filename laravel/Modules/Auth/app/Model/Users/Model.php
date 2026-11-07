<?php

namespace Modules\Auth\Model\Users;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\database\factories\UserFactory; 
use Modules\Entity\Contracts\UniversalModelInterface;
use Modules\Entity\Traits\UniversalLogic;
use Modules\Entity\Traits\FilterModel;
use Illuminate\Support\Facades\Hash;

class Model extends Authenticatable implements UniversalModelInterface
{
    use UniversalLogic, FilterModel,HasFactory, Notifiable;
    
    protected $table = 'users';
	protected $appends = ['input_name','input_title'];
	
    protected function filterClass() {
       return Filter::class;
    }
	
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
	
	
	 public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
	
	
	
}
