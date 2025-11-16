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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cinema_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->string('name', 100);
            $table->integer('seat_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint
            $table->unique(['cinema_id', 'name'], 'uk_rooms_cinema_name');

            // Indexes
            $table->index('cinema_id', 'idx_rooms_cinema_id');
            $table->index('deleted_at', 'idx_rooms_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
