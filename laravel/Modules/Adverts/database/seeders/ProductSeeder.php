<?php

namespace Modules\Adverts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Укажи путь к твоему SQL файлу.
        // Совет: положи messages.sql в Modules/Chat/Database/Seeders/sql/messages.sql
        $path = base_path('Modules/Adverts/database/bd/product.sql');

        if (! File::exists($path)) {
            $this->command->error("Файл базы не найден по пути: {$path}");

            return;
        }

        // 2. Читаем содержимое файла
        $sql = File::get($path);

        // 3. Выполняем сырой SQL запрос
        try {
            DB::unprepared($sql);
            $this->command->info('База сообщений успешно импортирована');
        } catch (\Exception $e) {
            $this->command->error('Ошибка при импорте: '.$e->getMessage());
        }
    }
}
