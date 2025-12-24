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
            $data['slug'] = $this->prepareSlug($data, null);

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

    /**
     * Get news by ID
     */
    public function getById(int $id): ?News
    {
        return News::with(['thumbnail', 'author'])->find($id);
    }

    /**
     * Update a news item with optional media uploads.
     */
    public function update(int $id, array $data, ?UploadedFile $thumbnail = null, array $inlineImages = []): bool
    {
        return DB::transaction(function () use ($id, $data, $thumbnail, $inlineImages) {
            $news = News::find($id);
            if (!$news) {
                return false;
            }

            // Update slug if title_vi changed
            if (isset($data['title_vi']) && $data['title_vi'] !== $news->title_vi) {
                $data['slug'] = $this->prepareSlug($data, $id);
            }

            if ($thumbnail) {
                // Delete old thumbnail if exists
                if ($news->thumbnail_id) {
                    $this->mediaService->deleteMediaFile($news->thumbnail_id);
                }
                $thumbnailFile = $this->mediaService->uploadImage($thumbnail, $news->author_id);
                $data['thumbnail_id'] = $thumbnailFile->id;
            }

            // Handle inline images - append to existing ones
            $existingInlineIds = $news->inline_image_ids ?? [];
            $uploadedInlineIds = [];
            foreach ($inlineImages as $inlineImage) {
                if ($inlineImage instanceof UploadedFile) {
                    $media = $this->mediaService->uploadImage($inlineImage, $news->author_id);
                    $uploadedInlineIds[] = $media->id;
                }
            }

            if (!empty($uploadedInlineIds)) {
                $data['inline_image_ids'] = array_merge($existingInlineIds, $uploadedInlineIds);
            }

            return $news->update($data);
        });
    }

    protected function prepareSlug(array $data, ?int $excludeId = null): string
    {
        // Ưu tiên slug từ Title (Vietnamese), nếu không có thì dùng English
        $base = $data['slug'] ?? $data['title_vi'] ?? $data['title_en'] ?? Str::random(8);
        $slug = Str::slug($base);

        $original = $slug;
        $counter = 1;

        $query = News::withTrashed()->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
            $query = News::withTrashed()->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Delete a news item (soft delete).
     */
    public function delete(int $id): bool
    {
        $news = News::find($id);
        
        if (!$news) {
            return false;
        }

        // Delete thumbnail if exists
        if ($news->thumbnail_id) {
            $this->mediaService->deleteMediaFile($news->thumbnail_id);
        }

        // Delete inline images if exist
        if (!empty($news->inline_image_ids)) {
            foreach ($news->inline_image_ids as $mediaId) {
                $this->mediaService->deleteMediaFile($mediaId);
            }
        }

        return $news->delete();
    }
}

