<?php

namespace App\Services\Avatar;

use Illuminate\Http\Request;
use App\Services\Avatar\Avatar; // Импортируем фасад аватара

class AvatarImageListener 
{
    protected Request $request;

    // Внедряем текущий HTTP-запрос, чтобы видеть прилетевшие файлы формы
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($event): void
    {
        $model = $event->model;
		
        // Проверяем: прикрепил ли админ в форме новый валидный файл аватара?
        if ($this->request->hasFile('image') && $this->request->file('image')->isValid()) {
            
            // Вызываем твой готовый сервис аватара, который нарезает фото и обновляет модель
            Avatar::save($model);
        }
    }
}
