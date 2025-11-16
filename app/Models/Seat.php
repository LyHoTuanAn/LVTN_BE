<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Seat extends Model
{
    protected $fillable = [
        'room_id',
        'row',
        'number',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
        ];
    }

    /**
     * Get the room that contains this seat
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all bookings that include this seat
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_seats');
    }
}
