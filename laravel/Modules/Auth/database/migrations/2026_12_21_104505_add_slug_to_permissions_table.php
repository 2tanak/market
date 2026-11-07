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
        // Получаем имя таблицы из конфигурации Spatie (по умолчанию 'permissions')
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            // Добавляем поле slug после поля name. Делаем его nullable, 
            // чтобы не сломать логику, если в базе уже есть старые записи.
            $table->string('slug')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            // Удаляем поле, если потребуется откат миграции
            $table->dropColumn('slug');
        });
    }
};
