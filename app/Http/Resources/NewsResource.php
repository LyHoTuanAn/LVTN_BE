<?php

namespace App\Http\Resources;

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

        return [
            'id' => $this->id,
            'title' => $isVi ? $this->title_vi : $this->title_en,
            'slug' => $this->slug,
            'summary' => $isVi ? $this->summary_vi : $this->summary_en,
            'content' => $isVi ? $this->content_vi : $this->content_en,
            'status' => $this->status,
            'thumbnail' => new MediaFileResource($this->whenLoaded('thumbnail')),
            'author' => new UserResource($this->whenLoaded('author')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

