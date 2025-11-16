<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSeat extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'seat_id',
    ];

    /**
     * Get the booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the seat
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}
