<?php
namespace App\Services\Avatar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\UploadPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;


class AvatarService
{
    protected $request;

    // Внедряем текущий HTTP-запрос через Container
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Основной метод сохранения аватара
     */
    public function save(Model $model, string $inputName = 'image')
    {
        // Проверяем, прикрепил ли админ файл в указанный инпут
        if ($this->request->hasFile($inputName)) {
            $file = $this->request->file($inputName);

            if ($file->isValid() && method_exists($model, 'files')) {
                
                // 1. Удаляем только старую аватарку со значением 'main'
                $this->deleteOldAvatar($model);

                // 2. Вызываем базовый сервис загрузки
                //$uploadService = new UploadPhoto();
                $this->upload($file, $model);
                // Внутри upload в поле description запишется 'main'
                //$uploadService->upload($file, $model);
                
                // 3. Обновляем кэш связей модели со свежей записью
                $model->load('files');
            }
        }
    }

    /**
     * Изолированное удаление старого аватара
     */
    protected function deleteOldAvatar(Model $model)
    {
        $mainFileQuery = $model->files()->where('description', 'main');

        if ($mainFileQuery->exists()) {
            foreach ($mainFileQuery->get() as $oldFile) {
                if ($oldFile->original_path && $oldFile->disk) {
                    Storage::disk($oldFile->disk)->delete($oldFile->original_path);
                }
                $oldFile->delete();
            }
        }
    }
	
	public function upload(UploadedFile $file, Model $model)
    {
		
        $disk = 'public';
        $dir = 'uploads/' . date('Y/m') . '/avatar';

        if (!Storage::disk($disk)->exists($dir)) {
            Storage::disk($disk)->makeDirectory($dir);
        }

  
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();

        // Генерируем одно уникальное имя для нашего финального кропа
        $baseName = time() . '_' . uniqid();
        $fileName = "{$baseName}.{$extension}";

        // 1. Просто сохраняем пришедший кропнутый файл на диск
        $path = $file->storeAs($dir, $fileName, ['disk' => $disk]);

        // 2. Удаляем старые файлы этой модели с диска и из БД
        //$this->deleteOldFiles($model);

        // 3. Записываем информацию в таблицу (все пути теперь ведут на один файл)
        $model->files()->create([
            'disk'          => $disk,
            'original_name' => $originalName,
            'extension'     => $extension,
            'mime_type'     => $mimeType,
            'size'          => $size,
            'original_path' => $path,
            'large_path'    => $path, // Нарезки нет, пишем один и тот же путь,
            'medium_path'   => $path, // чтобы не ломать старую структуру в шаблонах
            'small_path'    => $path,
            'description'   => 'main',
        ]);
    }
	
	
	
	
}
