<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\database\seeders\ArticleCategorySeeder;
use Modules\Blog\database\seeders\ArticleSeeder;
use Modules\Blog\database\seeders\CategorySeeder;




class BlogDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->call([
            ArticleCategorySeeder::class,
			CategorySeeder::class,
			ArticleSeeder::class
        ]);
    }
}
