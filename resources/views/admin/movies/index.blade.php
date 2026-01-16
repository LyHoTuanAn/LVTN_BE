@extends('layouts.app')

@section('title', __('Movie Management'))
@section('page-title', __('Movie Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Movie List') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.movies.index') }}" class="movie-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by title...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <select name="status" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="COMING_SOON" {{ request('status') == 'COMING_SOON' ? 'selected' : '' }}>{{ __('Coming Soon') }}</option>
                    <option value="NOW_SHOWING" {{ request('status') == 'NOW_SHOWING' ? 'selected' : '' }}>{{ __('Now Showing') }}</option>
                </select>
                <button type="submit" style="padding: 0 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9em; line-height: 1; height: 38px; box-sizing: border-box;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.movies.create') }}" 
                style="padding: 0 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                + {{ __('Add Movie') }}
            </a>
        </div>
    </div>

    <div style="width: 100%; overflow-x: auto;">
        <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
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
                    <td style="padding: 12px;" data-label="{{ __('ID') }}">{{ $movie->id }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Poster') }}">
                        @if ($movie->poster)
                            <img src="{{ asset('storage/' . $movie->poster->file_path) }}" alt="{{ $movie->title }}" style="width: 50px; height: 75px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div style="width: 50px; height: 75px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.7em;">
                                {{ __('No Image') }}
                            </div>
                        @endif
                    </td>
                    <td style="padding: 12px; font-weight: 500;" data-label="{{ __('Title') }}">{{ $movie->title }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Duration') }}">{{ $movie->duration }} {{ __('mins') }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Release Date') }}">{{ $movie->release_date->format('Y-m-d') }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Status') }}">
                        @php
                            $computedStatus = $movie->getComputedStatus();
                            $statusColors = [
                                'COMING_SOON' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                                'NOW_SHOWING' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                            ];
                            $statusLabels = [
                                'COMING_SOON' => __('Coming Soon'),
                                'NOW_SHOWING' => __('Now Showing'),
                            ];
                        @endphp
                        <span style="padding: 4px 8px; background: {{ $statusColors[$computedStatus]['bg'] ?? '#eee' }}; color: {{ $statusColors[$computedStatus]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.85em;">
                            {{ $statusLabels[$computedStatus] ?? $computedStatus }}
                        </span>
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Actions') }}">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a 
                                href="{{ route('admin.movies.show', $movie->id) }}" 
                                style="padding: 0 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.movies.edit', $movie->id) }}" 
                                style="padding: 0 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.movies.destroy', $movie->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this movie?') }}');">
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
                    <td colspan="7" style="padding: 40px; text-align: center; color: #666;">
                        {{ __('No movies found') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $movies->links() }}
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .movie-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .movie-filter-form input,
        .movie-filter-form select,
        .movie-filter-form button {
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
        .responsive-table td img {
            margin-left: auto;
        }
        .responsive-table td div {
            text-align: right;
        }
        .responsive-table td form,
        .responsive-table td a,
        .responsive-table td button {
            width: 100%;
        }
        .responsive-table td a,
        .responsive-table td button {
            text-align: center;
        }
    }
</style>
