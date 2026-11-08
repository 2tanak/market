<?php

namespace App\Services\Slider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Entity\Model\File\Model as FileModel;
use Intervention\Image\Laravel\Facades\Image;

class SliderService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Загрузка временного файла слайдера в подпапку temp/sliders (приватный диск local)
     */
    public function uploadSliderTemp(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = time() . '_' . uniqid('slider_', true);
        $filename = "{$baseName}.{$extension}";

        // Сохраняем оригинал на приватный диск 'local' в скрытую папку
        $path = $file->storeAs('temp/sliders', $filename, 'local');

        return [
            'success'  => true,
            'filename' => $path,
        ];
    }

    /**
     * Основной метод сохранения и синхронизации слайдера
     */
    public function save(Model $model): void
    {
        if ($this->request->has('existing_slider_images') || $this->request->has('slider_images')) {
            $this->syncSavedSliderImages($model);
            $this->moveSliderImages($model);
        }
    }

    /**
     * Удаление картинок, которые юзер убрал из интерфейса
     */
    protected function syncSavedSliderImages(Model $model): void
    {
        $existingIds = array_filter(array_map('intval', $this->request->input('existing_slider_images', [])));


        // Находим старые файлы слайдера модели (кроме главного фото description == 'main')
        $currentSliderImages = $model->files()
            ->where('description', '=', 'slider')
            ->get();

        foreach ($currentSliderImages as $file) {
            if (!in_array($file->id, $existingIds)) {
                $disk = $file->disk ?? 'public';
                $pathsToDelete = array_filter([$file->path, $file->original_path, $file->large_path, $file->medium_path, $file->small_path]);
                foreach (array_unique($pathsToDelete) as $path) {
                    if (Storage::disk($disk)->exists($path)) {
                        Storage::disk($disk)->delete($path);
                    }
                }

                $file->delete();
            }
        }
    }

    /**
     * Перенос из temp/sliders с генерацией адаптивных WebP-холстов (ВАШ ОРИГИНАЛЬНЫЙ КОД)
     */
    protected function moveSliderImages(Model $model): void
    {
        $newImages = $this->request->input('slider_images', []); // Массив путей ["temp/sliders/slider_xxx.jpg"]
        $diskPublic = 'public';
        $diskLocal = 'local';

        // Путь на ПМЖ: uploads/YYYY/MM/slider
        $permanentDir = 'uploads/' . date('Y/m') . '/slider';

        if (!Storage::disk($diskPublic)->exists($permanentDir)) {
            Storage::disk($diskPublic)->makeDirectory($permanentDir);
        }

        $recordsToInsert = [];

        // Конфигурация адаптивных отцентрированных холстов
        $sliderConfigs = [
            'web' => [
                'canvas_w'  => 301,
                'canvas_h'  => 301,
                'max_img_w' => 210,
                'max_img_h' => 150,
                'suffix'    => '_web.webp'
            ],
            'mobile' => [
                'canvas_w'  => 150,
                'canvas_h'  => 150,
                'max_img_w' => 110,
                'max_img_h' => 80,
                'suffix'    => '_mobile.webp'
            ]
        ];

        foreach ($newImages as $index => $tempPath) {
            // Безопасность: отсекаем попытки подмены путей
            $tempFileName = basename($tempPath);
            $actualLocalPath = 'temp/sliders/' . $tempFileName;

            // Если оригинальный черновик физически существует в папке temp/sliders на диске local
            if (Storage::disk($diskLocal)->exists($actualLocalPath)) {

                // Получаем чистое имя без расширения
                $baseName = pathinfo($tempFileName, PATHINFO_FILENAME);

                // Читаем ОРИГИНАЛЬНЫЙ временный файл из диска local ОДИН раз
                $fileBytes = Storage::disk($diskLocal)->get($actualLocalPath);

                // ВАШЕ СТАБИЛЬНОЕ ДЕКОДИРОВАНИЕ V2
                $sourceImage = Image::decode($fileBytes);

                $paths = [];

                // Генерируем адаптивные холсты из одного источника в цикле
                foreach ($sliderConfigs as $key => $config) {
                    $imgCopy = clone $sourceImage;

                    // Получаем текущие оригинальные размеры загруженной картинки
                    $img_Width = $imgCopy->width();
                    $img_Height = $imgCopy->height();

                    // Лимиты ЖЕСТКО равны размерам финального холста (например, 301x301 или любым другим)
                    $maxW = $config['canvas_w'];
                    $maxH = $config['canvas_h'];

                    // ШАГ 1: Если высота изображения больше высоты целевого холста
                    if ($img_Height > $maxH) {
                        $proportion = $img_Height / $maxH;

                        $img_Height = $maxH;
                        // Пересчитываем ширину на основе коэффициента уменьшения высоты
                        $img_Width = (int) round($img_Width / $proportion);

                        // Применяем промежуточный размер к картинке
                        $imgCopy->resize(width: $img_Width, height: $img_Height);
                    }

                    // ШАГ 2: Если после первого шага (или изначально) ширина всё ещё шире холста
                    if ($img_Width > $maxW) {
                        $proportion = $img_Width / $maxW;

                        $img_Width = $maxW;
                        // Пересчитываем высоту на основе коэффициента уменьшения ширины
                        $img_Height = (int) round($img_Height / $proportion);

                        // Применяем финальный размер к картинке
                        $imgCopy->resize(width: $img_Width, height: $img_Height);
                    }

                    // ШАГ 3: Создаем пустой холст строго заданного размера (Вёрстка никогда не поплывёт!)

                    // ШАГ 3: Создаем пустой прозрачный холст строго заданного размера (Синтаксис v4)
                    $canvas = Image::createImage($config['canvas_w'], $config['canvas_h'])->fill('rgba(0,0,0,0)');



                    // ШАГ 4: Точное центрирование картинки на созданном холсте
                    $x = (int) (($config['canvas_w'] - $img_Width) / 2);
                    $y = (int) (($config['canvas_h'] - $img_Height) / 2);

                    // Вставляем картинку по выверенным координатам
                    $canvas->insert($imgCopy, $x, $y);

                    // ШАГ 5: Кодируем в WebP и сохраняем на публичный диск
                    $encoded = $canvas->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 85));
                    $finalFileName = $baseName . $config['suffix'];
                    $permanentPath = $permanentDir . '/' . $finalFileName;
                    Storage::disk($diskPublic)->put($permanentPath, (string) $encoded);

                    $paths[$key] = $permanentPath;
                }


                // 5. Собираем массив для пакетной вставки в таблицу files
                $recordsToInsert[] = [
                    'disk'          => $diskPublic,
                    'original_name' => $baseName,
                    'extension'     => 'webp',
                    'mime_type'     => 'image/webp',
                    'size'          => Storage::disk($diskPublic)->size($paths['web']),
                    'path'          => $paths['web'], // Добавлено для консистентности структуры
                    'original_path' => $paths['web'],

                    // Раскладываем адаптивные отцентрированные холсты по колонкам:
                    'large_path'    => $paths['web'],
                    'medium_path'   => $paths['web'],
                    'small_path'    => $paths['mobile'],

                    'description'   => 'slider',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];

                // 6. Удаляем временный файл из temp/sliders на диске LOCAL
                Storage::disk($diskLocal)->delete($actualLocalPath);
            }
        }

        // 7. Записываем ВСЕ новые файлы в базу данных одним быстрым INSERT запросом
        if (!empty($recordsToInsert)) {
            $model->files()->createMany($recordsToInsert);
        }
    }







    public function destroyImage(FileModel $file): array
    {
        // Защита: удаляем только файлы слайдера
        if ($file->description !== 'slider') {
            return ['success' => false, 'error' => 'Доступ запрещен: этот файл не является картинкой слайдера'];
        }

        $disk = $file->disk ?? 'public';

        // Собираем все адаптивные холсты из колонок твоей модели
        $pathsToDelete = array_filter([
            $file->path, // Если используется основная колонка
            $file->original_path,
            $file->large_path,
            $file->medium_path,
            $file->small_path
        ]);

        // Физически стираем файлы с жесткого диска
        foreach (array_unique($pathsToDelete) as $path) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }

        // Удаляем строку из таблицы `files`
        $file->delete();

        return ['success' => true];
    }
}
