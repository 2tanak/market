<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            
            // Информация о файле
            $table->string('disk', 50)->default('public'); // Ограничили длину, задали дефолт
            $table->string('original_name')->nullable();   // Переименовали 'name' в понятное 'original_name'
            $table->string('extension', 10)->nullable();   // Для расширений (.jpg, .png) 10 символов за глаза
            $table->string('mime_type', 100)->nullable();  // Ограничили до 100 (обычно image/jpeg)
            $table->unsignedBigInteger('size')->nullable(); // Для размера файлов используем строго unsignedBigInteger
            
            
            // Храним в базе только относительный путь внутри папки (например: "uploads/2026/08/filename_large.webp")
            $table->string('original_path')->nullable(); // Путь к исходному файлу
            $table->string('large_path')->nullable();    // Понятные названия вместо просто large/medium/small
            $table->string('medium_path')->nullable();
            $table->string('small_path')->nullable();
            
            // Метаданные
            $table->string('description')->nullable(); // 
            
            $table->morphs('fileable'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
