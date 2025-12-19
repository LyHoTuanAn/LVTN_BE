@extends('layouts.admin')

@section('title', __('Movie Management'))
@section('page-title', __('Movie Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Movie List') }}</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="{{ route('admin.movies.index') }}" style="display: flex; gap: 10px;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by title...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <select name="status" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="coming_soon" {{ request('status') == 'coming_soon' ? 'selected' : '' }}>{{ __('Coming Soon') }}</option>
                    <option value="now_showing" {{ request('status') == 'now_showing' ? 'selected' : '' }}>{{ __('Now Showing') }}</option>
                    <option value="trending" {{ request('status') == 'trending' ? 'selected' : '' }}>{{ __('Trending') }}</option>
                </select>
                <button type="submit" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.movies.create') }}" 
                style="padding: 8px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap;"
            >
                + {{ __('Add Movie') }}
            </a>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('ID') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Poster') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Title') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Duration') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Release Date') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movies as $movie)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $movie->id }}</td>
                    <td style="padding: 12px;">
                        @if ($movie->poster)
                            <img src="{{ asset('storage/' . $movie->poster->file_path) }}" alt="{{ $movie->title }}" style="width: 50px; height: 75px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div style="width: 50px; height: 75px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.7em;">
                                {{ __('No Image') }}
                            </div>
                        @endif
                    </td>
                    <td style="padding: 12px; font-weight: 500;">{{ $movie->title }}</td>
                    <td style="padding: 12px;">{{ $movie->duration }} {{ __('mins') }}</td>
                    <td style="padding: 12px;">{{ $movie->release_date->format('Y-m-d') }}</td>
                    <td style="padding: 12px;">
                        @php
                            $statusColors = [
                                'coming_soon' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                                'now_showing' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                                'trending' => ['bg' => '#fff8e1', 'color' => '#f57c00'],
                            ];
                            $statusLabels = [
                                'coming_soon' => __('Coming Soon'),
                                'now_showing' => __('Now Showing'),
                                'trending' => __('Trending'),
                            ];
                        @endphp
                        <span style="padding: 4px 8px; background: {{ $statusColors[$movie->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$movie->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.85em;">
                            {{ $statusLabels[$movie->status] ?? $movie->status }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <div style="display: flex; gap: 8px;">
                            <a 
                                href="{{ route('admin.movies.show', $movie->id) }}" 
                                style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.movies.edit', $movie->id) }}" 
                                style="padding: 6px 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.movies.destroy', $movie->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this movie?') }}');">
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
                    <td colspan="7" style="padding: 40px; text-align: center; color: #666;">
                        {{ __('No movies found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $movies->links() }}
    </div>
</div>
@endsection
