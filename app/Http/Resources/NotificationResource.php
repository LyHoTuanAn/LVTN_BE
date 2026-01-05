<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'body' => $this->body,
            'image_url' => $this->image_url,
            'type' => $this->type,
            'channel' => $this->channel,
            'related_type' => $this->related_type,
            'related_id' => $this->related_id,
            'data' => $this->data,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
