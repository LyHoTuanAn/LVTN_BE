<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteMovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $this->duration,
            'release_date' => $this->release_date?->format('Y-m-d'),
            'status' => $this->getComputedStatus(), 
            'status_label' => $this->status_label,
            'genre' => $this->genre,
            'age_classification' => $this->age_classification,
            'language' => $this->language,
            'poster' => new MediaFileResource($this->whenLoaded('poster')),
            'trailer' => new MediaFileResource($this->whenLoaded('trailer')),
            'is_favorited' => true,
            'favorited_at' => $this->pivot?->created_at?->toDateTimeString(),
        ];
    }
}
