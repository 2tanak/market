<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Model\Users\Model as User;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Находим пользователя-администратора по его email
        $admin = User::where('email', 'admin@mail.ru')->first();

        // 2. Если пользователь существует, привязываем ему роль 'admin'
        if ($admin) {
            // Метод assignRole автоматически учтет ваш MorphMap ('user') 
            // и создаст правильную запись в таблице model_has_roles
            $admin->assignRole('admin');
        }
    }
}
