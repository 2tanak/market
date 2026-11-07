<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use App\Services\Editor\EditorImage;
use Illuminate\Http\Request;

Route::group(['middleware' => ['auth.admin']], function () {
	Route::prefix('admin')->group(function () {
		Route::get('/', [AdminController::class,'index'])->name('admin_index');
		
		Route::post('/editor-image-temp-upload', function (Request $request) {
            // 1. Валидируем прилетевший файл черновика
            $validator = Validator::make($request->all(), [
                'upload' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,webp',
                    'min:5',    // Минимальный вес: 5 КБ
                    'max:5120',   // Максимальный вес: 5 МБ
                ]
            ]);

            // 2. Если файл не прошел проверку — возвращаем ошибку в JSON
            // CKEditor перехватит её и красиво покажет предупреждение в интерфейсе
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($request->file('upload')->isValid()) {
                $url = EditorImage::uploadForEditor($request->file('upload'));
                return response()->json(['url' => $url]);
            }

            return response()->json(['error' => 'Файл не валиден'], 400);
        })->name('admin.temp.image.upload');
		
	});
});
