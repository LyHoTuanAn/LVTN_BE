<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            'showtimes' => ShowtimeResource::collection($this->whenLoaded('showtimes')),
            'directors' => DirectorMovieResource::collection($this->whenLoaded('directors')),
            'actors' => ActorMovieResource::collection($this->whenLoaded('actors')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'is_favorited' => $this->isFavoritedByCurrentUser(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Check if movie is favorited by current authenticated user
     */
    protected function isFavoritedByCurrentUser(): bool
    {
        $user = auth('api')->user();
        
        if (!$user) {
            return false;
        }

        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }
}
