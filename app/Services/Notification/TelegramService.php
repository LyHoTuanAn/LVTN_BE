<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $baseUrl = 'https://api.telegram.org/bot';
    protected ?string $botToken;
    protected ?string $adminChannel;
    protected bool $enabled;

    public function __construct()
    {
        $this->botToken = config('notifications.telegram.bot_token');
        $this->adminChannel = config('notifications.telegram.admin_channel');
        $this->enabled = config('notifications.telegram.enabled', true);
    }

    /**
     * Send message to admin channel
     */
    public function sendToAdminChannel(string $message, array $options = []): array
    {
        return $this->sendMessage($this->adminChannel, $message, $options);
    }

    /**
     * Send message to a specific chat/channel
     */
    public function sendMessage(string $chatId, string $message, array $options = []): array
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'Telegram notifications are disabled',
            ];
        }

        if (empty($this->botToken)) {
            Log::error('Telegram bot token is not configured');
            return [
                'success' => false,
                'message' => 'Telegram bot token is not configured',
            ];
        }

        try {
            $url = $this->baseUrl . $this->botToken . '/sendMessage';

            $payload = array_merge([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $options['parse_mode'] ?? 'HTML',
                'disable_web_page_preview' => $options['disable_preview'] ?? true,
            ], $options);

            $response = Http::timeout(30)->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['ok'] ?? false) {
                    Log::info('Telegram message sent successfully', [
                        'chat_id' => $chatId,
                        'message_id' => $result['result']['message_id'] ?? null,
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Message sent successfully',
                        'message_id' => $result['result']['message_id'] ?? null,
                    ];
                }

                Log::error('Telegram API returned error', [
                    'response' => $result,
                ]);

                return [
                    'success' => false,
                    'message' => $result['description'] ?? 'Unknown error',
                ];
            }

            Log::error('Telegram API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send message: HTTP ' . $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Telegram API exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send booking paid notification to admin channel
     */
    public function sendBookingPaidNotification(array $bookingData): array
    {
        $template = config('notifications.telegram_templates.booking_paid');

        // Format seats (e.g., "A1, A2, B3")
        $seats = is_array($bookingData['seats']) 
            ? implode(', ', $bookingData['seats']) 
            : $bookingData['seats'];

        // Format price with thousand separator
        $totalPrice = number_format($bookingData['total_price'] ?? 0, 0, ',', '.');

        // Format payment method
        $paymentMethodMap = [
            'stripe' => 'Stripe 💳',
            'vnpay' => 'VNPay 🏦',
            'cash' => 'Tiền mặt 💵',
        ];
        $paymentMethod = $paymentMethodMap[$bookingData['payment_method'] ?? 'stripe'] ?? 'N/A';

        // Replace placeholders
        $message = strtr($template, [
            '{code}' => $bookingData['code'] ?? 'N/A',
            '{movie_title}' => $bookingData['movie_title'] ?? 'N/A',
            '{cinema_name}' => $bookingData['cinema_name'] ?? 'N/A',
            '{room_name}' => $bookingData['room_name'] ?? 'N/A',
            '{date}' => $bookingData['date'] ?? 'N/A',
            '{start_time}' => $bookingData['start_time'] ?? 'N/A',
            '{seats}' => $seats,
            '{customer_name}' => $bookingData['customer_name'] ?? 'N/A',
            '{customer_email}' => $bookingData['customer_email'] ?? 'N/A',
            '{customer_phone}' => $bookingData['customer_phone'] ?? 'N/A',
            '{total_price}' => $totalPrice,
            '{payment_method}' => $paymentMethod,
            '{created_at}' => $bookingData['created_at'] ?? date('d/m/Y H:i:s'),
        ]);

        return $this->sendToAdminChannel($message);
    }

    /**
     * Send custom notification to admin
     */
    public function sendAdminAlert(string $title, string $message, string $type = 'info'): array
    {
        $icons = [
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'error' => '❌',
            'success' => '✅',
        ];

        $icon = $icons[$type] ?? 'ℹ️';
        $formattedMessage = "{$icon} <b>{$title}</b>\n\n{$message}";

        return $this->sendToAdminChannel($formattedMessage);
    }

    /**
     * Get bot info (for testing)
     */
    public function getBotInfo(): array
    {
        if (empty($this->botToken)) {
            return [
                'success' => false,
                'message' => 'Bot token is not configured',
            ];
        }

        try {
            $url = $this->baseUrl . $this->botToken . '/getMe';
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get bot info',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
