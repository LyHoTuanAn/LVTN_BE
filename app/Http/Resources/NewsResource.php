<?php

namespace App\Http\Resources;

use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $isVi = $locale === 'vi';

        // Get content based on locale
        $content = $isVi ? $this->content_vi : $this->content_en;

        // Process base64 images in content and convert to URLs
        if ($content && $this->author_id) {
            $mediaService = app(MediaService::class);
            $content = $mediaService->processContentImages($content, $this->author_id);
        }

        return [
            'id' => $this->id,
            'title' => $isVi ? $this->title_vi : $this->title_en,
            'slug' => $this->slug,
            'summary' => $isVi ? $this->summary_vi : $this->summary_en,
            'content' => $content,
            'status' => $this->status,
            'thumbnail' => new MediaFileResource($this->whenLoaded('thumbnail')),
            'author' => new UserResource($this->whenLoaded('author')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

