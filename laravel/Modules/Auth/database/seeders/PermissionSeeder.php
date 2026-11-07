<?php
namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Model\Permission\Model as Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Сбрасываем кэш разрешений Spatie (обязательно для сидеров)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Блог
            ['name' => 'read design', 'slug' => 'Чтение статьи'],
            ['name' => 'add design', 'slug' => 'Добавить статью'],
            ['name' => 'update design', 'slug' => 'Обновить статью'],
            ['name' => 'delete design', 'slug' => 'Удалить статью'], 

            // Роли
            ['name' => 'read role', 'slug' => 'Смотреть роль'],
            ['name' => 'add role', 'slug' => 'Добавить роль'],
            ['name' => 'update role', 'slug' => 'Обновить роль'],
            ['name' => 'delete role', 'slug' => 'Удалить роль'],

            // Права ролей
            ['name' => 'add permission role', 'slug' => 'Добавить права роли'],
            ['name' => 'delete permission role', 'slug' => 'Удалить права роли'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'], // По чему искать
                ['slug' => $permission['slug']]                        // Что обновлять/создавать
            );
        }
    }
}
