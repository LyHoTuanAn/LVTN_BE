<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kedniko\FCM\Credentials;
use Kedniko\FCM\FCM;

class NotificationService
{
    /*
    |--------------------------------------------------------------------------
    | FCM Topic Definitions
    |--------------------------------------------------------------------------
    |
    | All FCM topics are defined in config/firebase.php
    | Available topics:
    |   - config('firebase.topics.all_users')        : For all users broadcast
    |   - config('firebase.topics.new_movies')       : New movie announcements
    |   - config('firebase.topics.promotions')       : Promotions and discounts
    |   - config('firebase.topics.booking_reminders'): Booking reminders
    |
    */

    protected FcmTokenService $fcmTokenService;
    protected ?string $bearerToken = null;

    public function __construct(FcmTokenService $fcmTokenService)
    {
        $this->fcmTokenService = $fcmTokenService;
    }

    /**
     * Send notification to a single user.
     */
    public function sendToUser(
        int $userId,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): array {
        $tokens = $this->fcmTokenService->getActiveTokensForUser($userId);

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active FCM tokens found for user',
                'sent_count' => 0,
                'failed_count' => 0,
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $imageUrl);
    }

    /**
     * Send notification to multiple users.
     */
    public function sendToUsers(
        array $userIds,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): array {
        $tokens = $this->fcmTokenService->getActiveTokensForUsers($userIds);

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active FCM tokens found for users',
                'sent_count' => 0,
                'failed_count' => 0,
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $imageUrl);
    }

    /**
     * Send notification to all users via Topic Messaging.
     * This is the most efficient way to send broadcast notifications.
     */
    public function sendToAllUsers(
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): array {
        $topic = config('firebase.topics.all_users', 'celes_all_users');
        return $this->sendToTopic($topic, $title, $body, $data, $imageUrl);
    }

    /**
     * Send notification to a specific topic.
     */
    public function sendToTopic(
        string $topic,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): array {
        try {
            $bearerToken = $this->getBearerToken();
            $projectId = config('firebase.project_id');

            $message = $this->buildMessagePayload($title, $body, $data, $imageUrl);
            $message['topic'] = $topic;

            $payload = ['message' => $message];

            FCM::send($bearerToken, $projectId, $payload);

            Log::info('FCM Topic notification sent', [
                'topic' => $topic,
                'title' => $title,
            ]);

            return [
                'success' => true,
                'message' => 'Notification sent to topic successfully',
                'topic' => $topic,
            ];
        } catch (\Exception $e) {
            Log::error('FCM Topic notification failed', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send notification to topic',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification to multiple FCM tokens.
     */
    public function sendToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): array {
        $successCount = 0;
        $failedCount = 0;
        $failedTokens = [];

        $bearerToken = $this->getBearerToken();
        $projectId = config('firebase.project_id');

        foreach ($tokens as $token) {
            try {
                $message = $this->buildMessagePayload($title, $body, $data, $imageUrl);
                $message['token'] = $token;

                $payload = ['message' => $message];

                $client = new \Kedniko\FCM\Client();
                $result = $client->send($bearerToken, $projectId, $payload);

                if ($result === true) {
                    $successCount++;
                    Log::debug('FCM notification sent', [
                        'token' => substr($token, 0, 20) . '...',
                        'title' => $title,
                    ]);
                } else {
                    $failedCount++;
                    $failedTokens[] = $token;

                    Log::warning('FCM notification failed for token', [
                        'token' => substr($token, 0, 20) . '...',
                        'error' => $result,
                    ]);

                    // Clean up invalid token if it's unregistered
                    if ($this->isTokenInvalid($result)) {
                        $this->fcmTokenService->cleanupInvalidTokens([$token]);
                    }
                }
            } catch (\Exception $e) {
                $failedCount++;
                $failedTokens[] = $token;

                Log::warning('FCM notification exception for token', [
                    'token' => substr($token, 0, 20) . '...',
                    'error' => $e->getMessage(),
                ]);

                // Clean up invalid token if it's unregistered
                if ($this->isTokenInvalid($e->getMessage())) {
                    $this->fcmTokenService->cleanupInvalidTokens([$token]);
                }
            }
        }

        return [
            'success' => $successCount > 0,
            'message' => $failedCount === 0 
                ? 'All notifications sent successfully' 
                : "{$successCount} sent, {$failedCount} failed",
            'sent_count' => $successCount,
            'failed_count' => $failedCount,
            'failed_tokens' => $failedTokens,
        ];
    }

    /**
     * Subscribe token(s) to a topic.
     */
    public function subscribeToTopic(array $tokens, string $topic): array
    {
        try {
            $bearerToken = $this->getBearerToken();
            FCM::subscribeToTopic($bearerToken, $topic, $tokens);

            Log::info('Subscribed tokens to topic', [
                'topic' => $topic,
                'token_count' => count($tokens),
            ]);

            return [
                'success' => true,
                'message' => 'Subscribed to topic successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to subscribe to topic', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to subscribe to topic',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Unsubscribe token(s) from a topic.
     */
    public function unsubscribeFromTopic(array $tokens, string $topic): array
    {
        try {
            $bearerToken = $this->getBearerToken();
            FCM::unsubscribeFromTopic($bearerToken, $topic, $tokens);

            Log::info('Unsubscribed tokens from topic', [
                'topic' => $topic,
                'token_count' => count($tokens),
            ]);

            return [
                'success' => true,
                'message' => 'Unsubscribed from topic successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe from topic', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to unsubscribe from topic',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get FCM bearer token (cached for reuse).
     */
    protected function getBearerToken(): string
    {
        if ($this->bearerToken === null) {
            $credentialsPath = base_path(config('firebase.credentials_path', 'celes.json'));
            $keyContent = json_decode(file_get_contents($credentialsPath), true);
            $this->bearerToken = FCM::getBearerToken($keyContent);
        }

        return $this->bearerToken;
    }

    /**
     * Build FCM message payload.
     */
    protected function buildMessagePayload(
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null
    ): array {
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        if ($imageUrl) {
            $notification['image'] = $imageUrl;
        }

        $message = [
            'notification' => $notification,
        ];

        // Add custom data payload
        if (!empty($data)) {
            // Ensure all data values are strings (FCM requirement)
            $stringData = array_map(function ($value) {
                return is_array($value) ? json_encode($value) : (string) $value;
            }, $data);

            $message['data'] = $stringData;
        }

        // Android specific configuration
        $message['android'] = [
            'priority' => 'high',
            'notification' => [
                'click_action' => config('firebase.defaults.click_action', 'FLUTTER_NOTIFICATION_CLICK'),
                'color' => config('firebase.defaults.color', '#FF6B6B'),
                'sound' => config('firebase.defaults.sound', 'default'),
            ],
        ];

        // APNs (iOS) configuration
        $message['apns'] = [
            'payload' => [
                'aps' => [
                    'sound' => config('firebase.defaults.sound', 'default'),
                    'badge' => config('firebase.defaults.badge', 1),
                ],
            ],
        ];

        return $message;
    }

    /**
     * Check if error indicates an invalid/unregistered token.
     */
    protected function isTokenInvalid(string $errorMessage): bool
    {
        $invalidTokenPatterns = [
            'not a valid FCM registration token',
            'Requested entity was not found',
            'NotRegistered',
            'InvalidRegistration',
            'UNREGISTERED',
            'INVALID_ARGUMENT',
        ];

        foreach ($invalidTokenPatterns as $pattern) {
            if (stripos($errorMessage, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
