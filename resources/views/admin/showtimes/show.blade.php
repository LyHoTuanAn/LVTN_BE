@extends('layouts.app')

@section('title', __('Showtime Details'))
@section('page-title', __('Showtime Details'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.showtimes.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to List') }}
        </a>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a 
                href="{{ route('admin.showtimes.seat-map', $showtime->id) }}" 
                style="padding: 0 20px; background: #9b59b6; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; display: inline-flex; align-items: center; justify-content: center; gap: 6px; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                🎫 {{ __('Seat Map') }}
            </a>
            <a 
                href="{{ route('admin.showtimes.edit', $showtime->id) }}" 
                style="padding: 0 20px; background: #f39c12; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; display: inline-flex; align-items: center; justify-content: center; gap: 6px; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                {{ __('Edit') }}
            </a>
            <form method="POST" action="{{ route('admin.showtimes.destroy', $showtime->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this showtime?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 0 20px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 0.9em; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; line-height: 1; height: 38px; box-sizing: border-box;">
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.8em; margin-bottom: 30px;">{{ $showtime->movie?->title ?? __('Showtime') }} #{{ $showtime->id }}</h2>

    <div class="showtime-info-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Movie') }}</label>
            <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $showtime->movie?->title ?? '-' }}</p>
        </div>

        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Room') }}</label>
            <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $showtime->room?->name ?? '-' }}</p>
        </div>

        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Status') }}</label>
            @php
                $statusColors = [
                    'scheduled' => ['bg' => '#e3f2fd', 'color' => '#1976d2'],
                    'ongoing' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                    'completed' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                    'cancelled' => ['bg' => '#ffebee', 'color' => '#c62828'],
                ];
                $statusLabels = [
                    'scheduled' => __('Scheduled'),
                    'ongoing' => __('Ongoing'),
                    'completed' => __('Completed'),
                    'cancelled' => __('Cancelled'),
                ];
            @endphp
            <span style="padding: 4px 12px; background: {{ $statusColors[$showtime->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$showtime->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.9em;">
                {{ $statusLabels[$showtime->status] ?? $showtime->status }}
            </span>
        </div>

        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Date') }}</label>
            <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $showtime->date->format('d/m/Y') }}</p>
        </div>

        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Time') }}</label>
            <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $showtime->start_time }} - {{ $showtime->end_time }}</p>
        </div>

        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Price') }}</label>
            <p style="margin: 0; color: #27ae60; font-size: 1.1em; font-weight: 600;">{{ number_format($showtime->price, 0, ',', '.') }} VNĐ</p>
        </div>
    </div>

    <!-- Bookings Section -->
    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee;">
        <h3 style="color: #2c3e50; font-size: 1.3em; margin-bottom: 20px;">{{ __('Bookings') }} ({{ $showtime->bookings?->count() ?? 0 }})</h3>

        @if($showtime->bookings && $showtime->bookings->count() > 0)
            <div style="width: 100%; overflow-x: auto;">
                <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 700px;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Code') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Customer') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Total Price') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Paid') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($showtime->bookings as $booking)
                        @php
                            $bookingStatusLabels = [
                                'pending' => __('Pending'),
                                'confirmed' => __('Confirmed'),
                                'canceled' => __('Canceled'),
                                'completed' => __('Completed'),
                            ];
                            $bookingStatusColors = [
                                'pending' => ['bg' => '#e3f2fd', 'color' => '#1976d2'],
                                'confirmed' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                                'canceled' => ['bg' => '#ffebee', 'color' => '#c62828'],
                                'completed' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                            ];
                        @endphp
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; font-weight: 500;" data-label="{{ __('Code') }}">{{ $booking->code }}</td>
                            <td style="padding: 12px;" data-label="{{ __('Customer') }}">{{ $booking->user?->name ?? '-' }}</td>
                            <td style="padding: 12px;" data-label="{{ __('Total Price') }}">{{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</td>
                            <td style="padding: 12px;" data-label="{{ __('Status') }}">
                                <span style="padding: 4px 8px; background: {{ $bookingStatusColors[$booking->status]['bg'] ?? '#eee' }}; color: {{ $bookingStatusColors[$booking->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.85em;">
                                    {{ $bookingStatusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td style="padding: 12px;" data-label="{{ __('Paid') }}">
                                @if ($booking->is_paid)
                                    <span style="color: #27ae60;">✓ {{ __('Yes') }}</span>
                                @else
                                    <span style="color: #e74c3c;">✗ {{ __('No') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 30px; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
                {{ __('No bookings found for this showtime') }}
            </div>
        @endif
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .showtime-info-grid {
            grid-template-columns: 1fr !important;
        }
        .responsive-table {
            min-width: unset !important;
        }
        .responsive-table thead {
            display: none;
        }
        .responsive-table,
        .responsive-table tbody,
        .responsive-table tr,
        .responsive-table td {
            display: block;
            width: 100%;
        }
        .responsive-table tr {
            margin-bottom: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .responsive-table td {
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .responsive-table td:last-child {
            border-bottom: none;
        }
        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #7f8c8d;
            flex-shrink: 0;
            min-width: 120px;
        }
        .responsive-table td span {
            text-align: right;
        }
    }
</style>
