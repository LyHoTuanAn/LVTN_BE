<?php

namespace App\Services\News;

use App\Models\News;
use App\Services\Media\MediaService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsService
{
    public function __construct(protected MediaService $mediaService)
    {
    }

    /**
     * Paginate news with filters.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = News::query()->with(['thumbnail', 'author'])->orderByDesc('created_at');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title_en', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('title_vi', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a news item with optional media uploads.
     */
    public function create(array $data, int $authorId, ?UploadedFile $thumbnail = null, array $inlineImages = []): News
    {
        return DB::transaction(function () use ($data, $authorId, $thumbnail, $inlineImages) {
            $data['author_id'] = $authorId;
            $data['slug'] = $this->prepareSlug($data);

            if ($thumbnail) {
                $thumbnailFile = $this->mediaService->uploadImage($thumbnail, $authorId);
                $data['thumbnail_id'] = $thumbnailFile->id;
            }

            $uploadedInlineIds = [];
            foreach ($inlineImages as $inlineImage) {
                if ($inlineImage instanceof UploadedFile) {
                    $media = $this->mediaService->uploadImage($inlineImage, $authorId);
                    $uploadedInlineIds[] = $media->id;
                }
            }

            if (!empty($uploadedInlineIds)) {
                $data['inline_image_ids'] = $uploadedInlineIds;
            }

            return News::create($data);
        });
    }

    protected function prepareSlug(array $data): string
    {
        // Ưu tiên slug từ Title (Vietnamese), nếu không có thì dùng English
        $base = $data['slug'] ?? $data['title_vi'] ?? $data['title_en'] ?? Str::random(8);
        $slug = Str::slug($base);

        $original = $slug;
        $counter = 1;

        while (News::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

