<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{
    use SoftDeletes;

    /**
     * Showtime status constants
     * 
     * SCHEDULED: Suất chiếu đã lên lịch, chưa bắt đầu
     * ONGOING: Suất chiếu đang diễn ra
     * COMPLETED: Suất chiếu đã kết thúc
     * CANCELLED: Suất chiếu đã bị hủy
     */
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Đã lên lịch',
            self::STATUS_ONGOING => 'Đang chiếu',
            self::STATUS_COMPLETED => 'Đã hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

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
