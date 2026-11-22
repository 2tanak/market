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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            
            // ПЕРЕИМЕНОВАНО: Теперь ссылаемся на нашу единую таблицу объявлений listings
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            
            // Участники диалога (ссылаются на стандартную таблицу users)
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            
            // timestamps создаст поля created_at и updated_at.
            $table->timestamps(); 

            // ИНДЕКСЫ ДЛЯ СКОРОСТИ (Оптимизировано под PostgreSQL)
            $table->index('buyer_id');
            $table->index('seller_id');
            
            // Критически важно для OLX: индекс на updated_at, 
            // чтобы моментально поднимать активные чаты вверх списка
            $table->index('updated_at');
            
            // Уникальный индекс: защита от дубликатов комнат (Объявление + Покупатель + Продавец)
            $table->unique(['listing_id', 'buyer_id', 'seller_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
