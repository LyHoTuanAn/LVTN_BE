<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cinema_id',
        'room_type_id',
        'name',
        'seat_count',
    ];

    protected $casts = [
        'seat_count' => 'integer',
        'room_type_id' => 'integer',
        'cinema_id' => 'integer',
    ];

    /**
     * Get the cinema that owns this room
     */
    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class);
    }

    /**
     * Get the type of this room
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get all seats in this room
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Get all showtimes in this room
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }
}
