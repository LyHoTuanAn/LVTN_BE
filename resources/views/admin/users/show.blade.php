@extends('layouts.admin')

@section('title', __('User Details') . ' - ' . $user->name)
@section('page-title', __('User Details'))

@php
use Illuminate\Support\Facades\Storage;
@endphp

@push('styles')
<style>
    input[type="file"] {
        cursor: pointer;
    }
    input[type="file"]:hover {
        border-color: #3498db;
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }
    .toggle-slider {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .toggle-switch input:checked ~ .toggle-slider {
        background-color: #27ae60;
    }
    .toggle-switch input:checked ~ .toggle-slider:before {
        transform: translateX(30px);
    }
</style>
<script>
    function updateToggleStyle(checkbox) {
        const slider = checkbox.nextElementSibling;
        if (checkbox.checked) {
            slider.style.backgroundColor = '#27ae60';
            slider.style.setProperty('--transform', 'translateX(30px)');
        } else {
            slider.style.backgroundColor = '#ccc';
            slider.style.setProperty('--transform', 'translateX(0)');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('email_verified_toggle');
        if (toggle) {
            updateToggleStyle(toggle);
        }
    });
</script>
@endpush



@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 30px;">
        <a 
            href="{{ route('admin.users.index') }}" 
            style="color: #3498db; text-decoration: none; font-size: 0.9em;"
        >
            ← {{ __('Back to User List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.8em; margin-bottom: 30px;">{{ __('Edit User') }}</h2>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                    {{ __('Name') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $user->name) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                >
                @error('name')
                    <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                    {{ __('Email') }} <span style="color: #666; font-size: 0.85em;">({{ __('Cannot be changed') }})</span>
                </label>
                <input 
                    type="email" 
                    value="{{ $user->email }}" 
                    disabled
                    readonly
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; background: #f5f5f5; color: #666; cursor: not-allowed;"
                >
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                    {{ __('Phone') }}
                </label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone', $user->phone) }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                >
                @error('phone')
                    <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                    {{ __('Address') }}
                </label>
                <input 
                    type="text" 
                    name="address" 
                    value="{{ old('address', $user->address) }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                >
                @error('address')
                    <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                    {{ __('Role') }} <span style="color: #e74c3c;">*</span>
                </label>
                <select 
                    name="role_id" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                >
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                    {{ __('Password') }} <span style="color: #666; font-size: 0.85em;">({{ __('Leave blank to keep current password') }})</span>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="{{ __('New password (min 6 characters)') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                >
                @error('password')
                    <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 30px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                        {{ __('Email Verification Status') }}
                    </label>
                    <div style="color: #666; font-size: 0.9em;">
                        @if ($user->email_verified_at)
                            {{ __('Verified') }} ({{ $user->email_verified_at->format('Y-m-d H:i') }})
                        @else
                            {{ __('Not Verified') }}
                        @endif
                    </div>
                </div>
                <label class="toggle-switch" style="position: relative; display: inline-block; width: 60px; height: 30px; cursor: pointer;">
                    <input 
                        type="checkbox" 
                        id="email_verified_toggle"
                        {{ old('email_verified', $user->email_verified_at ? '1' : '') ? 'checked' : '' }}
                        style="opacity: 0; width: 0; height: 0; position: absolute;"
                        onchange="document.getElementById('email_verified_hidden').value = this.checked ? '1' : '0'; updateToggleStyle(this);"
                    >
                    <span class="toggle-slider"></span>
                    <input 
                        type="hidden" 
                        name="email_verified" 
                        id="email_verified_hidden"
                        value="{{ old('email_verified', $user->email_verified_at ? '1' : '0') }}"
                    >
                </label>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                {{ __('Avatar') }}
            </label>
            
            @if ($user->avatar)
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img 
                            src="{{ asset('storage/' . $user->avatar->file_path) }}" 
                            alt="{{ __('Current Avatar') }}"
                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px solid #ddd;"
                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23ddd\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3ENo Image%3C/text%3E%3C/svg%3E'"
                        >
                        <div>
                            <div style="color: #666; font-size: 0.9em; margin-bottom: 5px;">
                                {{ __('Current Avatar') }}
                            </div>
                            <div style="color: #999; font-size: 0.85em;">
                                {{ __('File') }}: {{ $user->avatar->file_name }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <input 
                type="file" 
                name="avatar" 
                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
            >
            <div style="margin-top: 5px; color: #666; font-size: 0.85em;">
                {{ __('Upload new avatar (will be converted to WebP format)') }}
            </div>
            @error('avatar')
                <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>



        <div style="display: flex; gap: 10px;">
            <button 
                type="submit" 
                style="padding: 12px 30px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1em; font-weight: 600;"
            >
                {{ __('Save Changes') }}
            </button>
            <a 
                href="{{ route('admin.users.index') }}" 
                style="padding: 12px 30px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em; display: inline-block;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection