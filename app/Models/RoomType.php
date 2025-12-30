<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image_id',
        'status',
    ];

    /**
     * Get the image for this room type
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_id');
    }

    /**
     * Get all rooms with this type
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_type_id');
    }

    /**
     * Scope for active room types
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
