<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'image_url',
        'type',
        'channel',
        'related_type',
        'related_id',
        'data',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Notification types constants
     */
    const TYPE_GENERAL = 'general';
    const TYPE_BOOKING_CREATED = 'booking_created';
    const TYPE_BOOKING_PAID = 'booking_paid';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_COMPLETED = 'booking_completed';
    const TYPE_NEW_MOVIE = 'new_movie';
    const TYPE_MOVIE_PREMIERE = 'movie_premiere';
    const TYPE_NEW_VOUCHER = 'new_voucher';
    const TYPE_PROMOTION = 'promotion';

    /**
     * Channel constants
     */
    const CHANNEL_FCM = 'fcm';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_TELEGRAM = 'telegram';

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related entity (polymorphic).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope: By type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: By channel
     */
    public function scopeOfChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}
