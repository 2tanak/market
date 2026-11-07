<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ArticleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Строим абсолютный путь от корня проекта с учетом разделителей Windows/Linux
        $path = base_path('Modules' . DIRECTORY_SEPARATOR . 'Blog' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'bd' . DIRECTORY_SEPARATOR . 'article_category.sql');

        if (!File::exists($path)) {
            $this->command->error("Файл дампа не найден по пути: {$path}");
            return;
        }

        $this->command->info('Импорт SQL-дампа из модуля Blog...');

        // Отключаем проверку внешних ключей на время импорта (защита от ошибок связей)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Выполняем SQL-дамп напрямую
        DB::unprepared(File::get($path));

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Импорт успешно завершен!');
    }
}
