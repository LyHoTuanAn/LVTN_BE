@extends('layouts.app')

@section('title', __('Room Management'))
@section('page-title', __('Room Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Room List') }}</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="{{ route('admin.rooms.index') }}" style="display: flex; gap: 10px;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by name...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <button type="submit" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.rooms.create') }}" 
                style="padding: 8px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap;"
            >
                + {{ __('Add Room') }}
            </a>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('ID') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Name') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Cinema') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Seat Count') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Showtimes') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rooms as $room)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $room->id }}</td>
                    <td style="padding: 12px; font-weight: 500;">{{ $room->name }}</td>
                    <td style="padding: 12px;">{{ $room->cinema?->name ?? '-' }}</td>
                    <td style="padding: 12px;">
                        <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 4px; font-size: 0.85em;">
                            {{ $room->seat_count }} {{ __('seats') }}
                        </span>
                    </td>
                    <td style="padding: 12px;">{{ $room->showtimes?->count() ?? 0 }}</td>
                    <td style="padding: 12px;">
                        <div style="display: flex; gap: 8px;">
                            <a 
                                href="{{ route('admin.rooms.show', $room->id) }}" 
                                style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.rooms.edit', $room->id) }}" 
                                style="padding: 6px 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.rooms.destroy', $room->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this room?') }}');">
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
                    <td colspan="6" style="padding: 40px; text-align: center; color: #666;">
                        {{ __('No rooms found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
