<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShowtimeRequest extends FormRequest
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
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price' => 'required|integer|min:0|max:999999999',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ];
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
            'date.after_or_equal' => __('Date must be today or later'),
            'start_time.required' => __('Start time is required'),
            'start_time.date_format' => __('Invalid start time format (HH:MM)'),
            'end_time.required' => __('End time is required'),
            'end_time.date_format' => __('Invalid end time format (HH:MM)'),
            'end_time.after' => __('End time must be after start time'),
            'price.required' => __('Price is required'),
            'price.integer' => __('Price must be a whole number'),
            'price.min' => __('Price cannot be negative'),
            'status.required' => __('Status is required'),
            'status.in' => __('Invalid status value'),
        ];
    }
}
