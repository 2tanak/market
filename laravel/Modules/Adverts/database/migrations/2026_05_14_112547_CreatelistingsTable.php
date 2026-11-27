<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Индекс на категорию обязателен
            $table->foreignId('ads_category_id')->index()->constrained('ads_categories')->onDelete('cascade');

            $table->string('title');
            $table->text('description');

            // Индекс на цену, чтобы моментально работал ползунок фильтра "Цена от и до"
            $table->unsignedInteger('price')->index();

            // Индекс на статус, чтобы отсекать проданные или забаненные товары
            $table->string('status')->default('active')->index();

            $table->json('properties')->nullable();
            $table->timestamps();

            // ВАЖНО: Правильный чистый SQL-синтаксис для MySQL 8.0 внутри virtualAs()

            // 1. Количество комнат (Число, вернет NULL если ключа нет)
            $table->integer('search_rooms')
                ->virtualAs('properties->"$.rooms"')
                ->nullable();

            // 2. Пробег/год (Число, вернет NULL если ключа нет)
            $table->integer('search_year')
                ->virtualAs('properties->"$.year"')
                ->nullable();

            // 3. Объем памяти iPhone (Число, вернет NULL если ключа нет)
            $table->integer('search_storage')
                ->virtualAs('properties->"$.storage"')
                ->nullable();

            // 4. Модель/линейка (Используем ДВОЙНУЮ стрелку ->> для удаления кавычек из строки)
            $table->string('search_model', 100)
                ->virtualAs('properties->>"$.model"')
                ->nullable();

            // Навешиваем индексы на созданные виртуальные колонки
            $table->index('search_rooms', 'listings_rooms_index');
            $table->index('search_year', 'listings_year_index');
            $table->index('search_storage', 'listings_storage_index');
            $table->index('search_model', 'listings_model_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
