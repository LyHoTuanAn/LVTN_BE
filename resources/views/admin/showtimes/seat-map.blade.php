@extends('layouts.admin')

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
            <div style="width: 30px; height: 30px; background: #f39c12; border-radius: 4px; border: 2px solid #d68910;"></div>
            <span style="color: #2c3e50; font-weight: 600;">{{ __('Booked (Unpaid)') }}</span>
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
                        $isPaid = $bookingInfo && $bookingInfo['is_paid'];
                        
                        if ($isBooked && $isPaid) {
                            $bgColor = '#e74c3c';
                            $borderColor = '#c0392b';
                        } elseif ($isBooked && !$isPaid) {
                            $bgColor = '#f39c12';
                            $borderColor = '#d68910';
                        } else {
                            $bgColor = '#27ae60';
                            $borderColor = '#1e8449';
                        }
                    @endphp
                    <div 
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
                            cursor: {{ $isBooked ? 'help' : 'default' }};
                            position: relative;
                            transition: transform 0.2s, box-shadow 0.2s;
                        "
                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.3)'"
                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'"
                        @if($isBooked)
                        title="{{ __('Booking Code') }}: {{ $bookingInfo['booking_code'] }} | {{ __('Customer') }}: {{ $bookingInfo['user_name'] }} | {{ $isPaid ? __('Paid') : __('Unpaid') }}"
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
            $availableSeatsCount = $totalSeats - $bookedSeatsCount;
            $paidBookingsCount = collect($bookedSeats)->where('is_paid', true)->count();
            $unpaidBookingsCount = collect($bookedSeats)->where('is_paid', false)->count();
        @endphp
        <div style="padding: 20px; background: #e8f5e9; border-radius: 8px; border-left: 5px solid #27ae60;">
            <div style="font-size: 2em; font-weight: 700; color: #27ae60; margin-bottom: 5px;">{{ $totalSeats }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Total Seats') }}</div>
        </div>
        <div style="padding: 20px; background: #fff3e0; border-radius: 8px; border-left: 5px solid #f39c12;">
            <div style="font-size: 2em; font-weight: 700; color: #f39c12; margin-bottom: 5px;">{{ $availableSeatsCount }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Available') }}</div>
        </div>
        <div style="padding: 20px; background: #ffebee; border-radius: 8px; border-left: 5px solid #e74c3c;">
            <div style="font-size: 2em; font-weight: 700; color: #e74c3c; margin-bottom: 5px;">{{ $paidBookingsCount }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Booked (Paid)') }}</div>
        </div>
        <div style="padding: 20px; background: #fff8e1; border-radius: 8px; border-left: 5px solid #f39c12;">
            <div style="font-size: 2em; font-weight: 700; color: #f39c12; margin-bottom: 5px;">{{ $unpaidBookingsCount }}</div>
            <div style="color: #2c3e50; font-weight: 600;">{{ __('Booked (Unpaid)') }}</div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
@endsection

