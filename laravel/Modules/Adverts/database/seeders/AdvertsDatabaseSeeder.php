<?php

namespace Modules\Adverts\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Adverts\database\seeders\ProductSeeder;

class AdvertsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
