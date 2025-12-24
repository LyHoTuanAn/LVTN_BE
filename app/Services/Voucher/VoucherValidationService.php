<?php

namespace App\Services\Voucher;

use App\Models\Booking;
use App\Models\Voucher;
use Exception;

class VoucherValidationService
{
    /**
     * Complete validation for voucher usage by a user
     *
     * @param Voucher $voucher
     * @param int $userId
     * @param int|null $movieId
     * @return void
     * @throws Exception
     */
    public function validateVoucherForUser(Voucher $voucher, int $userId, ?int $movieId = null): void
    {
        // 1. Check if voucher is valid (basic checks)
        if (!$voucher->isValid()) {
            throw new Exception(__('errors.VOUCHER_INVALID'));
        }

        // 2. Check if voucher applies to user
        if (!$this->isVoucherApplicableForUser($voucher, $userId)) {
            throw new Exception(__('errors.VOUCHER_USER_NOT_APPLICABLE'));
        }

        // 3. Check if voucher applies to movie (if movie ID provided)
        if ($movieId !== null && !$this->isVoucherApplicableForMovie($voucher, $movieId)) {
            throw new Exception(__('errors.VOUCHER_MOVIE_NOT_APPLICABLE'));
        }

        // 4. Check per-user limit
        if (!$this->canUserUseVoucher($voucher, $userId)) {
            throw new Exception(__('errors.VOUCHER_PER_USER_LIMIT_EXCEEDED'));
        }
    }

    /**
     * Check if user can use voucher (per-user limit check)
     *
     * @param Voucher $voucher
     * @param int $userId
     * @return bool
     */
    public function canUserUseVoucher(Voucher $voucher, int $userId): bool
    {
        // If no per_user_limit set, user can use unlimited times
        if ($voucher->per_user_limit === null) {
            return true;
        }

        $userUsageCount = $this->getUserVoucherUsageCount($voucher->id, $userId);

        return $userUsageCount < $voucher->per_user_limit;
    }

    /**
     * Check if voucher applies to a specific user
     *
     * @param Voucher $voucher
     * @param int $userId
     * @return bool
     */
    public function isVoucherApplicableForUser(Voucher $voucher, int $userId): bool
    {
        return $voucher->isApplicableForUser($userId);
    }

    /**
     * Check if voucher applies to a specific movie
     *
     * @param Voucher $voucher
     * @param int $movieId
     * @return bool
     */
    public function isVoucherApplicableForMovie(Voucher $voucher, int $movieId): bool
    {
        return $voucher->isApplicableForMovie($movieId);
    }

    /**
     * Get user's usage count for a voucher
     *
     * @param int $voucherId
     * @param int $userId
     * @return int
     */
    public function getUserVoucherUsageCount(int $voucherId, int $userId): int
    {
        return Booking::where('voucher_id', $voucherId)
            ->where('user_id', $userId)
            ->where('status', '!=', 'canceled')
            ->count();
    }

    /**
     * Calculate discount amount based on voucher type
     *
     * @param Voucher $voucher
     * @param float $price
     * @return float
     */
    public function calculateDiscountAmount(Voucher $voucher, float $price): float
    {
        if ($voucher->type === 'percentage') {
            $discount = $price * ($voucher->amount / 100);
            // Cap at price (can't have negative total)
            return min($discount, $price);
        }

        // Fixed amount
        // Cap at price (can't have negative total)
        return min((float) $voucher->amount, $price);
    }

    /**
     * Parse comma-separated IDs string to array
     *
     * @param string $idsString
     * @return array
     */
    protected function parseIds(string $idsString): array
    {
        return array_filter(
            array_map('trim', explode(',', $idsString)),
            fn($id) => is_numeric($id) && $id > 0
        );
    }
}

