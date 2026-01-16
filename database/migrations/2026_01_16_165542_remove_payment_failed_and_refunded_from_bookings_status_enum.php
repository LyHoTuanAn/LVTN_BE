<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove 'payment_failed' and 'refunded' status from bookings status enum
     * Update any existing 'payment_failed' or 'refunded' bookings to 'canceled'
     */
    public function up(): void
    {
        // Update any existing 'payment_failed' or 'refunded' bookings to 'canceled'
        DB::table('bookings')
            ->whereIn('status', ['payment_failed', 'refunded'])
            ->update(['status' => 'canceled']);

        // Modify enum to remove 'payment_failed' and 'refunded'
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'canceled', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'payment_failed' and 'refunded' back to enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'canceled', 'completed', 'payment_failed', 'refunded') DEFAULT 'pending'");
    }
};
