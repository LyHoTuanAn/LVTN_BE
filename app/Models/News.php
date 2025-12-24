<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_vi',
        'slug',
        'summary_en',
        'summary_vi',
        'content_en',
        'content_vi',
        'thumbnail_id',
        'author_id',
        'status',
        'inline_image_ids',
    ];

    protected $casts = [
        'inline_image_ids' => 'array',
    ];

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'thumbnail_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

