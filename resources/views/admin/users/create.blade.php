@extends('layouts.app')

@section('title', __('Add User'))
@section('page-title', __('Add User'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.users.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 30px;">{{ __('Create New User') }}</h2>

    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" autocomplete="off">
        @csrf

        <div class="user-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Name') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}"
                    required
                    maxlength="100"
                    placeholder="{{ __('Enter full name') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('name')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Email') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    required
                    autocomplete="off"
                    placeholder="{{ __('Enter email address') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('email')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Password') }} <span style="color: #e74c3c;">*</span>
                </label>
                <div style="position: relative;">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        minlength="6"
                        autocomplete="new-password"
                        placeholder="{{ __('Enter password (min 6 characters)') }}"
                        style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                    >
                    <button 
                        type="button" 
                        onclick="togglePasswordVisibility('password', 'password_toggle_icon')" 
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #7f8c8d; font-size: 1em;"
                        aria-label="Toggle password visibility"
                    >
                        <span id="password_toggle_icon">👁️</span>
                    </button>
                </div>
                @error('password')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password_confirmation" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Confirm Password') }} <span style="color: #e74c3c;">*</span>
                </label>
                <div style="position: relative;">
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required
                        minlength="6"
                        autocomplete="new-password"
                        placeholder="{{ __('Confirm password') }}"
                        style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                    >
                    <button 
                        type="button" 
                        onclick="togglePasswordVisibility('password_confirmation', 'password_confirm_toggle_icon')" 
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #7f8c8d; font-size: 1em;"
                        aria-label="Toggle password confirmation visibility"
                    >
                        <span id="password_confirm_toggle_icon">👁️</span>
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Phone') }}
                </label>
                <input 
                    type="text" 
                    name="phone" 
                    id="phone" 
                    value="{{ old('phone') }}"
                    maxlength="20"
                    placeholder="{{ __('Enter phone number') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('phone')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="role_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Role') }} <span style="color: #e74c3c;">*</span>
                </label>
                <select 
                    name="role_id" 
                    id="role_id" 
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; background: white; box-sizing: border-box;"
                >
                    <option value="">{{ __('Select Role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="address" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                {{ __('Address') }}
            </label>
            <input 
                type="text" 
                name="address" 
                id="address" 
                value="{{ old('address') }}"
                maxlength="255"
                placeholder="{{ __('Enter address') }}"
                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
            @error('address')
                <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="avatar" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                {{ __('Avatar') }}
            </label>
            <input 
                type="file" 
                name="avatar" 
                id="avatar" 
                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
            <small style="color: #7f8c8d; margin-top: 5px; display: block;">{{ __('Accepted formats: JPEG, PNG, JPG, GIF, WebP. Max size: 5MB') }}</small>
            @error('avatar')
                <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input 
                    type="checkbox" 
                    name="email_verified" 
                    value="1"
                    {{ old('email_verified') ? 'checked' : '' }}
                    style="width: 18px; height: 18px;"
                >
                <span style="font-weight: 600; color: #2c3e50;">{{ __('Mark email as verified') }}</span>
            </label>
            <small style="color: #7f8c8d; margin-top: 5px; display: block; padding-left: 28px;">{{ __('If checked, user can login immediately without email verification') }}</small>
        </div>

        <div class="user-actions" style="display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid #eee; flex-wrap: wrap;">
            <button 
                type="submit" 
                style="padding: 12px 30px; background: #27ae60; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; font-weight: 600;"
            >
                {{ __('Create User') }}
            </button>
            <a 
                href="{{ route('admin.users.index') }}" 
                style="padding: 12px 30px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em; font-weight: 600;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = '🙈';
        } else {
            input.type = 'password';
            icon.textContent = '👁️';
        }
    }
</script>

<style>
    @media (max-width: 768px) {
        .user-grid-2 {
            grid-template-columns: 1fr !important;
        }
        .user-actions {
            flex-direction: column;
        }
        .user-actions button,
        .user-actions a {
            width: 100%;
            text-align: center;
        }
    }
</style>

@endsection
