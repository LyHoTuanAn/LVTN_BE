<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create directors table
        Schema::create('directors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('avatar_id')->nullable()->constrained('media_files')->onDelete('set null');
            $table->timestamps();
            
            $table->index('name');
        });

        // Create director_movie pivot table
        Schema::create('director_movie', function (Blueprint $table) {
            $table->foreignId('director_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->primary(['director_id', 'movie_id']);
        });

        // Create actors table
        Schema::create('actors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('avatar_id')->nullable()->constrained('media_files')->onDelete('set null');
            $table->timestamps();
            
            $table->index('name');
        });

        // Create actor_movie pivot table
        Schema::create('actor_movie', function (Blueprint $table) {
            $table->foreignId('actor_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->string('role', 255)->nullable(); // Character name
            $table->primary(['actor_id', 'movie_id']);
        });

        // Remove director and actors columns from movies (added in previous migration)
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['director', 'actors']);
        });
    }

    public function down(): void
    {
        // Re-add columns to movies
        Schema::table('movies', function (Blueprint $table) {
            $table->string('director', 255)->nullable()->after('language');
            $table->json('actors')->nullable()->after('director');
        });

        Schema::dropIfExists('actor_movie');
        Schema::dropIfExists('actors');
        Schema::dropIfExists('director_movie');
        Schema::dropIfExists('directors');
    }
};
