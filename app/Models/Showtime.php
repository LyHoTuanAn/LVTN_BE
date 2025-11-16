<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'movie_id',
        'room_id',
        'date',
        'start_time',
        'end_time',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'string',
            'end_time' => 'string',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the movie for this showtime
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Get the room for this showtime
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all bookings for this showtime
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
