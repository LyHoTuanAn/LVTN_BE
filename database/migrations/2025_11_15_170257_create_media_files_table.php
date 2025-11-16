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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('mime_type', 100);
            $table->bigInteger('size');
            $table->string('type', 50);
            $table->timestamps();

            // Indexes
            $table->index('folder_id', 'idx_media_files_folder_id');
            $table->index('user_id', 'idx_media_files_user_id');
            $table->index('type', 'idx_media_files_type');
            $table->index('created_at', 'idx_media_files_created_at');
        });

        // Add foreign key for avatar_id in users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('avatar_id')->references('id')->on('media_files')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['avatar_id']);
        });
        
        Schema::dropIfExists('media_files');
    }
};
