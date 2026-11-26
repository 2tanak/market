<?php

namespace Modules\Chat\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Chat\database\seeders\MessageSeeder;
use Modules\Chat\database\seeders\ChatSeeder;

class ChatDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
		  ChatSeeder::class,
		  MessageSeeder::class,
		  
		]);
    }
}
