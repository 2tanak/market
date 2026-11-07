<?php

namespace App\Services\Editor;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string uploadForEditor(\Illuminate\Http\UploadedFile $file)
 * @method static string moveEditorImages(\Illuminate\Database\Eloquent\Model $model, string $content)
 * 
 * @see \App\Services\Editor\EditorImageService
 */
class EditorImage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'editor.image.service';
    }
}
