<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class FcmTokenRequest extends FormRequest
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
            'fcm_token' => ['required', 'string', 'min:100', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'fcm_token.required' => __('validation.required', ['attribute' => 'FCM token']),
            'fcm_token.min' => __('validation.min.string', ['attribute' => 'FCM token', 'min' => 100]),
            'fcm_token.max' => __('validation.max.string', ['attribute' => 'FCM token', 'max' => 500]),
        ];
    }
}
