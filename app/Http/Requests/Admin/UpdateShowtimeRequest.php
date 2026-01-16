<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Movie;

class UpdateShowtimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'price' => 'required|integer|min:0|max:999999999',
            'status' => 'required|in:scheduled,ongoing,completed',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $startTime = $this->input('start_time');
            $endTime = $this->input('end_time');
            $movieId = $this->input('movie_id');

            if (!$startTime || !$endTime) {
                return;
            }

            // Parse times
            $start = \Carbon\Carbon::createFromFormat('H:i', $startTime);
            $end = \Carbon\Carbon::createFromFormat('H:i', $endTime);

            // If end_time is less than start_time, it means end_time is next day
            if ($end->lt($start)) {
                // Add 24 hours to end_time to get the actual end time
                $end->addDay();
            }

            // Calculate duration in minutes
            $duration = $start->diffInMinutes($end);

            // Get movie duration
            $movie = Movie::find($movieId);
            if ($movie && $movie->duration) {
                // Allow some tolerance (±5 minutes) for rounding
                $expectedDuration = $movie->duration;
                $tolerance = 5;

                if ($duration < $expectedDuration - $tolerance || $duration > $expectedDuration + $tolerance) {
                    $validator->errors()->add(
                        'end_time',
                        __('End time does not match movie duration. Expected duration: :duration minutes', [
                            'duration' => $expectedDuration
                        ])
                    );
                }
            } else {
                // If no movie duration, just check that end_time is after start_time
                // (either same day or next day)
                // This case is already handled by the logic above
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize price: remove dots and ensure it's an integer
        if ($this->has('price')) {
            $price = $this->input('price');
            // Remove dots (thousand separators) and any non-numeric characters except digits
            $price = preg_replace('/[^\d]/', '', (string) $price);
            $this->merge([
                'price' => $price ? (int) $price : null,
            ]);
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'movie_id' => __('Movie'),
            'room_id' => __('Room'),
            'date' => __('Date'),
            'start_time' => __('Start Time'),
            'end_time' => __('End Time'),
            'price' => __('Price'),
            'status' => __('Status'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'movie_id.required' => __('Please select a movie'),
            'movie_id.exists' => __('Selected movie does not exist'),
            'room_id.required' => __('Please select a room'),
            'room_id.exists' => __('Selected room does not exist'),
            'date.required' => __('Date is required'),
            'date.date' => __('Invalid date format'),
            'start_time.required' => __('Start time is required'),
            'start_time.date_format' => __('Invalid start time format (HH:MM)'),
            'end_time.required' => __('End time is required'),
            'end_time.date_format' => __('Invalid end time format (HH:MM)'),
            'price.required' => __('Price is required'),
            'price.integer' => __('Price must be a whole number'),
            'price.min' => __('Price cannot be negative'),
            'status.required' => __('Status is required'),
            'status.in' => __('Invalid status value'),
        ];
    }
}
