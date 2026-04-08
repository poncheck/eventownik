<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_products', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['soup', 'starter', 'main', 'side_starchy', 'salad', 'sauce']);
            $table->enum('serving_type', ['plate', 'platter'])->nullable(); // tylko dla main
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_per_person', 8, 2)->default(0); // cena przy 100%
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_products');
    }
};
