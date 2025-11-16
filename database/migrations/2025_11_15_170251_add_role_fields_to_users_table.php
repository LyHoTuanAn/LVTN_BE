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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('password')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('avatar_id')->nullable()->after('role_id');
            $table->string('phone', 20)->nullable()->after('avatar_id');
            $table->string('address', 255)->nullable()->after('phone');

            // Indexes
            $table->index('role_id', 'idx_users_role_id');
            $table->index('avatar_id', 'idx_users_avatar_id');
            $table->index('email', 'idx_users_email');
            $table->index('created_at', 'idx_users_created_at');
        });

        // Add foreign key for avatar_id after media_files table is created
        // This will be done in a separate migration after media_files
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['avatar_id']);
            $table->dropIndex('idx_users_role_id');
            $table->dropIndex('idx_users_avatar_id');
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_created_at');
            $table->dropColumn(['role_id', 'avatar_id', 'phone', 'address']);
        });
    }
};
