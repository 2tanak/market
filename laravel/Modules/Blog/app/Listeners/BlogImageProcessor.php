<?php

namespace Modules\Blog\Listeners;

use Modules\Blog\Events\ArticleSaving;
use App\Services\Editor\EditorImage; // Наш готовый фасад

class BlogImageProcessor
{
    public function handle(ArticleSaving $event): void
    {
        $model = $event->model;

        // Цепочка 1: Обработка и нарезка картинок текстового редактора
        if ($model->isDirty('description') && !empty($model->description)) {
            // Нарезаем картинки v4 и сразу подменяем текст в объекте модели
            $model->description = EditorImage::moveEditorImages($model, $model->description);
        }

        // Цепочка 2: Сюда в будущем допишешь всё, что должно произойти до сохранения (например, Slug)
    }
}
