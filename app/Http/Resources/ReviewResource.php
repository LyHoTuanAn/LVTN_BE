<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'movie' => $this->when(
                $this->relationLoaded('movie'),
                fn() => [
                    'id' => $this->movie->id,
                    'title' => $this->movie->title,
                    'poster' => new MediaFileResource($this->movie->whenLoaded('poster')),
                ]
            ),
            'booking' => $this->when(
                $this->relationLoaded('booking'),
                fn() => $this->booking ? [
                    'id' => $this->booking->id,
                    'code' => $this->booking->code,
                ] : null
            ),
            'media' => new MediaFileResource($this->whenLoaded('media')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
