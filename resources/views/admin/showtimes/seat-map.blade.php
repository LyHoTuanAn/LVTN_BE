@extends('layouts.app')

@section('title', __('Seat Map Management'))
@section('page-title', __('Seat Map Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.showtimes.show', $showtime->id) }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to Showtime Details') }}
        </a>
    </div>

    <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 15px;">{{ __('Showtime Information') }}</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em;">{{ __('Movie') }}</label>
                <p style="margin: 0; color: #2c3e50; font-size: 1.1em; font-weight: 600;">{{ $showtime->movie->title }}</p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em;">{{ __('Date & Time') }}</label>
                <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $showtime->date->format('d/m/Y') }} {{ $showtime->start_time }} - {{ $showtime->end_time }}</p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em;">{{ __('Room') }}</label>
                <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $showtime->room->name }}</p>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div style="margin-bottom: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px; display: flex; gap: 30px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 30px; height: 30px; background: #27ae60; border-radius: 4px; border: 2px solid #1e8449;"></div>
            <span style="color: #2c3e50; font-weight: 600;">{{ __('Available') }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 30px; height: 30px; background: #e74c3c; border-radius: 4px; border: 2px solid #c0392b;"></div>
            <span style="color: #2c3e50; font-weight: 600;">{{ __('Booked') }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 30px; height: 30px; background: #95a5a6; border-radius: 4px; border: 2px solid #7f8c8d;"></div>
            <span style="color: #2c3e50; font-weight: 600;">{{ __('Maintenance') }}</span>
        </div>
    </div>

    <!-- Screen -->
    <div style="text-align: center; margin-bottom: 40px;">
        <div style="display: inline-block; padding: 15px 60px; background: linear-gradient(to bottom, #34495e, #2c3e50); color: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
            <span style="font-size: 1.2em; font-weight: 700; letter-spacing: 3px;">{{ __('SCREEN') }}</span>
        </div>
    </div>

    <!-- Seat Map -->
    <div style="margin-bottom: 30px;">
        @foreach($seatsByRow as $row => $seats)
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; justify-content: center;">
            <div style="min-width: 40px; text-align: center; font-weight: 700; color: #2c3e50; font-size: 1.1em;">{{ $row }}</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                @foreach($seats->sortBy('number') as $seat)
                    @php
                        $isBooked = isset($bookedSeats[$seat->id]);
                        $bookingInfo = $isBooked ? $bookedSeats[$seat->id] : null;
                        $isMaintenance = in_array($seat->status, ['maintenance', 'disabled']);
                        
                        // Priority: Maintenance > Booked > Available
                        if ($isMaintenance) {
                            $bgColor = '#95a5a6';
                            $borderColor = '#7f8c8d';
                            $statusText = __('Maintenance');
                        } elseif ($isBooked) {
                            $bgColor = '#e74c3c';
                            $borderColor = '#c0392b';
                            $statusText = __('Booked');
                        } else {
                            $bgColor = '#27ae60';
                            $borderColor = '#1e8449';
                            $statusText = __('Available');
                        }
                    @endphp
                    <div 
                        id="seat-{{ $seat->id }}"
                        data-seat-id="{{ $seat->id }}"
                        data-seat-status="{{ $seat->status }}"
                        data-is-booked="{{ $isBooked ? '1' : '0' }}"
                        style="
                            width: 45px; 
                            height: 45px; 
                            background: {{ $bgColor }}; 
                            border: 2px solid {{ $borderColor }}; 
                            border-radius: 6px; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center; 
                            color: white; 
                            font-weight: 700; 
                            font-size: 0.9em;
                            cursor: {{ $isBooked ? 'not-allowed' : ($isMaintenance ? 'pointer' : 'pointer') }};
                            position: relative;
                            transition: transform 0.2s, box-shadow 0.2s;
                        "
                        onmouseover="if (!this.dataset.isBooked || this.dataset.isBooked === '0') { this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.3)'; }"
                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';"
                        onclick="toggleSeatMaintenance({{ $showtime->id }}, {{ $seat->id }}, this)"
                        @if($isMaintenance)
                        title="{{ __('Status') }}: {{ $statusText }} | {{ __('Click to remove maintenance') }}"
                        @elseif($isBooked)
                        title="{{ __('Status') }}: {{ $statusText }} | {{ __('Booking Code') }}: {{ $bookingInfo['booking_code'] }} | {{ __('Customer') }}: {{ $bookingInfo['user_name'] }}"
                        @else
                        title="{{ __('Status') }}: {{ $statusText }} | {{ __('Click to set maintenance') }}"
                        @endif
                    >
                        {{ $seat->number }}
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 30px;">
        @php
            $totalSeats = $showtime->room->seats->count();
            $bookedSeatsCount = count($bookedSeats);
            $maintenanceSeatsCount = $showtime->room->seats->whereIn('status', ['maintenance', 'disabled'])->count();
            $availableSeatsCount = $totalSeats - $bookedSeatsCount - $maintenanceSeatsCount;
        @endphp
        <div style="padding: 20px; background: #e8f5e9; border-radius: 8px; border-left: 5px solid #27ae60;">
            <div style="font-size: 2em; font-weight: 700; color: #27ae60; margin-bottom: 5px;">{{ $totalSeats }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Total Seats') }}</div>
        </div>
        <div style="padding: 20px; background: #fff3e0; border-radius: 8px; border-left: 5px solid #27ae60;">
            <div style="font-size: 2em; font-weight: 700; color: #27ae60; margin-bottom: 5px;">{{ $availableSeatsCount }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Available') }}</div>
        </div>
        <div style="padding: 20px; background: #ffebee; border-radius: 8px; border-left: 5px solid #e74c3c;">
            <div style="font-size: 2em; font-weight: 700; color: #e74c3c; margin-bottom: 5px;">{{ $bookedSeatsCount }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Booked') }}</div>
        </div>
        <div style="padding: 20px; background: #eceff1; border-radius: 8px; border-left: 5px solid #95a5a6;">
            <div style="font-size: 2em; font-weight: 700; color: #95a5a6; margin-bottom: 5px;">{{ $maintenanceSeatsCount }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Maintenance') }}</div>
        </div>
    </div>
</div>

<script>
function toggleSeatMaintenance(showtimeId, seatId, element) {
    // Don't allow toggle for booked seats
    if (element.dataset.isBooked === '1') {
        showMessage('{{ __("Cannot set maintenance for booked seat") }}', 'error');
        return;
    }

    // Show loading state
    const originalBg = element.style.background;
    const originalBorder = element.style.borderColor;
    element.style.background = '#bdc3c7';
    element.style.cursor = 'wait';

    const url = `/admin/showtimes/${showtimeId}/seat/${seatId}/toggle-maintenance`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update seat status
            const isMaintenance = data.seat.is_maintenance;
            
            if (isMaintenance) {
                element.style.background = '#95a5a6';
                element.style.borderColor = '#7f8c8d';
                element.dataset.seatStatus = 'maintenance';
                element.title = '{{ __("Status") }}: {{ __("Maintenance") }} | {{ __("Click to remove maintenance") }}';
            } else {
                element.style.background = '#27ae60';
                element.style.borderColor = '#1e8449';
                element.dataset.seatStatus = 'active';
                element.title = '{{ __("Status") }}: {{ __("Available") }} | {{ __("Click to set maintenance") }}';
            }
            
            // Show success message
            showMessage(data.message, 'success');
            
            // Reload statistics
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            element.style.background = originalBg;
            element.style.borderColor = originalBorder;
            showMessage(data.message || '{{ __("Failed to update seat status") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        element.style.background = originalBg;
        element.style.borderColor = originalBorder;
        showMessage('{{ __("An error occurred") }}', 'error');
    })
    .finally(() => {
        element.style.cursor = 'pointer';
    });
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
        color: white;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 10000;
        font-weight: 600;
        animation: slideIn 0.3s ease-out;
    `;
    messageDiv.textContent = message;
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(messageDiv);
        }, 300);
    }, 3000);
}
</script>

<style>
    @media print {
        .no-print {
            display: none;
        }
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
</style>
@endsection

