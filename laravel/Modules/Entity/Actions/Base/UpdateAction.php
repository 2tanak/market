<?php

namespace Modules\Entity\Actions\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\UploadPhoto;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;
use App\Services\Avatar\Avatar; 
use App\Services\Slider\Slider;
use App\Services\Editor\EditorImage;

use Route;
use Cache;
use Illuminate\Support\Facades\Storage;

class UpdateAction
{
    protected $model;
    protected $validator;
    protected $request;

    // Убираем Request из конструктора
    function __construct(Model $model, Request $request, $validator)
    {

        $this->model = $model;
        $this->validator = $validator;

        $this->request = $request;
    }


    function run()
    {
        DB::transaction(function () {
			//dd($this->request->all());
            $validated = $this->validator->validated();

            if (isset($validated['description'])) {
                $validated['description'] = EditorImage::moveEditorImages($this->model, $validated['description']);

            }

            if (app()->getLocale() && app()->getLocale() != env('APP_LOCALE')) {

                //dd($this->model->relTrans());
                $this->model->relTrans()->updateOrCreate(
                    [
                        'lang' => $this->request->lang,
                        'transable_id' => $this->model->id
                    ],
                    $this->validator->validated()
                );
            } else {

                $this->saveMain($validated);
            }
            
			if ($this->request->hasFile('image') && $this->request->file('image')->isValid()) {
                Avatar::save($this->model);
            }
			
			if ($this->request->has('existing_slider_images') || $this->request->has('slider_images')) {
                Slider::save($this->model);
            }
			
			
		});
				
}
  
    private function saveMain(array $data)
    {
        $this->model->fill($data);
        $this->model->save();
    }


    


    private function moveEditorImages(string $content): string
    {
        $disk = 'public';
        $permanentDir = 'uploads/' . date('Y/m') . '/editor';

        if (!Storage::disk($disk)->exists($permanentDir)) {
            Storage::disk($disk)->makeDirectory($permanentDir);
        }

        // 1. Ищем новые картинки во временной папке temp
        preg_match_all('/src="[^"]+?\/storage\/uploads\/temp\/([^"]+?)"/', $content, $matches);

        $recordsToInsert = [];

        if (!empty($matches[1])) {
            // Конфигурация размеров (large идет без суффикса, а medium и small с суффиксами)
            $sizesConfig = [
                'large'  => ['width' => 1200, 'suffix' => ''],
                'medium' => ['width' => 768,  'suffix' => '_medium'],
                'small'  => ['width' => 300,  'suffix' => '_small'],
            ];

            foreach ($matches[1] as $fileName) {
                $oldPath = 'uploads/temp/' . $fileName;

                if (Storage::disk($disk)->exists($oldPath)) {
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $originalName = pathinfo($fileName, PATHINFO_FILENAME);

                    $mimeType = Storage::disk($disk)->mimeType($oldPath);
                    $size = Storage::disk($disk)->size($oldPath);
                    $fileBytes = Storage::disk($disk)->get($oldPath);

                    // Заготовка строки для таблицы files
                    $imageRecord = [
                        'disk'          => $disk,
                        'original_name' => $originalName,
                        'extension'     => $extension,
                        'mime_type'     => $mimeType,
                        'size'          => $size,
                        'description'   => $this->model->title ?? $this->model->name ?? null,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    // Нарезаем и динамически пишем ключи путей
                    foreach ($sizesConfig as $key => $config) {
                        $image = Image::decode($fileBytes);

                        if ($image->width() > $config['width']) {
                            $image->scale(width: $config['width']);
                        }

                        $encoded = $image->encodeUsingFileExtension($extension, quality: 85);

                        // Формируем имя: для large — чистое имя, для остальных — с суффиксом
                        $resizedFileName = "{$originalName}{$config['suffix']}.{$extension}";
                        $relativePath = "{$permanentDir}/{$resizedFileName}";

                        Storage::disk($disk)->put($relativePath, $encoded->toString());

                        // Пишем в large_path, medium_path, small_path
                        $imageRecord[$key . '_path'] = $relativePath;
                    }

                    // В оригинальный путь пишем ссылку на основной файл (без суффикса)
                    $imageRecord['original_path'] = $imageRecord['large_path'];
                    $recordsToInsert[] = $imageRecord;

                    // ПОДМЕНА ССЫЛКИ: Меняем временную ссылку на чистый оригинальный путь на постоянке
                    $oldUrl = '/storage/uploads/temp/' . $fileName;
                    $newUrl = '/storage/' . $imageRecord['large_path'];
                    $content = str_replace($oldUrl, $newUrl, $content);

                    Storage::disk($disk)->delete($oldPath);
                }
            }

            if (!empty($recordsToInsert) && method_exists($this->model, 'files')) {
                $this->model->files()->createMany($recordsToInsert);
            }
        }


        $oldDescription = $this->model->getOriginal('description');

        //удаление с диска и базы удаленных файлов
        if ($this->model->exists && !empty($oldDescription)) {

            preg_match_all('/storage\/uploads\/\d{4}\/\d{2}\/editor\/([a-z0-9_]+)\.([a-z]{3,4})/i', $oldDescription, $oldMatches);

            //return $content;
            if (!empty($oldMatches[1])) {
                foreach ($oldMatches[1] as $index => $baseName) {

                    // Если уникального имени файла больше нет в новом тексте — удаляем всю пачку с диска
                    if (!str_contains($content, $baseName)) {
                        $mainPath = str_replace('storage/', '', $oldMatches[0][$index]);

                        $mediumPath = str_replace($baseName, $baseName . '_medium', $mainPath);

                        $smallPath  = str_replace($baseName, $baseName . '_small', $mainPath);

                        Storage::disk($disk)->delete([$mainPath, $mediumPath, $smallPath]);


                        if (method_exists($this->model, 'files')) {
                            $this->model->files()
                                ->where('original_name', $baseName)
                                ->delete();
                        }
                    }
                }
            }
        }

        return $content;
    }
}
