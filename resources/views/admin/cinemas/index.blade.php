@extends('layouts.app')

@section('title', __('Cinema Management'))
@section('page-title', __('Cinema Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Cinema List') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.cinemas.index') }}" class="cinema-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by name...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <input 
                    type="text" 
                    name="location" 
                    value="{{ request('location') }}" 
                    placeholder="{{ __('Search by location...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <button type="submit" style="padding: 0 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9em; line-height: 1; height: 38px; box-sizing: border-box;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.cinemas.create') }}" 
                style="padding: 0 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                + {{ __('Add Cinema') }}
            </a>
            <a 
                href="{{ route('admin.cinemas.create-many') }}" 
                style="padding: 0 20px; background: #8e44ad; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                + {{ __('Add Multiple Cinemas') }}
            </a>
        </div>
    </div>

    <div style="width: 100%; overflow-x: auto;">
        <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('ID') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Name') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Location') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Address') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Phone') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Rooms') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($cinemas as $cinema)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;" data-label="{{ __('ID') }}">{{ $cinema->id }}</td>
                    <td style="padding: 12px; font-weight: 500;" data-label="{{ __('Name') }}">{{ $cinema->name }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Location') }}">{{ $cinema->location }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Address') }}">{{ Str::limit($cinema->address, 30) }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Phone') }}">{{ $cinema->phone ?? '-' }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Rooms') }}">
                        <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 4px; font-size: 0.85em;">
                            {{ $cinema->rooms->count() }} {{ __('rooms') }}
                        </span>
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Actions') }}">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a 
                                href="{{ route('admin.cinemas.show', $cinema->id) }}" 
                                style="padding: 0 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.cinemas.edit', $cinema->id) }}" 
                                style="padding: 0 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.cinemas.destroy', $cinema->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this cinema?') }}');">
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
                        {{ __('No cinemas found') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $cinemas->links() }}
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .cinema-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .cinema-filter-form input,
        .cinema-filter-form button {
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
</style>
