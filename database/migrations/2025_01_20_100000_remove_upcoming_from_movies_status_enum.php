<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Loại bỏ UPCOMING khỏi enum status của movies:
     * - COMING_SOON: Sắp ra mắt - Chưa có suất chiếu hoặc suất chiếu < release_date
     * - NOW_SHOWING: Đang chiếu - Có suất chiếu tại ngày hiện tại và các ngày trong tương lai
     */
    public function up(): void
    {
        // Chuyển tất cả UPCOMING thành NOW_SHOWING (vì UPCOMING là phim có suất tương lai, giờ thuộc NOW_SHOWING)
        DB::statement("UPDATE movies SET status = 'NOW_SHOWING' WHERE status = 'UPCOMING'");

        // Đối với MySQL, cần sử dụng raw SQL để thay đổi enum
        DB::statement("ALTER TABLE movies MODIFY COLUMN status ENUM('COMING_SOON', 'NOW_SHOWING') DEFAULT 'COMING_SOON'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về enum có UPCOMING
        DB::statement("ALTER TABLE movies MODIFY COLUMN status ENUM('COMING_SOON', 'UPCOMING', 'NOW_SHOWING') DEFAULT 'COMING_SOON'");
    }
};
