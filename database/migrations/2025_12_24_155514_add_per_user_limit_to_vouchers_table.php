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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->integer('per_user_limit')->nullable()->after('usage_limit');
            $table->index('per_user_limit', 'idx_vouchers_per_user_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropIndex('idx_vouchers_per_user_limit');
            $table->dropColumn('per_user_limit');
        });
    }
};
