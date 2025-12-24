<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'amount',
        'usage_limit',
        'per_user_limit',
        'used_count',
        'applies_to',
        'only_for_user',
        'only_for_movie',
        'valid_from',
        'valid_to',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'usage_limit' => 'integer',
            'per_user_limit' => 'integer',
            'used_count' => 'integer',
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
        ];
    }

    /**
     * Get all bookings that used this voucher
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if voucher is valid (basic validation)
     */
    public function isValid(): bool
    {
        $now = now();
        return $this->status === 'active'
            && $now->gte($this->valid_from)
            && $now->lte($this->valid_to)
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    /**
     * Get user's usage count for this voucher
     */
    public function getUserUsageCount(int $userId): int
    {
        return $this->bookings()
            ->where('user_id', $userId)
            ->where('status', '!=', 'canceled')
            ->count();
    }

    /**
     * Check if voucher applies to a specific user
     */
    public function isApplicableForUser(int $userId): bool
    {
        if ($this->applies_to === 'all_users') {
            return true;
        }

        if ($this->applies_to === 'specific_users') {
            if (!$this->only_for_user) {
                return false;
            }

            $allowedUserIds = array_filter(
                array_map('trim', explode(',', $this->only_for_user)),
                fn($id) => is_numeric($id) && $id > 0
            );

            return in_array((string) $userId, $allowedUserIds);
        }

        return true;
    }

    /**
     * Check if voucher applies to a specific movie
     */
    public function isApplicableForMovie(int $movieId): bool
    {
        if ($this->applies_to === 'all_users' || $this->applies_to === 'specific_users') {
            return true;
        }

        if ($this->applies_to === 'specific_movies') {
            if (!$this->only_for_movie) {
                return false;
            }

            $allowedMovieIds = array_filter(
                array_map('trim', explode(',', $this->only_for_movie)),
                fn($id) => is_numeric($id) && $id > 0
            );

            return in_array((string) $movieId, $allowedMovieIds);
        }

        return true;
    }
}
