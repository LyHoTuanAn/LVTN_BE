@extends('layouts.admin')

@section('title', __('Booking Details'))
@section('page-title', __('Booking Details'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.bookings.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to Bookings') }}
        </a>
    </div>

    @if(session('success'))
        <div style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 30px;">{{ __('Booking') }} #{{ $booking->code }}</h2>

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
        <!-- Booking Information -->
        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 5px solid #3498db;">
            <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">
                {{ __('Booking Information') }}
            </h3>
            <div style="display: grid; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Booking Code') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1.2em; font-weight: 700; font-family: monospace;">{{ $booking->code }}</p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Status') }}</label>
                    @php
                        $statusColors = [
                            'pending' => '#f39c12',
                            'confirmed' => '#3498db',
                            'canceled' => '#e74c3c',
                            'completed' => '#27ae60',
                        ];
                        $statusLabels = [
                            'pending' => __('Pending'),
                            'confirmed' => __('Confirmed'),
                            'canceled' => __('Canceled'),
                            'completed' => __('Completed'),
                        ];
                    @endphp
                    <span style="padding: 6px 16px; background: {{ $statusColors[$booking->status] ?? '#95a5a6' }}; color: white; border-radius: 12px; font-size: 0.9em; font-weight: 600; display: inline-block;">
                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                    </span>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Payment Status') }}</label>
                    @if($booking->is_paid)
                        <span style="padding: 6px 16px; background: #27ae60; color: white; border-radius: 12px; font-size: 0.9em; font-weight: 600; display: inline-block;">
                            {{ __('Paid') }}
                        </span>
                    @else
                        <span style="padding: 6px 16px; background: #e74c3c; color: white; border-radius: 12px; font-size: 0.9em; font-weight: 600; display: inline-block;">
                            {{ __('Unpaid') }}
                        </span>
                    @endif
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Created At') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em;">{{ $booking->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 5px solid #27ae60;">
            <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">
                {{ __('Customer Information') }}
            </h3>
            <div style="display: grid; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Name') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em; font-weight: 600;">{{ $booking->user->name }}</p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Email') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em;">{{ $booking->user->email }}</p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Phone') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em;">{{ $booking->user->phone ?? __('N/A') }}</p>
                </div>
            </div>
        </div>

        <!-- Showtime Information -->
        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 5px solid #9b59b6;">
            <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">
                {{ __('Showtime Information') }}
            </h3>
            <div style="display: grid; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Movie') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em; font-weight: 600;">{{ $booking->showtime->movie->title }}</p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Date & Time') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em;">
                        {{ $booking->showtime->date->format('d/m/Y') }} 
                        {{ $booking->showtime->start_time }} - {{ $booking->showtime->end_time }}
                    </p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Room') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em;">{{ $booking->showtime->room->name }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 5px solid #e67e22;">
            <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">
                {{ __('Payment Information') }}
            </h3>
            <div style="display: grid; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Price') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em;">{{ number_format($booking->price, 0, ',', '.') }} VNĐ</p>
                </div>
                @if($booking->voucher_amount > 0)
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Voucher Discount') }}</label>
                    <p style="margin: 0; color: #e74c3c; font-size: 1em;">-{{ number_format($booking->voucher_amount, 0, ',', '.') }} VNĐ</p>
                </div>
                @endif
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Total Price') }}</label>
                    <p style="margin: 0; color: #27ae60; font-size: 1.3em; font-weight: 700;">{{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</p>
                </div>
                @if($booking->payment_method)
                <div>
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.8em; text-transform: uppercase;">{{ __('Payment Method') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1em;">{{ $booking->payment_method }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Seats Information -->
    <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">
            {{ __('Booked Seats') }} ({{ $booking->seats->count() }})
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($booking->seats as $seat)
                <span style="padding: 8px 16px; background: #3498db; color: white; border-radius: 6px; font-size: 1em; font-weight: 600;">
                    {{ $seat->row }}{{ $seat->number }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Actions -->
    <div style="display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid #eee;">
        <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" style="display: inline;">
            @csrf
            <select name="status" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; margin-right: 10px;">
                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                <option value="canceled" {{ $booking->status == 'canceled' ? 'selected' : '' }}>{{ __('Canceled') }}</option>
                <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
            </select>
            <button type="submit" style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; font-weight: 600;">
                {{ __('Update Status') }}
            </button>
        </form>
        
        <form method="POST" action="{{ route('admin.bookings.update-payment', $booking->id) }}" style="display: inline;">
            @csrf
            <select name="is_paid" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; margin-right: 10px;">
                <option value="0" {{ !$booking->is_paid ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                <option value="1" {{ $booking->is_paid ? 'selected' : '' }}>{{ __('Paid') }}</option>
            </select>
            <input 
                type="text" 
                name="payment_method" 
                value="{{ $booking->payment_method }}" 
                placeholder="{{ __('Payment Method') }}"
                style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; margin-right: 10px;"
            >
            <button type="submit" style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; font-weight: 600;">
                {{ __('Update Payment') }}
            </button>
        </form>
    </div>
</div>
@endsection

