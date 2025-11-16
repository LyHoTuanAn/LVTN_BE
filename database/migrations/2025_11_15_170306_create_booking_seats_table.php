<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->primary(['booking_id', 'seat_id']);

            // Indexes
            $table->index('booking_id', 'idx_booking_seats_booking_id');
            $table->index('seat_id', 'idx_booking_seats_seat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_seats');
    }
};
