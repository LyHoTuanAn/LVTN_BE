<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'applies_to' => $this->applies_to,
            'valid_from' => $this->valid_from?->toDateTimeString(),
            'valid_to' => $this->valid_to?->toDateTimeString(),
            'status' => $this->status,
        ];
    }
}
