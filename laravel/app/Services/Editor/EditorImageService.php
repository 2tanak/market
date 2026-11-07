<?php

namespace App\Services\Editor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class EditorImageService
{
    /**
     * ВРЕМЕННОЕ сохранение картинки, перетащенной в CKEditor.
     * Нарезка произойдет позже — в экшене при нажатии кнопки "Сохранить".
     */
    public function uploadForEditor(UploadedFile $file): string
    {
        $disk = 'public';
        // ИСПРАВЛЕНО: Картинка-черновик летит строго в изолированную временную папку
        $dir = 'uploads/temp/editor';

        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = time() . '_' . uniqid();
        $fileName = "{$baseName}.{$extension}";

        // Сохраняем исходный файл на диск public
        $path = $file->storeAs($dir, $fileName, ['disk' => $disk]);

        // Возвращаем полный публичный URL для браузера (его CKEditor подставит в тег src)
        return asset(Storage::disk($disk)->url($path));
    }

    /**
     * Перенос картинок из temp/editor на ПМЖ с нарезкой на 3 адаптивных размера (v4)
     */
    public function moveEditorImages(Model $model, string $content): string
    {
        $disk = 'public';
        $permanentDir = 'uploads/' . date('Y/m') . '/editor';

        if (!Storage::disk($disk)->exists($permanentDir)) {
            Storage::disk($disk)->makeDirectory($permanentDir);
        }

        // 1. ИСПРАВЛЕНО: Заэкранированы слеши папки /temp/editor/ для корректной работы регулярки PHP
        preg_match_all('/src="[^"]+?\/storage\/uploads\/temp\/editor\/([^"]+?)"/', $content, $matches);

        $recordsToInsert = [];

        if (!empty($matches[1])) {
            // Конфигурация размеров (large идет без суффикса, а medium и small с суффиксами)
            $sizesConfig = [
                'large'  => ['width' => 1200, 'suffix' => ''],
                'medium' => ['width' => 768,  'suffix' => '_medium'],
                'small'  => ['width' => 300,  'suffix' => '_small'],
            ];

            foreach ($matches[1] as $fileName) {
                // ИСПРАВЛЕНО: Сверяем путь внутри новой изолированной папки editor
                $oldPath = 'uploads/temp/editor/' . $fileName;

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
                        'description'   => 'editor', // Четкая метка, что картинка из текста статьи
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    // Нарезаем и динамически пишем ключи путей через твой оригинальный Image v4 кодинг
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

                    // ИСПРАВЛЕНО: Меняем временную ссылку /temp/editor/ на чистый оригинальный путь на постоянке
                    $oldUrl = '/storage/uploads/temp/editor/' . $fileName;
                    $newUrl = '/storage/' . $imageRecord['large_path'];
                    $content = str_replace($oldUrl, $newUrl, $content);

                    Storage::disk($disk)->delete($oldPath);
                }
            }

            if (!empty($recordsToInsert) && method_exists($model, 'files')) {
                $model->files()->createMany($recordsToInsert);
            }
        }

        $oldDescription = $model->getOriginal('description');

        // Удаление с диска и базы удаленных из текста редактора файлов
        if ($model->exists && !empty($oldDescription)) {
            preg_match_all('/storage\/uploads\/\d{4}\/\d{2}\/editor\/([a-z0-9_]+)\.([a-z]{3,4})/i', $oldDescription, $oldMatches);

            if (!empty($oldMatches[1])) {
                foreach ($oldMatches[1] as $index => $baseName) {

                    // Если уникального имени файла больше нет в новом тексте — удаляем всю пачку с диска
                    if (!str_contains($content, $baseName)) {
                        $mainPath = str_replace('storage/', '', $oldMatches[0][$index]);
                        $mediumPath = str_replace($baseName, $baseName . '_medium', $mainPath);
                        $smallPath  = str_replace($baseName, $baseName . '_small', $mainPath);

                        Storage::disk($disk)->delete([$mainPath, $mediumPath, $smallPath]);

                        if (method_exists($model, 'files')) {
                            $model->files()
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
