<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'address',
        'phone',
    ];

    /**
     * Get the user (partner) who owns this cinema
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all rooms in this cinema
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
