@extends('layouts.app')

@section('title', $room->name)
@section('page-title', __('Room Details'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('admin.rooms.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to List') }}
        </a>
        <div style="display: flex; gap: 10px;">
            <a 
                href="{{ route('admin.rooms.edit', $room->id) }}" 
                style="padding: 8px 20px; background: #f39c12; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em;"
            >
                {{ __('Edit') }}
            </a>
            <form method="POST" action="{{ route('admin.rooms.destroy', $room->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this room?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 8px 20px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 0.9em; cursor: pointer;">
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.8em; margin-bottom: 30px;">{{ $room->name }}</h2>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Cinema') }}</label>
            <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $room->cinema?->name ?? '-' }}</p>
        </div>

        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Seat Count') }}</label>
            <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">
                <span style="padding: 4px 12px; background: #e3f2fd; color: #1976d2; border-radius: 4px;">
                    {{ $room->seat_count }} {{ __('seats') }}
                </span>
            </p>
        </div>

        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 5px; font-size: 0.9em;">{{ __('Created At') }}</label>
            <p style="margin: 0; color: #2c3e50; font-size: 1.1em;">{{ $room->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Showtimes Section -->
    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee;">
        <h3 style="color: #2c3e50; font-size: 1.3em; margin-bottom: 20px;">{{ __('Showtimes') }} ({{ $room->showtimes?->count() ?? 0 }})</h3>

        @if($room->showtimes && $room->showtimes->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Movie') }}</th>
                        <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Date') }}</th>
                        <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Time') }}</th>
                        <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Price') }}</th>
                        <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($room->showtimes as $showtime)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">{{ $showtime->movie?->title ?? '-' }}</td>
                            <td style="padding: 12px;">{{ $showtime->date->format('d/m/Y') }}</td>
                            <td style="padding: 12px;">{{ $showtime->start_time }} - {{ $showtime->end_time }}</td>
                            <td style="padding: 12px;">{{ number_format($showtime->price, 0, ',', '.') }} VNĐ</td>
                            <td style="padding: 12px;">
                                <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 4px; font-size: 0.85em;">
                                    {{ ucfirst($showtime->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 30px; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
                {{ __('No showtimes found for this room') }}
            </div>
        @endif
    </div>
</div>
@endsection
