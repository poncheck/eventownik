<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            // Dane klienta
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Impreza
            $table->foreignId('event_type_id')->constrained();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('menu_id')->nullable()->constrained()->nullOnDelete();

            $table->date('event_date');
            $table->time('event_time');
            $table->decimal('duration_hours', 4, 1);
            $table->integer('guest_count');
            $table->text('notes')->nullable();

            // Status
            $table->enum('status', [
                'new',
                'contacted',
                'awaiting_payment',
                'confirmed',
                'completed',
                'cancelled',
            ])->default('new');

            // Admin
            $table->text('internal_notes')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();

            $table->timestamps();
        });

        Schema::create('room_blockouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('date_from');
            $table->date('date_to');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_blockouts');
        Schema::dropIfExists('reservations');
    }
};
