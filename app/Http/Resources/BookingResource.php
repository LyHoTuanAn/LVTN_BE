<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'price' => (float) $this->price,
            'total_price' => (float) $this->total_price,
            'voucher_amount' => (float) $this->voucher_amount,
            'payment_method' => $this->payment_method,
            'user' => new UserResource($this->whenLoaded('user')),
            'showtime' => new ShowtimeResource($this->whenLoaded('showtime')),
            'seats' => SeatResource::collection($this->whenLoaded('seats')),
            'voucher' => new VoucherResource($this->whenLoaded('voucher')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
