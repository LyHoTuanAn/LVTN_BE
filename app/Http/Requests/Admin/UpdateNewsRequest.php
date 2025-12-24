<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $newsId = $this->route('id');

        return [
            'title_en' => 'required|string|max:255',
            'title_vi' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($newsId)],
            'summary_en' => 'nullable|string',
            'summary_vi' => 'nullable|string',
            'content_en' => 'nullable|string',
            'content_vi' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function attributes(): array
    {
        return [
            'title_en' => __('Title (English)'),
            'title_vi' => __('Title (Vietnamese)'),
            'slug' => __('Slug'),
            'summary_en' => __('Summary (English)'),
            'summary_vi' => __('Summary (Vietnamese)'),
            'content_en' => __('Content (English)'),
            'content_vi' => __('Content (Vietnamese)'),
            'status' => __('Status'),
            'thumbnail' => __('Thumbnail'),
        ];
    }
}

