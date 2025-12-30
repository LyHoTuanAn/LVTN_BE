<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'name' => $this->name,
            'seat_count' => $this->seat_count,
            'cinema' => new CinemaResource($this->whenLoaded('cinema')),
            'room_type' => new RoomTypeResource($this->whenLoaded('roomType')),
            'seats' => SeatResource::collection($this->whenLoaded('seats')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
