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
     * Thay đổi enum status của movies:
     * - COMING_SOON: Sắp ra mắt - Chưa có suất chiếu
     * - UPCOMING: Sắp chiếu - Có suất chiếu trong tương lai, chưa có suất nào đang diễn ra
     * - NOW_SHOWING: Đang chiếu - Có ít nhất 1 suất đang ONGOING hoặc còn suất trong hôm nay
     */
    public function up(): void
    {
        // Đối với MySQL, cần sử dụng raw SQL để thay đổi enum
        DB::statement("ALTER TABLE movies MODIFY COLUMN status ENUM('COMING_SOON', 'UPCOMING', 'NOW_SHOWING') DEFAULT 'COMING_SOON'");

        // Map các giá trị cũ sang giá trị mới
        DB::statement("UPDATE movies SET status = 'COMING_SOON' WHERE status = 'coming_soon' OR status = 'COMING_SOON'");
        DB::statement("UPDATE movies SET status = 'NOW_SHOWING' WHERE status = 'now_showing' OR status = 'NOW_SHOWING'");
        // 'ended' sẽ được chuyển thành 'COMING_SOON' vì phim đã hết chiếu có thể quay lại
        DB::statement("UPDATE movies SET status = 'COMING_SOON' WHERE status = 'ended'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về enum cũ
        DB::statement("ALTER TABLE movies MODIFY COLUMN status ENUM('coming_soon', 'now_showing', 'ended') DEFAULT 'coming_soon'");

        // Map các giá trị mới sang giá trị cũ
        DB::statement("UPDATE movies SET status = 'coming_soon' WHERE status = 'COMING_SOON'");
        DB::statement("UPDATE movies SET status = 'now_showing' WHERE status = 'NOW_SHOWING' OR status = 'UPCOMING'");
    }
};
