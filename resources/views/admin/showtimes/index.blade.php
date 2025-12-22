@extends('layouts.app')

@section('title', __('Showtime Management'))
@section('page-title', __('Showtime Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Showtime List') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.showtimes.index') }}" class="showtimes-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="movie_id" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em; height: 38px; line-height: 1; box-sizing: border-box;">
                    <option value="">{{ __('All Movies') }}</option>
                    @foreach ($movies as $movie)
                        <option value="{{ $movie->id }}" {{ request('movie_id') == $movie->id ? 'selected' : '' }}>{{ $movie->title }}</option>
                    @endforeach
                </select>
                <select name="room_id" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em; height: 38px; line-height: 1; box-sizing: border-box;">
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
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em; background: white; height: 38px; line-height: 1; box-sizing: border-box;"
                >
                <button type="submit" style="padding: 0 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9em; line-height: 1; height: 38px; box-sizing: border-box; vertical-align: middle;">
                    {{ __('Filter') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.showtimes.create') }}" 
                style="padding: 0 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 38px; box-sizing: border-box; vertical-align: middle;"
            >
                + {{ __('Add Showtime') }}
            </a>
        </div>
    </div>

    <div style="width: 100%; overflow-x: auto;">
        <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
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
                    <td style="padding: 12px;" data-label="{{ __('ID') }}">{{ $showtime->id }}</td>
                    <td style="padding: 12px; font-weight: 500;" data-label="{{ __('Movie') }}">{{ $showtime->movie?->title ?? '-' }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Room') }}">{{ $showtime->room?->name ?? '-' }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Date') }}">{{ $showtime->date->format('d/m/Y') }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Time') }}">{{ $showtime->start_time }} - {{ $showtime->end_time }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Price') }}">{{ number_format($showtime->price, 0, ',', '.') }} VNĐ</td>
                    <td style="padding: 12px;" data-label="{{ __('Status') }}">
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
                    <td style="padding: 12px;" data-label="{{ __('Actions') }}">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a 
                                href="{{ route('admin.showtimes.show', $showtime->id) }}" 
                                style="padding: 0 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.showtimes.edit', $showtime->id) }}" 
                                style="padding: 0 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.showtimes.destroy', $showtime->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this showtime?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 0 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 0.85em; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;">
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
    </div>

    <div style="margin-top: 20px;">
        {{ $showtimes->links() }}
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .showtimes-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .showtimes-filter-form select,
        .showtimes-filter-form input,
        .showtimes-filter-form button {
            width: 100%;
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
        .responsive-table td div {
            text-align: right;
        }
        .responsive-table td a,
        .responsive-table td button,
        .responsive-table td form {
            width: 100%;
        }
        .responsive-table td a,
        .responsive-table td button {
            text-align: center;
        }
    }
    @media (min-width: 769px) {
        .showtimes-filter-form {
            flex-wrap: nowrap;
            align-items: center;
        }
        .showtimes-filter-form select,
        .showtimes-filter-form input,
        .showtimes-filter-form button {
            width: auto;
        }
        /* Override width 100% của flatpickr alt input trong app layout cho ô Filter by date (desktop) */
        .showtimes-filter-form .flatpickr-alt-input {
            width: auto !important;
            max-width: 220px;
            display: inline-block;
            height: 38px !important;
            line-height: 1 !important;
            box-sizing: border-box;
        }
    }
</style>
