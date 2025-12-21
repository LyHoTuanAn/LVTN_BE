@extends('layouts.app')

@section('title', __('Showtime Management'))
@section('page-title', __('Showtime Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Showtime List') }}</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="{{ route('admin.showtimes.index') }}" style="display: flex; gap: 10px;">
                <select name="movie_id" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                    <option value="">{{ __('All Movies') }}</option>
                    @foreach ($movies as $movie)
                        <option value="{{ $movie->id }}" {{ request('movie_id') == $movie->id ? 'selected' : '' }}>{{ $movie->title }}</option>
                    @endforeach
                </select>
                <select name="room_id" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                    <option value="">{{ __('All Rooms') }}</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
                <input 
                    type="text" 
                    name="date" 
                    class="datepicker"
                    value="{{ request('date') }}" 
                    placeholder="{{ __('Filter by date') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em; background: white;"
                >
                <button type="submit" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    {{ __('Filter') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.showtimes.create') }}" 
                style="padding: 8px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap;"
            >
                + {{ __('Add Showtime') }}
            </a>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('ID') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Movie') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Room') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Date') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Time') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Price') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($showtimes as $showtime)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $showtime->id }}</td>
                    <td style="padding: 12px; font-weight: 500;">{{ $showtime->movie?->title ?? '-' }}</td>
                    <td style="padding: 12px;">{{ $showtime->room?->name ?? '-' }}</td>
                    <td style="padding: 12px;">{{ $showtime->date->format('d/m/Y') }}</td>
                    <td style="padding: 12px;">{{ $showtime->start_time }} - {{ $showtime->end_time }}</td>
                    <td style="padding: 12px;">{{ number_format($showtime->price, 0, ',', '.') }} VNĐ</td>
                    <td style="padding: 12px;">
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
                        <span style="padding: 4px 8px; background: {{ $statusColors[$showtime->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$showtime->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.85em;">
                            {{ $statusLabels[$showtime->status] ?? $showtime->status }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <div style="display: flex; gap: 8px;">
                            <a 
                                href="{{ route('admin.showtimes.show', $showtime->id) }}" 
                                style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.showtimes.edit', $showtime->id) }}" 
                                style="padding: 6px 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.showtimes.destroy', $showtime->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this showtime?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 6px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 0.85em; cursor: pointer;">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #666;">
                        {{ __('No showtimes found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $showtimes->links() }}
    </div>
</div>
@endsection
