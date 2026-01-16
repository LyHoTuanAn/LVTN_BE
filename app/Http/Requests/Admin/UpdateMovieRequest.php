<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:600',
            'release_date' => 'required|date',
            'status' => 'nullable|in:COMING_SOON,NOW_SHOWING',
            'genre' => 'nullable|string|max:255',
            'age_classification' => 'required|in:P,K,T13,T16,T18,C',
            'language' => 'nullable|string|max:100',
            'directors' => 'nullable|array',
            'directors.*.name' => 'nullable|string|max:255',
            'directors.*.avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'actors' => 'nullable|array',
            'actors.*.name' => 'nullable|string|max:255',
            'actors.*.avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'trailer' => 'nullable|mimes:mp4,mov,avi,wmv|max:102400',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => __('Title'),
            'description' => __('Description'),
            'duration' => __('Duration'),
            'release_date' => __('Release Date'),
            'status' => __('Status'),
            'genre' => __('Genre'),
            'age_classification' => __('Age Classification'),
            'language' => __('Language'),
            'directors' => __('Directors'),
            'actors' => __('Actors'),
            'poster' => __('Poster'),
            'trailer' => __('Trailer'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('Title is required'),
            'title.max' => __('Title cannot exceed 255 characters'),
            'duration.required' => __('Duration is required'),
            'duration.integer' => __('Duration must be a number'),
            'duration.min' => __('Duration must be at least 1 minute'),
            'duration.max' => __('Duration cannot exceed 600 minutes'),
            'release_date.required' => __('Release date is required'),
            'release_date.date' => __('Invalid release date format'),
            'status.required' => __('Status is required'),
            'status.in' => __('Invalid status value'),
            'age_classification.required' => __('Age classification is required'),
            'age_classification.in' => __('Invalid age classification value'),
            'language.max' => __('Language cannot exceed 100 characters'),
            'poster.image' => __('Poster must be an image'),
            'poster.mimes' => __('Poster must be a file of type: jpeg, png, jpg, gif, webp'),
            'poster.max' => __('Poster cannot be larger than 5MB'),
            'trailer.mimes' => __('Trailer must be a file of type: mp4, mov, avi, wmv'),
            'trailer.max' => __('Trailer cannot be larger than 100MB'),
        ];
    }
}

