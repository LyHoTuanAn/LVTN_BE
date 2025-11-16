<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'otp_code',
        'type',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the OTP
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include valid (not expired) OTPs
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope a query to only include unverified OTPs
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope a query to filter by type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by email
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
}
