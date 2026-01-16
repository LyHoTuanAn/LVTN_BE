<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use SoftDeletes;

    /**
     * Movie status constants
     * 
     * COMING_SOON: Sắp ra mắt - Khi release_date > today (không phụ thuộc vào showtimes)
     * NOW_SHOWING: Đang chiếu - Khi release_date <= today (không phụ thuộc vào showtimes)
     */
    public const STATUS_COMING_SOON = 'COMING_SOON';
    public const STATUS_NOW_SHOWING = 'NOW_SHOWING';

    /**
     * Showtime status constants (reference from Showtime model)
     */
    public const SHOWTIME_SCHEDULED = 'scheduled';
    public const SHOWTIME_ONGOING = 'ongoing';
    public const SHOWTIME_COMPLETED = 'completed';

    protected $fillable = [
        'title',
        'description',
        'duration',
        'release_date',
        'status',
        'genre',
        'age_classification',
        'language',
        'poster_id',
        'trailer_id',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'duration' => 'integer',
        ];
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_COMING_SOON => 'Sắp ra mắt',
            self::STATUS_NOW_SHOWING => 'Đang chiếu',
        ];
    }

    /**
     * Get computed status based on release_date
     * 
     * Logic:
     * - NOW_SHOWING: Khi release_date <= today (không phụ thuộc vào showtimes)
     * - COMING_SOON: Khi release_date > today (không phụ thuộc vào showtimes)
     * 
     * Status của phim chỉ dựa trên ngày phát hành so với thời điểm hiện tại,
     * không phụ thuộc vào việc có hay không có suất chiếu.
     */
    public function getComputedStatus(): string
    {
        if (!$this->release_date) {
            // Nếu không có release_date, mặc định là COMING_SOON
            return self::STATUS_COMING_SOON;
        }

        $today = now()->startOfDay();
        $releaseDate = \Carbon\Carbon::parse($this->release_date)->startOfDay();

        // Nếu release_date <= today → NOW_SHOWING
        // Nếu release_date > today → COMING_SOON
        return $releaseDate->lessThanOrEqualTo($today) 
            ? self::STATUS_NOW_SHOWING 
            : self::STATUS_COMING_SOON;
    }

    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->getComputedStatus()] ?? $this->status;
    }

    /**
     * Age classification options
     */
    public static function getAgeClassifications(): array
    {
        return [
            'P' => 'All Ages',
            'K' => 'Children (Parental Guidance)',
            'T13' => '13+',
            'T16' => '16+',
            'T18' => '18+',
            'C' => 'Prohibited',
        ];
    }

    /**
     * Get the poster image
     */
    public function poster(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'poster_id');
    }

    /**
     * Get the trailer video
     */
    public function trailer(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'trailer_id');
    }

    /**
     * Get all showtimes for this movie
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }

    /**
     * Get all bookings for this movie through showtimes
     */
    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Showtime::class);
    }

    /**
     * Get all reviews for this movie
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all users who favorited this movie
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_movies');
    }

    /**
     * Get all users who favorited this movie (with pivot data)
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_movies')
            ->withPivot('created_at');
    }

    /**
     * Get all directors for this movie
     */
    public function directors(): HasMany
    {
        return $this->hasMany(DirectorMovie::class);
    }

    /**
     * Get all actors for this movie
     */
    public function actors(): HasMany
    {
        return $this->hasMany(ActorMovie::class);
    }
}

