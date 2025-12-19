@extends('layouts.admin')

@section('title', __('Add Room'))
@section('page-title', __('Add Room'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.rooms.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 30px;">{{ __('Create New Room') }}</h2>

    <div style="margin-bottom: 20px; padding: 15px; background: #e3f2fd; border-radius: 6px;">
        <strong style="color: #1976d2;">{{ __('Cinema') }}:</strong> {{ $cinema->name }}
    </div>

    <form method="POST" action="{{ route('admin.rooms.store') }}">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Room Name') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}"
                    required
                    maxlength="100"
                    placeholder="{{ __('Enter room name (e.g., Room 1, VIP Room)') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('name')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="seat_count" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Seat Count') }} <span style="color: #e74c3c;">*</span>
                </label>
                <select 
                    name="seat_count" 
                    id="seat_count" 
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box; background: white; cursor: pointer;"
                >
                    <option value="">{{ __('Select seat count') }}</option>
                    <option value="30" {{ old('seat_count', '') == '30' ? 'selected' : '' }}>30</option>
                    <option value="40" {{ old('seat_count', '') == '40' ? 'selected' : '' }}>40</option>
                    <option value="50" {{ old('seat_count', '') == '50' ? 'selected' : '' }}>50</option>
                    <option value="60" {{ old('seat_count', '') == '60' ? 'selected' : '' }}>60</option>
                </select>
                @error('seat_count')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid #eee;">
            <button 
                type="submit" 
                style="padding: 12px 30px; background: #27ae60; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; font-weight: 600;"
            >
                {{ __('Create Room') }}
            </button>
            <a 
                href="{{ route('admin.rooms.index') }}" 
                style="padding: 12px 30px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em; font-weight: 600;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
