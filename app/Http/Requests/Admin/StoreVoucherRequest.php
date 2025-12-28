<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize amount: remove dots (thousands separator) before validation
        // This handles cases where user inputs "1.000" (should be 1000, not 1.00)
        if ($this->has('amount')) {
            $amount = $this->input('amount');
            if (is_string($amount)) {
                // Remove all dots (Vietnamese thousands separator)
                // Only remove dots if they are thousands separators (not decimal point)
                // For VND currency, we don't use decimal points, so remove all dots
                $normalizedAmount = str_replace('.', '', $amount);
                $this->merge([
                    'amount' => $normalizedAmount,
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'code' => 'nullable|string|max:50|unique:vouchers,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'amount' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'applies_to' => 'required|in:all_users,specific_users,specific_movies',
            'only_for_user' => 'nullable|string|max:255',
            'only_for_movie' => 'nullable|string|max:255',
            'valid_from' => 'required|date|before_or_equal:valid_to',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'status' => 'sometimes|in:active,expired,disabled',
        ];

        // Conditional validation based on type
        $rules['amount'] = 'required|numeric|min:0';
        
        // Conditional validation based on applies_to
        if ($this->input('applies_to') === 'specific_users') {
            $rules['only_for_user'] = 'required|string|max:255';
        }
        
        if ($this->input('applies_to') === 'specific_movies') {
            $rules['only_for_movie'] = 'required|string|max:255';
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // If type is percentage, amount should not exceed 100
            if ($this->input('type') === 'percentage' && $this->input('amount') > 100) {
                $validator->errors()->add('amount', __('Percentage amount cannot exceed 100'));
            }

            // Validate comma-separated IDs format
            if ($this->input('only_for_user')) {
                $userIds = array_filter(array_map('trim', explode(',', $this->input('only_for_user'))));
                foreach ($userIds as $userId) {
                    if (!is_numeric($userId) || $userId <= 0) {
                        $validator->errors()->add('only_for_user', __('Invalid user ID format. Use comma-separated numeric IDs.'));
                        break;
                    }
                }
            }

            if ($this->input('only_for_movie')) {
                $movieIds = array_filter(array_map('trim', explode(',', $this->input('only_for_movie'))));
                foreach ($movieIds as $movieId) {
                    if (!is_numeric($movieId) || $movieId <= 0) {
                        $validator->errors()->add('only_for_movie', __('Invalid movie ID format. Use comma-separated numeric IDs.'));
                        break;
                    }
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'code' => __('Code'),
            'name' => __('Name'),
            'type' => __('Type'),
            'amount' => __('Amount'),
            'usage_limit' => __('Usage Limit'),
            'per_user_limit' => __('Per User Limit'),
            'applies_to' => __('Applies To'),
            'only_for_user' => __('Only For User'),
            'only_for_movie' => __('Only For Movie'),
            'valid_from' => __('Valid From'),
            'valid_to' => __('Valid To'),
            'status' => __('Status'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'code.unique' => __('Voucher code already exists'),
            'code.max' => __('Voucher code cannot exceed 50 characters'),
            'name.required' => __('Voucher name is required'),
            'name.max' => __('Voucher name cannot exceed 255 characters'),
            'type.required' => __('Voucher type is required'),
            'type.in' => __('Invalid voucher type'),
            'amount.required' => __('Amount is required'),
            'amount.numeric' => __('Amount must be a number'),
            'amount.min' => __('Amount must be at least 0'),
            'usage_limit.integer' => __('Usage limit must be a number'),
            'usage_limit.min' => __('Usage limit must be at least 1'),
            'per_user_limit.integer' => __('Per user limit must be a number'),
            'per_user_limit.min' => __('Per user limit must be at least 1'),
            'applies_to.required' => __('Applies to field is required'),
            'applies_to.in' => __('Invalid applies to value'),
            'only_for_user.required' => __('User IDs are required when applies to specific users'),
            'only_for_movie.required' => __('Movie IDs are required when applies to specific movies'),
            'valid_from.required' => __('Valid from date is required'),
            'valid_from.date' => __('Invalid valid from date format'),
            'valid_from.before_or_equal' => __('Valid from date must be before or equal to valid to date'),
            'valid_to.required' => __('Valid to date is required'),
            'valid_to.date' => __('Invalid valid to date format'),
            'valid_to.after_or_equal' => __('Valid to date must be after or equal to valid from date'),
            'status.in' => __('Invalid status value'),
        ];
    }
}

