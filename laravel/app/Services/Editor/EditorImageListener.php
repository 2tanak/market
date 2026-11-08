<?php

namespace App\Services\Editor;
use Illuminate\Http\Request;
use App\Services\Editor\EditorImage;


class EditorImageListener {
	
	 protected Request $request;
	 
	  public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($event) {
        $model = $event->model;
        if ($model->isDirty('description') && !empty($model->description)) {
            $model->description = EditorImage::moveEditorImages($model, $model->description);
        }
    }
}
