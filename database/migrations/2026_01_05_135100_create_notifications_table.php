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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification content
            $table->string('title');
            $table->text('body');
            $table->string('image_url')->nullable();
            
            // Notification type & channel
            $table->string('type')->default('general'); // booking_paid, booking_cancelled, new_movie, promotion, etc.
            $table->string('channel')->default('fcm'); // fcm, sms, telegram
            
            // Related entity (optional)
            $table->string('related_type')->nullable(); // booking, movie, voucher, etc.
            $table->unsignedBigInteger('related_id')->nullable();
            
            // Extra data as JSON
            $table->json('data')->nullable();
            
            // Status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
            $table->index('created_at');
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
