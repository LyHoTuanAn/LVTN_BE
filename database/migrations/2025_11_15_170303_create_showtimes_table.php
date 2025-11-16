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
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint
            $table->unique(['room_id', 'date', 'start_time'], 'uk_showtimes_room_date_start');

            // Indexes
            $table->index('movie_id', 'idx_showtimes_movie_id');
            $table->index('room_id', 'idx_showtimes_room_id');
            $table->index('date', 'idx_showtimes_date');
            $table->index('status', 'idx_showtimes_status');
            $table->index(['date', 'start_time'], 'idx_showtimes_date_start_time');
            $table->index(['movie_id', 'date'], 'idx_showtimes_movie_date');
            $table->index('deleted_at', 'idx_showtimes_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
