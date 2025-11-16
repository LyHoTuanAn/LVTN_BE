<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteMovie extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'movie_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
