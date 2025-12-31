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
        Schema::table('reviews', function (Blueprint $table) {
            // Add booking_id column (optional - user can review without booking in some cases)
            $table->foreignId('booking_id')
                ->nullable()
                ->after('movie_id')
                ->constrained()
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Add status column for moderation
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->after('comment');

            // Indexes
            $table->index('booking_id', 'idx_reviews_booking_id');
            $table->index('status', 'idx_reviews_status');
            $table->index(['movie_id', 'status'], 'idx_reviews_movie_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_reviews_booking_id');
            $table->dropIndex('idx_reviews_status');
            $table->dropIndex('idx_reviews_movie_status');

            // Drop foreign key and column
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
            $table->dropColumn('status');
        });
    }
};
