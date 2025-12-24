@extends('layouts.app')

@section('title', __('Edit Voucher'))
@section('page-title', __('Edit Voucher'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <form method="POST" action="{{ route('admin.vouchers.update', $voucher->id) }}" style="display: grid; gap: 20px;">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Code') }}</label>
                <input type="text" name="code" value="{{ old('code', $voucher->code) }}" maxlength="50"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                @error('code')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Name') }} <span style="color: #e74c3c;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $voucher->name) }}" required maxlength="255"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                @error('name')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Type') }} <span style="color: #e74c3c;">*</span></label>
                <select name="type" id="voucher_type" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">{{ __('Select type') }}</option>
                    <option value="percentage" {{ old('type', $voucher->type) === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
                    <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
                </select>
                @error('type')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Amount') }} <span style="color: #e74c3c;">*</span></label>
                <input type="text" name="amount" id="amount_input" value="{{ old('amount', number_format((int)$voucher->amount, 0, '', '.')) }}" required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;"
                    placeholder="{{ __('Enter amount') }}">
                <input type="hidden" name="amount_raw" id="amount_raw">
                @error('amount')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
                <div id="amount_error" style="color: #e74c3c; margin-top: 6px; font-size: 0.9em; display: none;"></div>
                <p style="font-size: 0.85em; color: #666; margin-top: 6px;" id="amount_hint">
                    {{ __('Select type to see format instructions') }}
                </p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Usage Limit') }}</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;"
                    placeholder="{{ __('Leave empty for unlimited') }}">
                @error('usage_limit')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
                <p style="font-size: 0.85em; color: #666; margin-top: 6px;">{{ __('Total number of times this voucher can be used') }}</p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Per User Limit') }}</label>
                <input type="number" name="per_user_limit" value="{{ old('per_user_limit', $voucher->per_user_limit) }}" min="1"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;"
                    placeholder="{{ __('Leave empty for unlimited') }}">
                @error('per_user_limit')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
                <p style="font-size: 0.85em; color: #666; margin-top: 6px;">{{ __('Number of times each user can use this voucher') }}</p>
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Applies To') }} <span style="color: #e74c3c;">*</span></label>
            <select name="applies_to" id="applies_to" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                <option value="">{{ __('Select applies to') }}</option>
                <option value="all_users" {{ old('applies_to', $voucher->applies_to) === 'all_users' ? 'selected' : '' }}>{{ __('All Users') }}</option>
                <option value="specific_users" {{ old('applies_to', $voucher->applies_to) === 'specific_users' ? 'selected' : '' }}>{{ __('Specific Users') }}</option>
                <option value="specific_movies" {{ old('applies_to', $voucher->applies_to) === 'specific_movies' ? 'selected' : '' }}>{{ __('Specific Movies') }}</option>
            </select>
            @error('applies_to')
                <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
            @enderror
        </div>

        <div id="only_for_user_field" style="display: none;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Only For User IDs') }} <span style="color: #e74c3c;">*</span></label>
            <input type="text" name="only_for_user" value="{{ old('only_for_user', $voucher->only_for_user) }}" maxlength="255"
                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;"
                placeholder="{{ __('Comma-separated user IDs, e.g. 1,2,3') }}">
            @error('only_for_user')
                <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
            @enderror
            <p style="font-size: 0.85em; color: #666; margin-top: 6px;">{{ __('Enter comma-separated user IDs (e.g., 1,2,3)') }}</p>
        </div>

        <div id="only_for_movie_field" style="display: none;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Only For Movie IDs') }} <span style="color: #e74c3c;">*</span></label>
            <input type="text" name="only_for_movie" value="{{ old('only_for_movie', $voucher->only_for_movie) }}" maxlength="255"
                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;"
                placeholder="{{ __('Comma-separated movie IDs, e.g. 1,2,3') }}">
            @error('only_for_movie')
                <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
            @enderror
            <p style="font-size: 0.85em; color: #666; margin-top: 6px;">{{ __('Enter comma-separated movie IDs (e.g., 1,2,3)') }}</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Valid From') }} <span style="color: #e74c3c;">*</span></label>
                <input type="date" name="valid_from" id="valid_from" class="datepicker" value="{{ old('valid_from', $voucher->valid_from->format('Y-m-d')) }}" required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                <input type="hidden" name="valid_from_time" value="00:00:00">
                @error('valid_from')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Valid To') }} <span style="color: #e74c3c;">*</span></label>
                <input type="date" name="valid_to" id="valid_to" class="datepicker" value="{{ old('valid_to', $voucher->valid_to->format('Y-m-d')) }}" required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                <input type="hidden" name="valid_to_time" value="23:59:59">
                @error('valid_to')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Status') }}</label>
            <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                @php $selectedStatus = old('status', $voucher->status); @endphp
                <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="expired" {{ $selectedStatus === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                <option value="disabled" {{ $selectedStatus === 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
            </select>
            @error('status')
                <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
            @enderror
        </div>

        @if ($errors->any())
            <div style="padding: 12px 16px; background: #ffebee; color: #c62828; border-radius: 6px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('admin.vouchers.show', $voucher->id) }}" style="padding: 10px 16px; background: #e0e0e0; color: #2c3e50; text-decoration: none; border-radius: 6px; font-weight: 600;">
                {{ __('Cancel') }}
            </a>
            <button type="submit" style="padding: 10px 18px; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                {{ __('Update Voucher') }}
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const appliesToSelect = document.getElementById('applies_to');
        const onlyForUserField = document.getElementById('only_for_user_field');
        const onlyForMovieField = document.getElementById('only_for_movie_field');
        const voucherTypeSelect = document.getElementById('voucher_type');
        const amountInput = document.getElementById('amount_input');
        const amountRawInput = document.getElementById('amount_raw');
        const amountHint = document.getElementById('amount_hint');
        const form = document.querySelector('form');

        // Format number as Vietnamese currency (VND format: x.xxx.xxx) or integer
        function formatNumber(value) {
            if (!value) return '';
            // Remove all non-digit characters (only allow digits)
            let numericValue = value.toString().replace(/[^\d]/g, '');
            // Format with dots as thousands separator (Vietnamese format)
            return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Parse formatted number to raw number (remove dots, only integer)
        function parseNumber(formattedValue) {
            if (!formattedValue) return '';
            // Remove all dots (thousands separator) - return integer only
            return formattedValue.toString().replace(/\./g, '');
        }

        // Validate amount based on type
        function validateAmount(value, type) {
            const errorDiv = document.getElementById('amount_error');
            const numericValue = parseNumber(value);
            
            // Clear previous error
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            amountInput.style.borderColor = '#ddd';
            
            if (!numericValue) {
                return true; // Empty is OK, required attribute will handle it
            }
            
            const num = parseInt(numericValue);
            
            if (type === 'percentage') {
                if (num < 1 || num > 100) {
                    errorDiv.textContent = '{{ __('Percentage must be between 1 and 100') }}';
                    errorDiv.style.display = 'block';
                    amountInput.style.borderColor = '#e74c3c';
                    return false;
                }
            }
            
            return true;
        }

        // Format amount input
        amountInput.addEventListener('input', function(e) {
            const value = e.target.value;
            const cursorPosition = e.target.selectionStart;
            const type = voucherTypeSelect.value;
            
            // Only allow digits
            let digitsOnly = value.replace(/[^\d]/g, '');
            
            // If percentage type, limit to max 100 while typing
            if (type === 'percentage' && digitsOnly) {
                const num = parseInt(digitsOnly);
                if (num > 100) {
                    digitsOnly = '100';
                }
            }
            
            const formatted = formatNumber(digitsOnly);
            
            // Calculate new cursor position
            const diff = formatted.length - digitsOnly.length;
            const newPosition = Math.max(0, Math.min(formatted.length, cursorPosition + diff));
            
            e.target.value = formatted;
            e.target.setSelectionRange(newPosition, newPosition);
            
            // Update hidden raw value (integer only)
            const rawValue = parseNumber(formatted);
            amountRawInput.value = rawValue;
            
            // Validate
            validateAmount(formatted, type);
        });

        // Format on blur
        amountInput.addEventListener('blur', function(e) {
            const type = voucherTypeSelect.value;
            const value = parseNumber(e.target.value);
            
            if (value) {
                // For percentage, ensure value is between 1-100
                if (type === 'percentage') {
                    const num = parseInt(value);
                    if (num < 1) {
                        e.target.value = '1';
                        amountRawInput.value = '1';
                    } else if (num > 100) {
                        e.target.value = '100';
                        amountRawInput.value = '100';
                    } else {
                        e.target.value = formatNumber(value);
                        amountRawInput.value = value;
                    }
                } else {
                    e.target.value = formatNumber(value);
                    amountRawInput.value = value;
                }
                
                // Validate after formatting
                validateAmount(e.target.value, type);
            }
        });

        // Before form submit, validate and set the actual numeric value
        form.addEventListener('submit', function(e) {
            const type = voucherTypeSelect.value;
            const formattedValue = amountInput.value;
            const numericValue = parseNumber(formattedValue);
            
            // Validate before submit
            if (!validateAmount(formattedValue, type)) {
                e.preventDefault();
                amountInput.focus();
                return false;
            }
            
            // Set the actual numeric value
            amountInput.value = numericValue;
            return true;
        });

        function toggleAppliesToFields() {
            const value = appliesToSelect.value;
            onlyForUserField.style.display = value === 'specific_users' ? 'block' : 'none';
            onlyForMovieField.style.display = value === 'specific_movies' ? 'block' : 'none';
            
            if (value !== 'specific_users') {
                document.querySelector('input[name="only_for_user"]').value = '';
            }
            if (value !== 'specific_movies') {
                document.querySelector('input[name="only_for_movie"]').value = '';
            }
        }

        function updateAmountHint() {
            const type = voucherTypeSelect.value;
            // Reformat current value when type changes
            if (amountInput.value) {
                const parsed = parseNumber(amountInput.value);
                
                // Validate and adjust if percentage
                if (type === 'percentage') {
                    const num = parseInt(parsed);
                    if (num > 100) {
                        const adjustedValue = '100';
                        amountInput.value = formatNumber(adjustedValue);
                        amountRawInput.value = adjustedValue;
                        validateAmount(amountInput.value, type);
                    } else {
                        const formatted = formatNumber(parsed);
                        amountInput.value = formatted;
                        amountRawInput.value = parsed;
                    }
                } else {
                    const formatted = formatNumber(parsed);
                    amountInput.value = formatted;
                    amountRawInput.value = parsed;
                }
            }
            
            // Clear validation error when type changes
            const errorDiv = document.getElementById('amount_error');
            errorDiv.style.display = 'none';
            amountInput.style.borderColor = '#ddd';
            
            if (type === 'percentage') {
                amountHint.textContent = '{{ __('For percentage: 1-100 (integer only, e.g., 10)') }}';
            } else if (type === 'fixed') {
                amountHint.textContent = '{{ __('For fixed: amount in VND (e.g., 100.000)') }}';
            } else {
                amountHint.textContent = '{{ __('For percentage: 1-100, for fixed: amount in VND') }}';
            }
        }

        // Format initial value
        if (amountInput.value) {
            // If old value is already formatted, parse and reformat
            const parsed = parseNumber(amountInput.value);
            const formatted = formatNumber(parsed);
            amountInput.value = formatted;
            amountRawInput.value = parsed;
        }

        appliesToSelect.addEventListener('change', toggleAppliesToFields);
        voucherTypeSelect.addEventListener('change', updateAmountHint);
        
        // Initialize on page load
        toggleAppliesToFields();
        updateAmountHint();
    });
</script>
@endsection

