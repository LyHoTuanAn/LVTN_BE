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

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
                $table->string('email', 150);
                $table->string('token', 64)->unique();
                $table->timestamp('expires_at');
                $table->timestamps();

                $table->index('user_id', 'idx_password_reset_tokens_user_id');
                $table->index('email', 'idx_password_reset_tokens_email');
                $table->index('expires_at', 'idx_password_reset_tokens_expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
