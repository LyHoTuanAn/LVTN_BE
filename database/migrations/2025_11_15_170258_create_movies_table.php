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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('duration');
            $table->date('release_date');
            $table->enum('status', ['coming_soon', 'now_showing', 'ended'])->default('coming_soon');
            $table->foreignId('poster_id')->nullable()->constrained('media_files')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('trailer_id')->nullable()->constrained('media_files')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status', 'idx_movies_status');
            $table->index('release_date', 'idx_movies_release_date');
            $table->index('title', 'idx_movies_title');
            $table->index('poster_id', 'idx_movies_poster_id');
            $table->index('trailer_id', 'idx_movies_trailer_id');
            $table->index('created_at', 'idx_movies_created_at');
            $table->index('deleted_at', 'idx_movies_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
