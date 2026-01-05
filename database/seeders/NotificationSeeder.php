<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users (or only customers if they exist)
        $customers = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->get();

        // If no customers, get all users
        if ($customers->isEmpty()) {
            $customers = User::all();
        }

        if ($customers->isEmpty()) {
            $this->command->warn('No users found. Skipping notification seeder.');
            return;
        }

        // Sample notification templates
        $notificationTemplates = [
            // Booking notifications
            [
                'type' => Notification::TYPE_BOOKING_PAID,
                'title' => '🎟️ Đặt vé thành công!',
                'body' => 'Bạn đã đặt thành công 2 vé xem phim "Avengers: Endgame" vào lúc 19:30 ngày 05/01/2026 tại CGV Vincom.',
                'related_type' => 'booking',
            ],
            [
                'type' => Notification::TYPE_BOOKING_PAID,
                'title' => '🎟️ Đặt vé thành công!',
                'body' => 'Bạn đã đặt thành công 3 vé xem phim "Spider-Man: No Way Home" vào lúc 21:00 ngày 06/01/2026 tại Lotte Cinema.',
                'related_type' => 'booking',
            ],
            [
                'type' => Notification::TYPE_BOOKING_COMPLETED,
                'title' => '✅ Xem phim vui vẻ!',
                'body' => 'Vé #ABCD1234 đã được sử dụng. Chúc bạn xem phim vui vẻ!',
                'related_type' => 'booking',
            ],
            [
                'type' => Notification::TYPE_BOOKING_CANCELLED,
                'title' => '❌ Đặt vé đã hủy',
                'body' => 'Đơn đặt vé #XYZ5678 của bạn đã bị hủy do quá thời gian thanh toán.',
                'related_type' => 'booking',
            ],

            // Movie notifications
            [
                'type' => Notification::TYPE_NEW_MOVIE,
                'title' => '🎬 Phim mới ra mắt!',
                'body' => 'Phim "Dune: Part Two" đã có lịch chiếu! Đặt vé ngay để nhận ưu đãi sớm.',
                'related_type' => 'movie',
            ],
            [
                'type' => Notification::TYPE_NEW_MOVIE,
                'title' => '🎬 Phim mới ra mắt!',
                'body' => 'Phim "Oppenheimer" đã có lịch chiếu tại các rạp! Đặt vé ngay hôm nay.',
                'related_type' => 'movie',
            ],
            [
                'type' => Notification::TYPE_MOVIE_PREMIERE,
                'title' => '🌟 Công chiếu đặc biệt!',
                'body' => 'Đừng bỏ lỡ buổi công chiếu đặc biệt phim "Avatar 3" vào tối nay! Chỉ còn 50 vé.',
                'related_type' => 'movie',
            ],

            // Promotion notifications
            [
                'type' => Notification::TYPE_NEW_VOUCHER,
                'title' => '🎁 Voucher mới cho bạn!',
                'body' => 'Bạn nhận được voucher giảm 20% cho đơn đặt vé tiếp theo. Mã: HAPPYNEW2026',
                'related_type' => 'voucher',
            ],
            [
                'type' => Notification::TYPE_NEW_VOUCHER,
                'title' => '🎁 Ưu đãi đặc biệt!',
                'body' => 'Mua 2 vé tặng 1 bắp rang bơ. Áp dụng đến hết 31/01/2026. Mã: COMBO2026',
                'related_type' => 'voucher',
            ],
            [
                'type' => Notification::TYPE_PROMOTION,
                'title' => '🔥 Flash Sale!',
                'body' => 'Giảm 50% tất cả các suất chiếu buổi sáng thứ 2 và thứ 3. Chỉ trong tuần này!',
                'related_type' => null,
            ],
            [
                'type' => Notification::TYPE_PROMOTION,
                'title' => '💝 Ưu đãi Valentine!',
                'body' => 'Đặt vé cho 2 người, nhận ngay combo bắp nước miễn phí. Áp dụng 14/02.',
                'related_type' => null,
            ],

            // General notifications
            [
                'type' => Notification::TYPE_GENERAL,
                'title' => '📢 Thông báo hệ thống',
                'body' => 'Ứng dụng đã được cập nhật phiên bản mới với nhiều tính năng hấp dẫn!',
                'related_type' => null,
            ],
            [
                'type' => Notification::TYPE_GENERAL,
                'title' => '🎉 Chào mừng bạn!',
                'body' => 'Cảm ơn bạn đã đăng ký tài khoản. Khám phá ngay các phim hot nhất!',
                'related_type' => null,
            ],
        ];

        $now = Carbon::now();
        $notifications = [];

        // Generate notifications for each customer
        foreach ($customers as $customer) {
            // Random number of notifications per user (3-8)
            $numNotifications = rand(3, 8);
            $selectedTemplates = collect($notificationTemplates)->random($numNotifications);

            foreach ($selectedTemplates as $index => $template) {
                // Random created_at in the last 30 days
                $createdAt = $now->copy()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                
                // 60% chance of being read
                $isRead = rand(1, 100) <= 60;
                $readAt = $isRead ? $createdAt->copy()->addMinutes(rand(5, 120)) : null;

                $notifications[] = [
                    'user_id' => $customer->id,
                    'title' => $template['title'],
                    'body' => $template['body'],
                    'image_url' => null,
                    'type' => $template['type'],
                    'channel' => Notification::CHANNEL_FCM,
                    'related_type' => $template['related_type'],
                    'related_id' => $template['related_type'] ? rand(1, 100) : null,
                    'data' => null,
                    'is_read' => $isRead,
                    'read_at' => $readAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }

        // Batch insert
        $chunks = array_chunk($notifications, 500);
        foreach ($chunks as $chunk) {
            Notification::insert($chunk);
        }

        $this->command->info('Created ' . count($notifications) . ' notifications for ' . $customers->count() . ' customers.');
    }
}
