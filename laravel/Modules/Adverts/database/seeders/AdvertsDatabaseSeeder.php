<?php

namespace Modules\Adverts\Database\Seeders;

use Modules\Adverts\database\seeders\ProductSeeder;
use Illuminate\Database\Seeder;

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
