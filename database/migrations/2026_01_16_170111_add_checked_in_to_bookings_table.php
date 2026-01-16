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
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('checked_in')->default(false)->after('status');
            $table->timestamp('checked_in_at')->nullable()->after('checked_in');
            
            // Index for filtering checked-in bookings
            $table->index('checked_in', 'idx_bookings_checked_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_checked_in');
            $table->dropColumn(['checked_in', 'checked_in_at']);
        });
    }
};
