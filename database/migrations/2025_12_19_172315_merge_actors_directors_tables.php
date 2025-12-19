<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Create new actor_movie table with all actor data
        Schema::create('actor_movie_new', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('avatar_id')->nullable()->constrained('media_files')->onDelete('set null');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('name');
            $table->index('movie_id');
            $table->index('avatar_id');
            $table->index(['movie_id', 'name']);
            $table->index('deleted_at');
        });

        // Step 2: Migrate data from actors + actor_movie to actor_movie_new
        DB::statement("
            INSERT INTO actor_movie_new (name, avatar_id, movie_id, created_at, updated_at)
            SELECT 
                a.name,
                a.avatar_id,
                am.movie_id,
                a.created_at,
                a.updated_at
            FROM actors a
            INNER JOIN actor_movie am ON a.id = am.actor_id
        ");

        // Step 3: Create new director_movie table with all director data
        Schema::create('director_movie_new', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('avatar_id')->nullable()->constrained('media_files')->onDelete('set null');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('name');
            $table->index('movie_id');
            $table->index('avatar_id');
            $table->index(['movie_id', 'name']);
            $table->index('deleted_at');
        });

        // Step 4: Migrate data from directors + director_movie to director_movie_new
        DB::statement("
            INSERT INTO director_movie_new (name, avatar_id, movie_id, created_at, updated_at)
            SELECT 
                d.name,
                d.avatar_id,
                dm.movie_id,
                d.created_at,
                d.updated_at
            FROM directors d
            INNER JOIN director_movie dm ON d.id = dm.director_id
        ");

        // Step 5: Drop old tables
        Schema::dropIfExists('actor_movie');
        Schema::dropIfExists('actors');
        Schema::dropIfExists('director_movie');
        Schema::dropIfExists('directors');

        // Step 6: Rename new tables to final names using raw SQL
        DB::statement('ALTER TABLE actor_movie_new RENAME TO actor_movie');
        DB::statement('ALTER TABLE director_movie_new RENAME TO director_movie');
    }

    public function down(): void
    {
        // Rename tables back to temporary names
        DB::statement('ALTER TABLE actor_movie RENAME TO actor_movie_old');
        DB::statement('ALTER TABLE director_movie RENAME TO director_movie_old');

        // Recreate old structure
        Schema::create('directors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('avatar_id')->nullable()->constrained('media_files')->onDelete('set null');
            $table->timestamps();
            $table->index('name');
        });

        Schema::create('director_movie', function (Blueprint $table) {
            $table->foreignId('director_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->primary(['director_id', 'movie_id']);
        });

        Schema::create('actors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('avatar_id')->nullable()->constrained('media_files')->onDelete('set null');
            $table->timestamps();
            $table->index('name');
        });

        Schema::create('actor_movie', function (Blueprint $table) {
            $table->foreignId('actor_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->primary(['actor_id', 'movie_id']);
        });

        // Migrate data back (simplified - may lose some data if same name+avatar appears in multiple movies)
        // Note: This is a simplified rollback. Full data restoration would require more complex logic.
        DB::statement("
            INSERT INTO directors (name, avatar_id, created_at, updated_at)
            SELECT DISTINCT name, avatar_id, MIN(created_at), MAX(updated_at)
            FROM director_movie_old
            GROUP BY name, avatar_id
        ");

        DB::statement("
            INSERT INTO director_movie (director_id, movie_id)
            SELECT d.id, dm.movie_id
            FROM directors d
            INNER JOIN director_movie_old dm ON d.name = dm.name AND (d.avatar_id = dm.avatar_id OR (d.avatar_id IS NULL AND dm.avatar_id IS NULL))
        ");

        DB::statement("
            INSERT INTO actors (name, avatar_id, created_at, updated_at)
            SELECT DISTINCT name, avatar_id, MIN(created_at), MAX(updated_at)
            FROM actor_movie_old
            GROUP BY name, avatar_id
        ");

        DB::statement("
            INSERT INTO actor_movie (actor_id, movie_id)
            SELECT a.id, am.movie_id
            FROM actors a
            INNER JOIN actor_movie_old am ON a.name = am.name AND (a.avatar_id = am.avatar_id OR (a.avatar_id IS NULL AND am.avatar_id IS NULL))
        ");

        // Drop old tables
        Schema::dropIfExists('actor_movie_old');
        Schema::dropIfExists('director_movie_old');
    }
};
