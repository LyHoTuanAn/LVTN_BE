<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Stripe payment intent ID
            $table->string('payment_intent_id')->nullable()->after('payment_method');
            
            // Timestamp when payment was completed
            $table->timestamp('paid_at')->nullable()->after('payment_intent_id');
            
            // Index for payment_intent_id for webhook lookups
            $table->index('payment_intent_id', 'idx_bookings_payment_intent_id');
        });

        // Update status enum to include new payment statuses
        // Note: In MySQL, we need to modify enum this way
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'canceled', 'completed', 'payment_failed', 'refunded') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_payment_intent_id');
            $table->dropColumn(['payment_intent_id', 'paid_at']);
        });

        // Revert status enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'canceled', 'completed') DEFAULT 'pending'");
    }
};
