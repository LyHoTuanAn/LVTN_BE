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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('amount', 10, 2);
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->enum('applies_to', ['all_users', 'specific_users', 'specific_movies'])->default('all_users');
            $table->string('only_for_user', 255)->nullable();
            $table->string('only_for_movie', 255)->nullable();
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index('code', 'idx_vouchers_code');
            $table->index('status', 'idx_vouchers_status');
            $table->index(['valid_from', 'valid_to'], 'idx_vouchers_valid_range');
            $table->index('type', 'idx_vouchers_type');
        });

        // Add foreign key for voucher_id in bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
        });
        
        Schema::dropIfExists('vouchers');
    }
};
