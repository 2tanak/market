<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Auth\database\seeders\AuthDatabaseSeeder;
use Modules\Blog\database\seeders\BlogDatabaseSeeder;
use Modules\Entity\database\seeders\EntityDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AuthDatabaseSeeder::class,
			BlogDatabaseSeeder::class,
			EntityDatabaseSeeder::class,
        ]);
    }
}
