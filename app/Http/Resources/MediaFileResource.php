<?php

namespace App\Http\Resources;

use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mediaService = app(MediaService::class);
        
        return [
            'id' => $this->id,
            'folder_id' => $this->folder_id,
            'user_id' => $this->user_id,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'url' => $mediaService->getUrl($this->resource),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'type' => $this->type,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
