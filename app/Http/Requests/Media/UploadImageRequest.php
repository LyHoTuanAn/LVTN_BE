<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authentication will be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:10240', // 10MB max
            ],
            'folder_id' => [
                'nullable',
                'integer',
                'exists:media_folders,id',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.required' => __('validation.required', ['attribute' => 'image']),
            'image.image' => __('validation.image', ['attribute' => 'image']),
            'image.mimes' => __('validation.mimes', ['attribute' => 'image', 'values' => 'jpeg, jpg, png, gif, webp']),
            'image.max' => __('validation.max.file', ['attribute' => 'image', 'max' => '10MB']),
            'folder_id.exists' => __('validation.exists', ['attribute' => 'folder_id']),
        ];
    }
}

