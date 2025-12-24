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
            'per_user_limit' => $this->per_user_limit,
            'used_count' => $this->used_count,
            'applies_to' => $this->applies_to,
            'only_for_user' => $this->only_for_user ? $this->parseCommaSeparatedIds($this->only_for_user) : null,
            'only_for_movie' => $this->only_for_movie ? $this->parseCommaSeparatedIds($this->only_for_movie) : null,
            'valid_from' => $this->valid_from?->toDateTimeString(),
            'valid_to' => $this->valid_to?->toDateTimeString(),
            'status' => $this->status,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Parse comma-separated IDs string to array of integers
     */
    protected function parseCommaSeparatedIds(?string $idsString): ?array
    {
        if (!$idsString) {
            return null;
        }

        return array_map(
            'intval',
            array_filter(
                array_map('trim', explode(',', $idsString)),
                fn($id) => is_numeric($id) && $id > 0
            )
        );
    }
}
