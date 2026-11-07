<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
			$table->string('title',255);
			$table->text('description');
			$table->text('photo',255)->nullable();
			$table->integer('file_id')->nullable();
			$table->bigInteger('category_id')->unsigned()->nullable();
			$table->integer('views_count')->nullable();
			$table->enum('publish', [0, 1])->default(0);
			$table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
