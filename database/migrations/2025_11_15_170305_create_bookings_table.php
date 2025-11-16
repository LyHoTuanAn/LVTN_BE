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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('showtime_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->char('code', 8)->unique();
            $table->boolean('is_paid')->default(false);
            $table->foreignId('voucher_id')->nullable();
            $table->decimal('voucher_amount', 10, 2)->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'completed'])->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id', 'idx_bookings_user_id');
            $table->index('showtime_id', 'idx_bookings_showtime_id');
            $table->index('code', 'idx_bookings_code');
            $table->index('status', 'idx_bookings_status');
            $table->index('is_paid', 'idx_bookings_is_paid');
            $table->index('created_at', 'idx_bookings_created_at');
            $table->index(['user_id', 'status'], 'idx_bookings_user_status');
            $table->index('voucher_id', 'idx_bookings_voucher_id');
        });

        // Add foreign key for voucher_id after vouchers table is created
        // This will be done in a separate migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
