<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use App\Services\Slider\Slider;
use Illuminate\Http\Request;
use Modules\Entity\Model\File\Model as FileModel;

Route::group(['middleware' => ['auth.admin']], function () {
	Route::prefix('admin')->group(function () {
		Route::get('/', [AdminController::class,'index'])->name('admin_index');
		
		  //slider
        Route::post('/slider/image-upload', function (Request $request) {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Вызываем метод НАПРЯМУЮ через фасад слайдера
                $result = Slider::uploadSliderTemp($request->file('file'));

                return response()->json($result);
            }
            return response()->json(['error' => 'Файл не валиден'], 400);
        })->name('admin.slider.image.upload');
        //удаление загруженной картинки слайдера по крестику
        Route::delete('/slider/image-delete/{file}', function (FileModel $file) {
            // Передаем готовый объект модели прямо в сервис
            $result = Slider::destroyImage($file);

            return response()->json($result, $result['success'] ? 200 : 400);
        })->name('admin.slider.image.delete');
	});
});
