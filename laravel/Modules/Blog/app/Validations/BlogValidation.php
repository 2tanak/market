<?php

namespace Modules\Blog\Validations;

use Modules\Admin\Validations\ValidationInterface;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // ОБЯЗАТЕЛЬНО импортируем Storage

class BlogValidation implements ValidationInterface 
{
    public function rules(array $data, $id = null): array 
    {
        $rules = [
            'title'        => ['required', 'string', 'max:255'],
            'publish'      => ['required', 'boolean'],
             // ВАЛИДАЦИЯ ТЕКСТА РЕДАКТОРА
            'description'  => [
                'required', 
                'string',
                'min:10', // Статья не должна быть слишком короткой
                
                // КАСТОМНАЯ БЕЗОПАСНОСТЬ: Дополнительная инлайн-защита (Опционально)
                // Проверяем, что если в тексте есть картинки, они ведут строго на наш домен или temp
                function ($attribute, $value, $fail) {
                    if (str_contains($value, '<script')) {
                        $fail('The text contains prohibited HTML tags (<script>).');
                    }
                }
            ],
            
            // Главное фото (Валидируем как обычный UploadedFile)
            'image'        => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'min:5',
                'max:5120', // Максимум 5 МБ
            ],

            // Оставшиеся картинки
            'existing_slider_images'   => ['nullable', 'array'],
            'existing_slider_images.*' => ['required', 'integer', 'exists:files,id'],

            // Новые картинки слайдера (Валидируем текстовые пути из папки temp)
            'slider_images'   => ['nullable', 'array'],
            'slider_images.*' => [
                'required', 
                'string', 
                'regex:/^temp\/sliders\/.+$/',
                
               
                function ($attribute, $value, $fail) {
                    // $value — это путь к файлу, например: "temp/sliders/slider_672b1a.webp"
                    if (Storage::disk('local')->exists($value)) {
                        // Получаем размер в байтах и переводим в Мегабайты
                        $fileSizeInMb = Storage::disk('local')->size($value) / 1024 / 1024;
                        
                        // Если файл больше 5 МБ — бракуем всю форму
                        if ($fileSizeInMb > 5) {
                            $fail('The slider image exceeds the maximum allowed size of 5 MB.');
                        }
                    }
                }
            ],
        ];

        return $rules;
    }

    public function messages(): array 
    {
        return [
            'title.required'        => 'The field must be filled in',
            'image.max'             => 'The main image must not be greater than 5 MB',
            'slider_images.*.regex' => 'Invalid temp file path detector',
        ];
    }
}
