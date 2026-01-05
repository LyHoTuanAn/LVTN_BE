<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
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
     *
     * @param int $userId
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @param string $type Notification type (e.g., 'booking_paid', 'new_movie')
     * @param string|null $relatedType Related entity type (e.g., 'booking', 'movie')
     * @param int|null $relatedId Related entity ID
     * @param bool $saveToDb Whether to save notification to database
     * @return array
     */
    public function sendToUser(
        int $userId,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $type = 'general',
        ?string $relatedType = null,
        ?int $relatedId = null,
        bool $saveToDb = true
    ): array {
        // Save notification to database
        $notification = null;
        if ($saveToDb) {
            $notification = $this->saveNotification(
                $userId,
                $title,
                $body,
                Notification::CHANNEL_FCM,
                $type,
                $imageUrl,
                $relatedType,
                $relatedId,
                $data
            );
        }

        $tokens = $this->fcmTokenService->getActiveTokensForUser($userId);

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active FCM tokens found for user',
                'sent_count' => 0,
                'failed_count' => 0,
                'notification_id' => $notification?->id,
            ];
        }

        $result = $this->sendToTokens($tokens, $title, $body, $data, $imageUrl);
        $result['notification_id'] = $notification?->id;

        return $result;
    }

    /**
     * Send notification to multiple users.
     *
     * @param array $userIds
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @param string $type Notification type
     * @param string|null $relatedType Related entity type
     * @param int|null $relatedId Related entity ID
     * @param bool $saveToDb Whether to save notifications to database
     * @return array
     */
    public function sendToUsers(
        array $userIds,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $type = 'general',
        ?string $relatedType = null,
        ?int $relatedId = null,
        bool $saveToDb = true
    ): array {
        // Save notifications to database for each user
        $notificationIds = [];
        if ($saveToDb) {
            $notificationIds = $this->saveNotificationsForUsers(
                $userIds,
                $title,
                $body,
                Notification::CHANNEL_FCM,
                $type,
                $imageUrl,
                $relatedType,
                $relatedId,
                $data
            );
        }

        $tokens = $this->fcmTokenService->getActiveTokensForUsers($userIds);

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active FCM tokens found for users',
                'sent_count' => 0,
                'failed_count' => 0,
                'notification_ids' => $notificationIds,
            ];
        }

        $result = $this->sendToTokens($tokens, $title, $body, $data, $imageUrl);
        $result['notification_ids'] = $notificationIds;

        return $result;
    }

    /**
     * Send notification to all users via Topic Messaging.
     * This is the most efficient way to send broadcast notifications.
     *
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @param string $type Notification type
     * @param string|null $relatedType Related entity type
     * @param int|null $relatedId Related entity ID
     * @param bool $saveToDb Whether to save notifications to database for all users
     * @return array
     */
    public function sendToAllUsers(
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $type = 'general',
        ?string $relatedType = null,
        ?int $relatedId = null,
        bool $saveToDb = true
    ): array {
        // Save notifications to database for all customers
        $notificationCount = 0;
        if ($saveToDb) {
            $notificationCount = $this->saveNotificationsForAllUsers(
                $title,
                $body,
                Notification::CHANNEL_FCM,
                $type,
                $imageUrl,
                $relatedType,
                $relatedId,
                $data
            );
        }

        $topic = config('firebase.topics.all_users', 'celes_all_users');
        $result = $this->sendToTopic($topic, $title, $body, $data, $imageUrl);
        $result['notifications_saved'] = $notificationCount;

        return $result;
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

    /*
    |--------------------------------------------------------------------------
    | Database Methods - Save and Manage Notifications
    |--------------------------------------------------------------------------
    */

    /**
     * Save a notification to database for a single user.
     */
    public function saveNotification(
        int $userId,
        string $title,
        string $body,
        string $channel = 'fcm',
        string $type = 'general',
        ?string $imageUrl = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'image_url' => $imageUrl,
            'type' => $type,
            'channel' => $channel,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'data' => !empty($data) ? $data : null,
            'is_read' => false,
        ]);
    }

    /**
     * Save notifications to database for multiple users.
     *
     * @return array Array of created notification IDs
     */
    public function saveNotificationsForUsers(
        array $userIds,
        string $title,
        string $body,
        string $channel = 'fcm',
        string $type = 'general',
        ?string $imageUrl = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
        array $data = []
    ): array {
        $notificationIds = [];
        $now = now();

        $insertData = [];
        foreach ($userIds as $userId) {
            $insertData[] = [
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'image_url' => $imageUrl,
                'type' => $type,
                'channel' => $channel,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'data' => !empty($data) ? json_encode($data) : null,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Batch insert for performance
        Notification::insert($insertData);

        // Get the created notification IDs
        $notifications = Notification::where('title', $title)
            ->where('created_at', '>=', $now->subSecond())
            ->whereIn('user_id', $userIds)
            ->pluck('id')
            ->toArray();

        return $notifications;
    }

    /**
     * Save notifications to database for all users (customers only).
     *
     * @return int Number of notifications created
     */
    public function saveNotificationsForAllUsers(
        string $title,
        string $body,
        string $channel = 'fcm',
        string $type = 'general',
        ?string $imageUrl = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
        array $data = []
    ): int {
        // Get all customer users (role with name 'customer')
        $customerUserIds = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->pluck('id')->toArray();

        if (empty($customerUserIds)) {
            return 0;
        }

        $now = now();
        $insertData = [];

        foreach ($customerUserIds as $userId) {
            $insertData[] = [
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'image_url' => $imageUrl,
                'type' => $type,
                'channel' => $channel,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'data' => !empty($data) ? json_encode($data) : null,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Batch insert in chunks for performance
        $chunks = array_chunk($insertData, 500);
        foreach ($chunks as $chunk) {
            Notification::insert($chunk);
        }

        return count($insertData);
    }

    /*
    |--------------------------------------------------------------------------
    | API Methods - Get and Manage User Notifications
    |--------------------------------------------------------------------------
    */

    /**
     * Get notifications for a user with pagination.
     */
    public function getUserNotifications(
        int $userId,
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        // Filter by read status
        if (isset($filters['is_read'])) {
            $query->where('is_read', filter_var($filters['is_read'], FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        return $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(int $notificationId, int $userId): bool
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    /**
     * Get a single notification by ID.
     */
    public function getNotificationById(int $notificationId, int $userId): ?Notification
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
    }
}
