<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Entity\Model\Users\Model as User;


class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       User::factory()->create([
            'name'     => 'admin',
            'email'    => 'admin@mail.ru',
            
        ]);
        User::factory()->create([
            'name'     => 'Юлия',
            'email'    => 'ulia@mail.ru',
            
        ]);
		 User::factory()->create([
            'name'     => 'gerj',
            'email'    => 'gerj@mail.ru',
            
        ]);
		 User::factory()->create([
            'name'     => 'viktor',
            'email'    => 'viktor@mail.ru',
            
        ]);
        User::factory(40)->create();
    }
}
