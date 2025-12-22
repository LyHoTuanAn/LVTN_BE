@extends('layouts.app')

@section('title', __('Add Showtime'))
@section('page-title', __('Add Showtime'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.showtimes.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 30px;">{{ __('Create New Showtime') }}</h2>

    <form method="POST" action="{{ route('admin.showtimes.store') }}">
        @csrf

        <div class="showtime-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label for="movie_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Movie') }} <span style="color: #e74c3c;">*</span>
                </label>
                <select 
                    name="movie_id" 
                    id="movie_id" 
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; background: white; box-sizing: border-box;"
                >
                    <option value="">{{ __('Select Movie') }}</option>
                    @foreach ($movies as $movie)
                        <option value="{{ $movie->id }}" data-duration="{{ $movie->duration }}" {{ old('movie_id') == $movie->id ? 'selected' : '' }}>
                            {{ $movie->title }} ({{ $movie->duration }} {{ __('mins') }})
                        </option>
                    @endforeach
                </select>
                @error('movie_id')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="room_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Room') }} <span style="color: #e74c3c;">*</span>
                </label>
                <select 
                    name="room_id" 
                    id="room_id" 
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; background: white; box-sizing: border-box;"
                >
                    <option value="">{{ __('Select Room') }}</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }} ({{ $room->seat_count }} {{ __('seats') }})
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Date') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="date" 
                    id="date" 
                    class="datepicker-future"
                    value="{{ old('date', date('Y-m-d')) }}"
                    required
                    placeholder="{{ __('Select date') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box; background: white;"
                >
                @error('date')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="status" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Status') }} <span style="color: #e74c3c;">*</span>
                </label>
                <select 
                    name="status" 
                    id="status" 
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; background: white; box-sizing: border-box;"
                >
                    <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing') }}</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
                @error('status')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="start_time" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Start Time') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="start_time" 
                    id="start_time" 
                    class="timepicker-24h"
                    value="{{ old('start_time', '09:00') }}"
                    required
                    placeholder="{{ __('HH:mm (24-hour)') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box; font-family: monospace;"
                >
                @error('start_time')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="end_time" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('End Time') }} <span style="color: #7f8c8d; font-size: 0.85em; font-weight: 400;">({{ __('Auto calculated') }})</span>
                </label>
                <input 
                    type="text" 
                    id="end_time_display" 
                    class="timepicker-24h-disabled"
                    value="{{ old('end_time', '11:00') }}"
                    readonly
                    disabled
                    placeholder="{{ __('HH:mm (24-hour)') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box; background: #f5f5f5; color: #666; cursor: not-allowed; font-family: monospace;"
                >
                <input 
                    type="hidden" 
                    name="end_time" 
                    id="end_time" 
                    value="{{ old('end_time', '11:00') }}"
                    required
                >
                <small style="color: #7f8c8d; margin-top: 5px; display: block; font-size: 0.85em;">{{ __('End time is automatically calculated based on start time and movie duration') }}</small>
                @error('end_time')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Price (VNĐ)') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="price" 
                    id="price" 
                    value="{{ old('price') ? number_format(old('price'), 0, ',', '.') : '80.000' }}"
                    required
                    placeholder="{{ __('Enter ticket price (e.g., 80.000)') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box; text-align: left;"
                >
                <small style="color: #7f8c8d; margin-top: 5px; display: block; font-size: 0.85em;">{{ __('Format: x.xxx (e.g., 80.000)') }}</small>
                @error('price')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="showtime-actions" style="display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid #eee; flex-wrap: wrap;">
            <button 
                type="submit" 
                style="padding: 12px 30px; background: #27ae60; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; font-weight: 600;"
            >
                {{ __('Create Showtime') }}
            </button>
            <a 
                href="{{ route('admin.showtimes.index') }}" 
                style="padding: 12px 30px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em; font-weight: 600;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Ensure time inputs display in 24-hour format */
    .timepicker-24h,
    .timepicker-24h-disabled {
        font-family: monospace;
        direction: ltr;
        text-align: left;
    }
    
    /* Flatpickr time picker styling */
    .flatpickr-time {
        display: flex;
        flex-direction: row;
        align-items: center;
    }
    
    .flatpickr-time input.flatpickr-hour,
    .flatpickr-time input.flatpickr-minute {
        font-family: monospace;
    }
    @media (max-width: 768px) {
        .showtime-grid-2 {
            grid-template-columns: 1fr !important;
        }
        .showtime-actions {
            flex-direction: column;
        }
        .showtime-actions button,
        .showtime-actions a {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const movieSelect = document.getElementById('movie_id');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time'); // Hidden input for form submission
    const endTimeDisplay = document.getElementById('end_time_display'); // Display input (disabled)
    const priceInput = document.getElementById('price');
    
    // Initialize Flatpickr for time picker with 24-hour format
    const startTimePicker = flatpickr(startTimeInput, {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 1,
        allowInput: true,
        locale: {
            firstDayOfWeek: 1
        }
    });
    
    // Store movie durations in a map for quick lookup
    const movieDurations = {};
    @foreach ($movies as $movie)
        movieDurations[{{ $movie->id }}] = {{ $movie->duration }};
    @endforeach
    
    // Validate and format time input (HH:mm format)
    function validateTimeFormat(input) {
        const value = input.value.trim();
        const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
        
        if (value && !timeRegex.test(value)) {
            // Try to fix common mistakes (remove SA/CH/AM/PM)
            const fixed = value.replace(/\s*(SA|CH|AM|PM)\s*/i, '');
            if (timeRegex.test(fixed)) {
                input.value = fixed;
                if (input === startTimeInput) {
                    startTimePicker.setDate('1970-01-01 ' + fixed, false);
                }
            }
        }
        
        // Ensure format is HH:mm
        if (value && timeRegex.test(value)) {
            const [hours, minutes] = value.split(':');
            const formatted = String(parseInt(hours)).padStart(2, '0') + ':' + minutes;
            if (input.value !== formatted) {
                input.value = formatted;
                if (input === startTimeInput) {
                    startTimePicker.setDate('1970-01-01 ' + formatted, false);
                }
            }
        }
    }
    
    function calculateEndTime() {
        const movieId = movieSelect.value;
        const startTime = startTimeInput.value.trim();
        
        if (!movieId || !startTime) {
            endTimeInput.value = '';
            endTimeDisplay.value = '';
            return;
        }
        
        // Validate start time format
        validateTimeFormat(startTimeInput);
        const validatedStartTime = startTimeInput.value;
        
        if (!validatedStartTime) {
            endTimeInput.value = '';
            endTimeDisplay.value = '';
            return;
        }
        
        const duration = movieDurations[movieId];
        if (!duration) {
            endTimeInput.value = '';
            endTimeDisplay.value = '';
            return;
        }
        
        // Parse start time
        const [hours, minutes] = validatedStartTime.split(':').map(Number);
        
        // Validate hours and minutes
        if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
            endTimeInput.value = '';
            endTimeDisplay.value = '';
            return;
        }
        
        // Calculate total minutes from start time
        const totalMinutes = hours * 60 + minutes;
        
        // Add movie duration
        const endTotalMinutes = totalMinutes + duration;
        
        // Calculate end time hours and minutes
        const endHours = Math.floor(endTotalMinutes / 60) % 24;
        const endMinutes = endTotalMinutes % 60;
        
        // Format as HH:mm (24-hour format)
        const endTime = String(endHours).padStart(2, '0') + ':' + String(endMinutes).padStart(2, '0');
        
        // Set end time in both hidden input (for form submission) and display input
        endTimeInput.value = endTime;
        endTimeDisplay.value = endTime;
    }
    
    // Validate time format on input
    startTimeInput.addEventListener('blur', function() {
        validateTimeFormat(this);
        calculateEndTime();
    });
    
    startTimeInput.addEventListener('change', function() {
        validateTimeFormat(this);
        calculateEndTime();
    });
    
    // Calculate end time when movie is selected
    movieSelect.addEventListener('change', calculateEndTime);
    
    // Calculate on page load if values are already set
    if (movieSelect.value && startTimeInput.value) {
        validateTimeFormat(startTimeInput);
        calculateEndTime();
    }
    
    // Format price input (x.xxx format)
    function formatPrice(value) {
        // Remove all non-digit characters
        const numbers = value.replace(/\D/g, '');
        
        if (!numbers) {
            return '';
        }
        
        // Format with dots as thousand separators
        return numbers.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function unformatPrice(value) {
        // Remove dots and return pure number
        return value.replace(/\./g, '');
    }
    
    // Format price on input
    priceInput.addEventListener('input', function(e) {
        const cursorPosition = e.target.selectionStart;
        const oldValue = e.target.value;
        const newValue = formatPrice(e.target.value);
        
        e.target.value = newValue;
        
        // Restore cursor position after formatting
        const diff = newValue.length - oldValue.length;
        e.target.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
    });
    
    // Format price on blur
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = formatPrice(this.value);
        }
    });
    
    // Convert price back to number before form submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        validateTimeFormat(startTimeInput);
        // Ensure end time is calculated before submit
        calculateEndTime();
        
        // Convert price format back to number (remove dots) before submit
        if (priceInput.value) {
            const priceValue = unformatPrice(priceInput.value);
            // Ensure it's a valid integer
            const numericValue = parseInt(priceValue, 10);
            if (!isNaN(numericValue) && numericValue >= 0) {
                priceInput.value = numericValue.toString();
            } else {
                e.preventDefault();
                alert('{{ __("Invalid price value") }}');
                return false;
            }
        }
    });
});
</script>
@endpush
@endsection
