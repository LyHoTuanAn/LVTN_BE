@extends('layouts.admin')

@section('title', __('Booking Management'))
@section('page-title', __('Booking Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Booking List') }}</h2>
        <form method="GET" action="{{ route('admin.bookings.index') }}" style="display: flex; gap: 10px;">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="{{ __('Search by order code') }}"
                style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
            >
            <select name="status" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                <option value="">{{ __('All Status') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>{{ __('Canceled') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
            </select>
            <select name="is_paid" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                <option value="">{{ __('All Payment') }}</option>
                <option value="1" {{ request('is_paid') == '1' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                <option value="0" {{ request('is_paid') == '0' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
            </select>
            <button type="submit" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    @if(session('success'))
        <div style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Code') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Customer') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Movie') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Showtime') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Seats') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Total Price') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Payment') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr style="border-bottom: 1px solid #dee2e6; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                <td style="padding: 12px;">
                    <strong style="color: #3498db; font-family: monospace;">{{ $booking->code }}</strong>
                </td>
                <td style="padding: 12px;">
                    <div>
                        <div style="font-weight: 600; color: #2c3e50;">{{ $booking->user->name }}</div>
                        <div style="font-size: 0.85em; color: #7f8c8d;">{{ $booking->user->email }}</div>
                    </div>
                </td>
                <td style="padding: 12px;">
                    <div style="font-weight: 600; color: #2c3e50;">{{ $booking->showtime->movie->title }}</div>
                </td>
                <td style="padding: 12px;">
                    <div>
                        <div style="font-weight: 600; color: #2c3e50;">{{ $booking->showtime->date->format('d/m/Y') }}</div>
                        <div style="font-size: 0.85em; color: #7f8c8d;">{{ $booking->showtime->start_time }} - {{ $booking->showtime->end_time }}</div>
                        <div style="font-size: 0.85em; color: #7f8c8d;">{{ __('Room') }}: {{ $booking->showtime->room->name }}</div>
                    </div>
                </td>
                <td style="padding: 12px;">
                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                        @foreach($booking->seats as $seat)
                            <span style="padding: 4px 8px; background: #e8f4f8; color: #2c3e50; border-radius: 4px; font-size: 0.85em; font-weight: 600;">
                                {{ $seat->row }}{{ $seat->number }}
                            </span>
                        @endforeach
                    </div>
                </td>
                <td style="padding: 12px;">
                    <div style="font-weight: 600; color: #27ae60;">
                        {{ number_format($booking->total_price, 0, ',', '.') }} VNĐ
                    </div>
                    @if($booking->voucher_amount > 0)
                        <div style="font-size: 0.85em; color: #e74c3c;">
                            -{{ number_format($booking->voucher_amount, 0, ',', '.') }} VNĐ
                        </div>
                    @endif
                </td>
                <td style="padding: 12px;">
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
                    <span style="padding: 4px 12px; background: {{ $statusColors[$booking->status] ?? '#95a5a6' }}; color: white; border-radius: 12px; font-size: 0.85em; font-weight: 600;">
                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                    </span>
                </td>
                <td style="padding: 12px;">
                    @if($booking->is_paid)
                        <span style="padding: 4px 12px; background: #27ae60; color: white; border-radius: 12px; font-size: 0.85em; font-weight: 600;">
                            {{ __('Paid') }}
                        </span>
                    @else
                        <span style="padding: 4px 12px; background: #e74c3c; color: white; border-radius: 12px; font-size: 0.85em; font-weight: 600;">
                            {{ __('Unpaid') }}
                        </span>
                    @endif
                </td>
                <td style="padding: 12px;">
                    <a 
                        href="{{ route('admin.bookings.show', $booking->id) }}" 
                        style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                    >
                        {{ __('View') }}
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="padding: 40px; text-align: center; color: #7f8c8d;">
                    {{ __('No bookings found') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $bookings->links() }}
    </div>
</div>
@endsection

