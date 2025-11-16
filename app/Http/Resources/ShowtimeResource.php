<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeResource extends JsonResource
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
            'date' => $this->date?->format('Y-m-d'),
            'start_time' => is_string($this->start_time) ? $this->start_time : $this->start_time?->format('H:i:s'),
            'end_time' => is_string($this->end_time) ? $this->end_time : $this->end_time?->format('H:i:s'),
            'price' => (float) $this->price,
            'status' => $this->status,
            'movie' => new MovieResource($this->whenLoaded('movie')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
