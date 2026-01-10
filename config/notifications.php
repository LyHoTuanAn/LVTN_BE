<?php

use App\Services\Notification\TelegramService;
use App\Services\Notification\NotificationService;

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Channels Configuration
    |--------------------------------------------------------------------------
    |
    | Define which channels each notification type should use.
    | Channels: 'fcm', 'telegram', 'sms'
    |
    */

    'channels' => [
        /*
        |--------------------------------------------------------------------------
        | Booking Notifications
        |--------------------------------------------------------------------------
        */
        'booking_created' => [
            'fcm' => true,      // Gửi FCM cho user đặt vé
            'telegram' => false, // Không cần gửi telegram
            'sms' => false,
        ],

        'booking_paid' => [
            'fcm' => true,       // Gửi FCM cho user
            'telegram' => true,  // Gửi Telegram cho admin
            'email' => true,     // Gửi Email cho user
            'sms' => false,
        ],

        'booking_cancelled' => [
            'fcm' => true,      // Gửi FCM cho user
            'telegram' => false,
            'sms' => false,
        ],

        'booking_completed' => [
            'fcm' => true,      // Gửi FCM cho user khi hoàn thành
            'telegram' => false,
            'sms' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Movie Notifications
        |--------------------------------------------------------------------------
        */
        'new_movie' => [
            'fcm' => true,      // Broadcast FCM cho tất cả users
            'telegram' => false,
            'sms' => false,
        ],

        'movie_premiere' => [
            'fcm' => true,
            'telegram' => false,
            'sms' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Promotion Notifications
        |--------------------------------------------------------------------------
        */
        'new_voucher' => [
            'fcm' => true,
            'telegram' => false,
            'sms' => false,
        ],

        'promotion_alert' => [
            'fcm' => true,
            'telegram' => true, // Alert admin về promotions
            'sms' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | User Notifications
        |--------------------------------------------------------------------------
        */
        'user_registered' => [
            'fcm' => false,
            'telegram' => true, // Thông báo admin có user mới
            'sms' => false,
        ],

        'password_reset' => [
            'fcm' => false,
            'telegram' => false,
            'sms' => true,      // Gửi OTP qua SMS
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Configuration
    |--------------------------------------------------------------------------
    */
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'admin_channel' => env('TELEGRAM_ADMIN_CHANNEL', '-1003538778084'),
        'enabled' => env('TELEGRAM_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration (Twilio / Other)
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'driver' => env('SMS_DRIVER', 'twilio'), // twilio, vonage, custom
        'from' => env('SMS_FROM'),
        'enabled' => env('SMS_ENABLED', false),

        // Twilio specific
        'twilio' => [
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    */
    'email' => [
        'enabled' => env('EMAIL_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | FCM Configuration
    |--------------------------------------------------------------------------
    | FCM config is stored in config/firebase.php
    */
    'fcm' => [
        'enabled' => env('FCM_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Locale for Notifications and Emails
    |--------------------------------------------------------------------------
    | 
    | This determines the default language for notifications and emails
    | when no locale is explicitly provided. Default: 'vi' (Vietnamese)
    |
    */
    'default_locale' => env('NOTIFICATIONS_DEFAULT_LOCALE', 'vi'),

    /*
    |--------------------------------------------------------------------------
    | Notification Templates
    |--------------------------------------------------------------------------
    | Define message templates for each notification type
    */
    'templates' => [
        'booking_paid' => [
            'title' => [
                'vi' => '🎟️ Đặt vé thành công!',
                'en' => '🎟️ Booking Successful!',
            ],
            'body' => [
                'vi' => 'Bạn đã đặt thành công {seat_count} vé xem phim "{movie_title}" vào lúc {showtime}.',
                'en' => 'You have successfully booked {seat_count} ticket(s) for "{movie_title}" at {showtime}.',
            ],
        ],
        'booking_cancelled' => [
            'title' => [
                'vi' => '❌ Đặt vé đã hủy',
                'en' => '❌ Booking Cancelled',
            ],
            'body' => [
                'vi' => 'Đơn đặt vé #{code} của bạn đã bị hủy.',
                'en' => 'Your booking #{code} has been cancelled.',
            ],
        ],
        'booking_completed' => [
            'title' => [
                'vi' => '✅ Xem phim vui vẻ!',
                'en' => '✅ Enjoy your movie!',
            ],
            'body' => [
                'vi' => 'Vé #{code} đã được sử dụng. Chúc bạn xem phim vui vẻ!',
                'en' => 'Ticket #{code} has been used. Enjoy your movie!',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Admin Message Templates
    |--------------------------------------------------------------------------
    */
    'telegram_templates' => [
        'booking_paid' => "🎟️ <b>ĐƠN HÀNG MỚI!</b>\n\n"
            . "📋 <b>Mã đơn:</b> <code>{code}</code>\n"
            . "🎬 <b>Phim:</b> {movie_title}\n"
            . "🏠 <b>Rạp:</b> {cinema_name}\n"
            . "🚪 <b>Phòng:</b> {room_name}\n"
            . "📅 <b>Ngày chiếu:</b> {date}\n"
            . "⏰ <b>Giờ chiếu:</b> {start_time}\n"
            . "💺 <b>Ghế:</b> {seats}\n"
            . "👤 <b>Khách hàng:</b> {customer_name}\n"
            . "📧 <b>Email:</b> {customer_email}\n"
            . "📞 <b>SĐT:</b> {customer_phone}\n"
            . "💰 <b>Tổng tiền:</b> {total_price} VNĐ\n"
            . "💳 <b>Thanh toán:</b> {payment_method}\n"
            . "⏱️ <b>Thời gian:</b> {created_at}",
    ],
];
