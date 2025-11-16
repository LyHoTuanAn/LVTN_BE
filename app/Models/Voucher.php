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
     * Check if voucher is valid
     */
    public function isValid(): bool
    {
        $now = now();
        return $this->status === 'active'
            && $now->gte($this->valid_from)
            && $now->lte($this->valid_to)
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }
}
