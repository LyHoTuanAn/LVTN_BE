<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Actor extends Model
{
    protected $fillable = [
        'name',
        'avatar_id',
    ];

    /**
     * Get the avatar image
     */
    public function avatar(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'avatar_id');
    }

    /**
     * Get all movies with this actor
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class)->withPivot('role');
    }
}
