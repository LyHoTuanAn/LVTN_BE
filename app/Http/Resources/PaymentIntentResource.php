<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentIntentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Handle both array and object inputs
        $data = is_array($this->resource) ? $this->resource : (array) $this->resource;

        return [
            'id' => $data['id'] ?? null,
            'client_secret' => $data['client_secret'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'currency' => $data['currency'] ?? 'vnd',
            'status' => $data['status'] ?? null,
            'booking_id' => $data['booking_id'] ?? null,
            'booking_code' => $data['booking_code'] ?? null,
        ];
    }
}
