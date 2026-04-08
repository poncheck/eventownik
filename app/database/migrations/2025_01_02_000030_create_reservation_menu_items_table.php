<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_product_id')->constrained('menu_products')->cascadeOnDelete();
            $table->decimal('percentage', 5, 1)->default(100);
            $table->timestamps();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('menu_type', ['proposal', 'custom'])->nullable()->after('menu_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('menu_type');
        });
        Schema::dropIfExists('reservation_menu_items');
    }
};
