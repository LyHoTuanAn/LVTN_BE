<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Note: Migration 2025_12_19_172315_merge_actors_directors_tables already created
        // the new structure with actor_movie and director_movie tables.
        // This migration only needs to ensure columns are dropped from movies table.
        
        // Check if movies table exists and has the columns before dropping
        if (Schema::hasTable('movies')) {
            $columns = DB::select('SHOW COLUMNS FROM movies');
            $columnNames = array_column($columns, 'Field');
            
            Schema::table('movies', function (Blueprint $table) use ($columnNames) {
                if (in_array('director', $columnNames)) {
                    $table->dropColumn('director');
                }
                if (in_array('actors', $columnNames)) {
                    $table->dropColumn('actors');
                }
            });
        }
    }

    public function down(): void
    {
        // Re-add columns to movies if they don't exist
        if (Schema::hasTable('movies')) {
            $columns = DB::select('SHOW COLUMNS FROM movies');
            $columnNames = array_column($columns, 'Field');
            
            Schema::table('movies', function (Blueprint $table) use ($columnNames) {
                if (!in_array('director', $columnNames)) {
                    $table->string('director', 255)->nullable()->after('language');
                }
                if (!in_array('actors', $columnNames)) {
                    $table->json('actors')->nullable()->after('director');
                }
            });
        }
        
        // Note: We don't drop actor_movie and director_movie tables here
        // because they are managed by migration 2025_12_19_172315_merge_actors_directors_tables
    }
};
