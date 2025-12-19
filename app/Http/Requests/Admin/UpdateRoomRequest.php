<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'seat_count' => 'required|integer|min:1|max:500',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('Room Name'),
            'seat_count' => __('Seat Count'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Room name is required'),
            'name.max' => __('Room name cannot exceed 100 characters'),
            'seat_count.required' => __('Seat count is required'),
            'seat_count.integer' => __('Seat count must be a number'),
            'seat_count.min' => __('Seat count must be at least 1'),
            'seat_count.max' => __('Seat count cannot exceed 500'),
        ];
    }
}
