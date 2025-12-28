<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $showtime = $this->resource['showtime'];
        $seats = $this->resource['seats'];
        $movie = $showtime->movie;

        return [
            'movie_title' => $movie->title ?? null,
            'genre' => $movie->genre ?? null,
            'showtime' => [
                'date' => $showtime->date?->format('Y-m-d'),
                'start_time' => is_string($showtime->start_time) 
                    ? $showtime->start_time 
                    : $showtime->start_time?->format('H:i:s'),
            ],
            'seats' => $seats->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                ];
            })->toArray(),
            'seat_count' => $this->resource['seat_count'],
            'price' => (float) $this->resource['price'],
            'voucher_code' => $this->resource['voucher_code'],
            'voucher_discount' => (float) $this->resource['voucher_discount'],
            'total_price' => (float) $this->resource['total_price'],
        ];
    }
}

