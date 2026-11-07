<?php

namespace Modules\Entity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Entity\database\seeders\LanguageSeeder;


class EntityDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([LanguageSeeder::class]);
    }
}
