<?php

namespace App\Services\Editor;

class EditorImageListener {
    public function handle($event) {
        $model = $event->model;
        if ($model->isDirty('description') && !empty($model->description)) {
            $model->description = EditorImage::moveEditorImages($model, $model->description);
        }
    }
}
