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
        Schema::table('refresh_tokens', function (Blueprint $table) {
            // Drop old token column and indexes
            if (Schema::hasColumn('refresh_tokens', 'token')) {
                $table->dropIndex('idx_refresh_tokens_token');
                $table->dropColumn('token');
            }

            // Add jti column (JWT ID)
            if (!Schema::hasColumn('refresh_tokens', 'jti')) {
                $table->string('jti', 100)->unique()->after('user_id');
                $table->index('jti', 'idx_refresh_tokens_jti');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refresh_tokens', function (Blueprint $table) {
            // Drop jti column
            if (Schema::hasColumn('refresh_tokens', 'jti')) {
                $table->dropIndex('idx_refresh_tokens_jti');
                $table->dropColumn('jti');
            }

            // Restore token column
            if (!Schema::hasColumn('refresh_tokens', 'token')) {
                $table->string('token', 64)->unique()->after('user_id');
                $table->index('token', 'idx_refresh_tokens_token');
            }
        });
    }
};
