<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'email_verified' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'password' => __('Password'),
            'password_confirmation' => __('Password Confirmation'),
            'phone' => __('Phone'),
            'address' => __('Address'),
            'role_id' => __('Role'),
            'email_verified' => __('Email Verified'),
            'avatar' => __('Avatar'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Name is required'),
            'name.max' => __('Name cannot exceed 100 characters'),
            'email.required' => __('Email is required'),
            'email.email' => __('Invalid email format'),
            'email.unique' => __('This email is already in use'),
            'password.required' => __('Password is required'),
            'password.min' => __('Password must be at least 6 characters'),
            'password.confirmed' => __('Password confirmation does not match'),
            'role_id.required' => __('Please select a role'),
            'role_id.exists' => __('The selected role is invalid'),
            'avatar.image' => __('Avatar must be an image'),
            'avatar.mimes' => __('Avatar must be a file of type: jpeg, png, jpg, gif, webp'),
            'avatar.max' => __('Avatar cannot be larger than 5MB'),
        ];
    }
}
