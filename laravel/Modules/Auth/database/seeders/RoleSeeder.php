<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Model\Role\Model as Role; 

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Обязательно сбрасываем кэш пакета перед работой
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            ['name' => 'admin', 'slug' => 'Администратор'],
            ['name' => 'user', 'slug' => 'Пользователь'],
            ['name' => 'manager', 'slug' => 'Менеджер'],
             
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name'], 'guard_name' => 'web'], 
                ['slug' => $role['slug']]                        
            );
        }
    }
}
