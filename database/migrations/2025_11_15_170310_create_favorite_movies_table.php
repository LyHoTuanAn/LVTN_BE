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
        Schema::create('favorite_movies', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('created_at')->nullable();
            $table->primary(['user_id', 'movie_id']);

            // Indexes
            $table->index('user_id', 'idx_favorite_movies_user_id');
            $table->index('movie_id', 'idx_favorite_movies_movie_id');
            $table->index('created_at', 'idx_favorite_movies_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_movies');
    }
};
