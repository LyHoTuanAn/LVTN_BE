<?php

namespace App\Services\Notification;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationDispatcher
{
    protected NotificationService $fcmService;
    protected TelegramService $telegramService;

    public function __construct(
        NotificationService $fcmService,
        TelegramService $telegramService
    ) {
        $this->fcmService = $fcmService;
        $this->telegramService = $telegramService;
    }

    /**
     * Dispatch notification based on type
     * 
     * @param string $type Notification type (e.g., 'booking_paid', 'new_movie')
     * @param array $data Data for the notification
     * @param int|null $userId Target user ID (for FCM)
     */
    public function dispatch(string $type, array $data, ?int $userId = null): array
    {
        $channels = config("notifications.channels.{$type}", []);
        $results = [];

        // Check if FCM is enabled for this type
        if ($this->isChannelEnabled($channels, 'fcm') && $userId) {
            $results['fcm'] = $this->dispatchFcm($type, $data, $userId);
        }

        // Check if Telegram is enabled for this type
        if ($this->isChannelEnabled($channels, 'telegram')) {
            $results['telegram'] = $this->dispatchTelegram($type, $data);
        }

        // Check if SMS is enabled for this type
        if ($this->isChannelEnabled($channels, 'sms')) {
            $results['sms'] = $this->dispatchSms($type, $data, $userId);
        }

        Log::info("Notification dispatched", [
            'type' => $type,
            'user_id' => $userId,
            'channels' => array_keys($results),
        ]);

        return $results;
    }

    /**
     * Check if a channel is enabled for a notification type
     */
    protected function isChannelEnabled(array $channels, string $channel): bool
    {
        // Check channel-specific config
        if (!($channels[$channel] ?? false)) {
            return false;
        }

        // Check global channel enabled status
        return match ($channel) {
            'fcm' => config('notifications.fcm.enabled', true),
            'telegram' => config('notifications.telegram.enabled', true),
            'sms' => config('notifications.sms.enabled', false),
            default => false,
        };
    }

    /**
     * Dispatch FCM notification
     */
    protected function dispatchFcm(string $type, array $data, int $userId): array
    {
        try {
            $templates = config("notifications.templates.{$type}");
            $locale = app()->getLocale();

            $title = $templates['title'][$locale] ?? $templates['title']['en'] ?? 'Notification';
            $body = $templates['body'][$locale] ?? $templates['body']['en'] ?? '';

            // Replace placeholders in body
            $body = $this->replacePlaceholders($body, $data);

            return $this->fcmService->sendToUser($userId, $title, $body, [
                'type' => $type,
                ...$data,
            ]);
        } catch (\Exception $e) {
            Log::error("FCM dispatch failed", [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch Telegram notification
     */
    protected function dispatchTelegram(string $type, array $data): array
    {
        try {
            return match ($type) {
                'booking_paid' => $this->telegramService->sendBookingPaidNotification($data),
                default => $this->telegramService->sendAdminAlert(
                    ucfirst(str_replace('_', ' ', $type)),
                    json_encode($data, JSON_PRETTY_PRINT),
                    'info'
                ),
            };
        } catch (\Exception $e) {
            Log::error("Telegram dispatch failed", [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch SMS notification
     */
    protected function dispatchSms(string $type, array $data, ?int $userId): array
    {
        // TODO: Implement SMS service (Twilio, Vonage, etc.)
        Log::info("SMS dispatch not implemented", ['type' => $type]);

        return [
            'success' => false,
            'message' => 'SMS service not implemented',
        ];
    }

    /**
     * Replace placeholders in template
     */
    protected function replacePlaceholders(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $template = str_replace("{{$key}}", (string)$value, $template);
            }
        }

        return $template;
    }

    /**
     * Convenience method: Dispatch booking paid notification
     */
    public function dispatchBookingPaid(Booking $booking): array
    {
        // Load relationships if not loaded
        $booking->loadMissing([
            'user',
            'showtime.movie',
            'showtime.room.cinema',
            'seats'
        ]);

        $seats = $booking->seats->map(function ($seat) {
            return $seat->row . $seat->number;
        })->toArray();

        $data = [
            'code' => $booking->code,
            'movie_title' => $booking->showtime->movie->title ?? 'N/A',
            'cinema_name' => $booking->showtime->room->cinema->name ?? 'N/A',
            'room_name' => $booking->showtime->room->name ?? 'N/A',
            'date' => $booking->showtime->date?->format('d/m/Y') ?? 'N/A',
            'start_time' => $booking->showtime->start_time ?? 'N/A',
            'seats' => $seats,
            'seat_count' => count($seats),
            'showtime' => ($booking->showtime->date?->format('d/m/Y') ?? '') . ' ' . ($booking->showtime->start_time ?? ''),
            'customer_name' => $booking->user->name ?? 'N/A',
            'customer_email' => $booking->user->email ?? 'N/A',
            'customer_phone' => $booking->user->phone ?? 'N/A',
            'total_price' => $booking->total_price,
            'payment_method' => $booking->payment_method ?? 'stripe',
            'created_at' => $booking->created_at?->format('d/m/Y H:i:s') ?? now()->format('d/m/Y H:i:s'),
        ];

        return $this->dispatch('booking_paid', $data, $booking->user_id);
    }

    /**
     * Convenience method: Dispatch booking cancelled notification
     */
    public function dispatchBookingCancelled(Booking $booking): array
    {
        $data = [
            'code' => $booking->code,
        ];

        return $this->dispatch('booking_cancelled', $data, $booking->user_id);
    }

    /**
     * Convenience method: Dispatch booking completed notification
     */
    public function dispatchBookingCompleted(Booking $booking): array
    {
        $data = [
            'code' => $booking->code,
        ];

        return $this->dispatch('booking_completed', $data, $booking->user_id);
    }
}
