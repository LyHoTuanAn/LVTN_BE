<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use SoftDeletes;

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

