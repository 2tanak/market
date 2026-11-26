<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('slug')->unique();
			$table->json('options')->nullable(); 
            $table->string('type'); 
            $table->timestamps();
        });

        Schema::create('attribute_category', function (Blueprint $table) {
            $table->id();
            // Указываем жесткую привязку к ads_categories
            $table->foreignId('ads_category_id')->constrained('ads_categories')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('attribute_category');
        Schema::dropIfExists('attributes');
    }
};
