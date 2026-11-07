<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\database\seeders\UserSeeder; 

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		
		 $this->call([
            UserSeeder::class,
        ]);
		
    }
}
