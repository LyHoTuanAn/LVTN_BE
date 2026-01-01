<?php

namespace App\Services\Notification;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FcmTokenService
{
    /**
     * Register/Update FCM token for a user.
     * If token exists for another user, it will be moved to current user.
     * If token exists for same user, it will be updated.
     */
    public function registerToken(int $userId, string $fcmToken): FcmToken
    {
        return DB::transaction(function () use ($userId, $fcmToken) {
            // Check if token already exists
            $existingToken = FcmToken::where('fcm_token', $fcmToken)->first();

            if ($existingToken) {
                // If exists for different user, transfer ownership
                if ($existingToken->user_id !== $userId) {
                    $existingToken->update([
                        'user_id' => $userId,
                        'is_active' => true,
                        'last_used_at' => now(),
                    ]);
                } else {
                    // Same user, just update
                    $existingToken->update([
                        'is_active' => true,
                        'last_used_at' => now(),
                    ]);
                }

                return $existingToken->fresh();
            }

            // Create new token
            return FcmToken::create([
                'user_id' => $userId,
                'fcm_token' => $fcmToken,
                'is_active' => true,
                'last_used_at' => now(),
            ]);
        });
    }

    /**
     * Delete FCM token (on logout).
     */
    public function deleteToken(string $fcmToken): bool
    {
        return FcmToken::where('fcm_token', $fcmToken)->delete() > 0;
    }

    /**
     * Delete all FCM tokens for a user (full logout from all devices).
     */
    public function deleteAllTokensForUser(int $userId): int
    {
        return FcmToken::where('user_id', $userId)->delete();
    }

    /**
     * Deactivate FCM token (soft disable).
     */
    public function deactivateToken(string $fcmToken): bool
    {
        return FcmToken::where('fcm_token', $fcmToken)
            ->update(['is_active' => false]) > 0;
    }

    /**
     * Get all active FCM tokens for a user.
     */
    public function getActiveTokensForUser(int $userId): array
    {
        return FcmToken::where('user_id', $userId)
            ->active()
            ->pluck('fcm_token')
            ->toArray();
    }

    /**
     * Get all active FCM tokens (for broadcast).
     */
    public function getAllActiveTokens(): array
    {
        return FcmToken::active()
            ->pluck('fcm_token')
            ->toArray();
    }

    /**
     * Get active tokens for multiple users.
     */
    public function getActiveTokensForUsers(array $userIds): array
    {
        return FcmToken::whereIn('user_id', $userIds)
            ->active()
            ->pluck('fcm_token')
            ->toArray();
    }

    /**
     * Clean up invalid/expired tokens.
     */
    public function cleanupInvalidTokens(array $invalidTokens): int
    {
        return FcmToken::whereIn('fcm_token', $invalidTokens)->delete();
    }

    /**
     * Get user IDs who have active tokens.
     */
    public function getUsersWithActiveTokens(): array
    {
        return FcmToken::active()
            ->distinct()
            ->pluck('user_id')
            ->toArray();
    }
}
