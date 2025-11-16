<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaFile extends Model
{
    protected $fillable = [
        'folder_id',
        'user_id',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    /**
     * Get the folder that contains this file
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    /**
     * Get the user who uploaded this file
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all users who use this as avatar
     */
    public function usersAsAvatar(): HasMany
    {
        return $this->hasMany(User::class, 'avatar_id');
    }
}
