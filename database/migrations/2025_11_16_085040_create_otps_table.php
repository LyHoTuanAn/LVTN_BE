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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('email', 150);
            $table->string('otp_code', 6);
            $table->enum('type', ['register', 'forgot_password']);
            $table->timestamp('expires_at');
            $table->timestamps();

            // Indexes
            $table->index('user_id', 'idx_otps_user_id');
            $table->index('email', 'idx_otps_email');
            $table->index('type', 'idx_otps_type');
            $table->index('otp_code', 'idx_otps_otp_code');
            $table->index('expires_at', 'idx_otps_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
