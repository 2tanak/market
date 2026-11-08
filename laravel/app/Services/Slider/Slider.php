<?php

namespace App\Services\Slider;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void save(\Illuminate\Database\Eloquent\Model $model)
 * @method static array uploadSliderTemp(\Illuminate\Http\UploadedFile $file)
 * @method static array destroyImage(\Modules\Entity\Model\File\Model $file) // <-- НАПИШИ ЭТУ СТРОКУ ТУТ
 * 
 * @see \App\Services\Slider\SliderService
 */
class Slider extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'slider.service';
    }
}
