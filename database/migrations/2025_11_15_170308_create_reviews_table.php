<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media_files')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();

            // Unique constraint
            $table->unique(['user_id', 'movie_id'], 'uk_reviews_user_movie');

            // Indexes
            $table->index('user_id', 'idx_reviews_user_id');
            $table->index('movie_id', 'idx_reviews_movie_id');
            $table->index('rating', 'idx_reviews_rating');
            $table->index(['movie_id', 'rating'], 'idx_reviews_movie_rating');
            $table->index('created_at', 'idx_reviews_created_at');
            $table->index('media_id', 'idx_reviews_media_id');
        });

        // Add CHECK constraint for rating using raw SQL
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT chk_reviews_rating CHECK (rating >= 1 AND rating <= 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Dropping the table will automatically drop the CHECK constraint
        Schema::dropIfExists('reviews');
    }
};
