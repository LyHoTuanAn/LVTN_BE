<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove 'cancelled' status from showtimes status enum
     * Update any existing 'cancelled' showtimes to 'completed'
     */
    public function up(): void
    {
        // Update any existing 'cancelled' showtimes to 'completed'
        DB::table('showtimes')
            ->where('status', 'cancelled')
            ->update(['status' => 'completed']);

        // Modify enum to remove 'cancelled'
        DB::statement("ALTER TABLE showtimes MODIFY COLUMN status ENUM('scheduled', 'ongoing', 'completed') DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'cancelled' back to enum
        DB::statement("ALTER TABLE showtimes MODIFY COLUMN status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled'");
    }
};
