<?php

namespace App\Services\Avatar;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void save(\Illuminate\Database\Eloquent\Model $model, string $inputName = 'image')
 * 
 * @see \App\Services\Avatar\AvatarService
 */
class Avatar extends Facade
{
    /**
     * Получить зарегистрированное имя компонента в контейнере.
     */
    protected static function getFacadeAccessor(): string
    {
        // Строго совпадает со строкой синглтона в вашем AvatarServiceProvider
        return 'avatar.service'; 
    }
}

