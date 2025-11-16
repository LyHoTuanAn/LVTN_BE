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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('row', 5);
            $table->integer('number');
            $table->enum('type', ['normal', 'vip', 'couple'])->default('normal');
            $table->enum('status', ['active', 'maintenance', 'disabled'])->default('active');
            $table->timestamps();

            // Unique constraint
            $table->unique(['room_id', 'row', 'number'], 'uk_seats_room_row_number');

            // Indexes
            $table->index('room_id', 'idx_seats_room_id');
            $table->index('type', 'idx_seats_type');
            $table->index('status', 'idx_seats_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
