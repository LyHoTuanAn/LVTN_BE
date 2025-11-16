<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'showtime_id',
        'code',
        'is_paid',
        'voucher_id',
        'voucher_amount',
        'price',
        'total_price',
        'status',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'voucher_amount' => 'decimal:2',
            'price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Get the user who made this booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the showtime for this booking
     */
    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class);
    }

    /**
     * Get the voucher used in this booking
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Get all seats booked in this booking
     */
    public function seats(): BelongsToMany
    {
        return $this->belongsToMany(Seat::class, 'booking_seats');
    }
}
