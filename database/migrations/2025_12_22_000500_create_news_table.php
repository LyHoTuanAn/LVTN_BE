<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_vi');
            $table->string('slug')->unique();
            $table->text('summary_en')->nullable();
            $table->text('summary_vi')->nullable();
            $table->longText('content_en')->nullable();
            $table->longText('content_vi')->nullable();
            $table->foreignId('thumbnail_id')->nullable()->constrained('media_files')->onDelete('set null');
            $table->foreignId('author_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('author_id');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

