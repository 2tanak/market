<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\database\seeders\UserSeeder; 
use Modules\Auth\database\seeders\RoleSeeder;
use Modules\Auth\database\seeders\PermissionSeeder;
use Modules\Auth\database\seeders\UserRoleSeeder;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		
		 $this->call([
            UserSeeder::class,
			RoleSeeder::class,
			PermissionSeeder::class,
			UserRoleSeeder::class,
        ]);
		
    }
}
