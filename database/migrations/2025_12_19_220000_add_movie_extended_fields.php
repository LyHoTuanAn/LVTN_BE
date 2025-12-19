<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Simplified design: All fields directly in movies table
     * - genre: String (e.g., "Action, Drama, Comedy")
     * - age_classification: Enum (P, K, T13, T16, T18, C)
     * - language: String (e.g., "Vietnamese", "English")
     * - director: String (director name)
     * - actors: JSON array of actor names
     */
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->string('genre', 255)->nullable()->after('status');
            $table->enum('age_classification', ['P', 'K', 'T13', 'T16', 'T18', 'C'])->default('P')->after('genre');
            $table->string('language', 100)->nullable()->after('age_classification');
            $table->string('director', 255)->nullable()->after('language');
            $table->json('actors')->nullable()->after('director');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['genre', 'age_classification', 'language', 'director', 'actors']);
        });
    }
};
