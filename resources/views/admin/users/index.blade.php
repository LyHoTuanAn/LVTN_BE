@extends('layouts.app')

@section('title', __('User Management'))
@section('page-title', __('User Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('User List') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.users.index') }}" class="users-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by name, email, phone...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <button type="submit" style="padding: 0 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9em; line-height: 1; height: 38px; box-sizing: border-box;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.users.create') }}" 
                style="padding: 0 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                + {{ __('Add User') }}
            </a>
        </div>
    </div>

    <div style="width: 100%; overflow-x: auto;">
        <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('ID') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Name') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Email') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Phone') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Role') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Email Verified') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Created At') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;" data-label="{{ __('ID') }}">{{ $user->id }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Name') }}">{{ $user->name }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Email') }}">{{ $user->email }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Phone') }}">{{ $user->phone ?? '-' }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Role') }}">
                        <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 4px; font-size: 0.85em;">
                            {{ $user->role?->name ?? '-' }}
                        </span>
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Email Verified') }}">
                        @if ($user->email_verified_at)
                            <span style="padding: 4px 8px; background: #e8f5e9; color: #2e7d32; border-radius: 4px; font-size: 0.85em;">
                                {{ __('Verified') }}
                            </span>
                        @else
                            <span style="padding: 4px 8px; background: #ffebee; color: #c62828; border-radius: 4px; font-size: 0.85em;">
                                {{ __('Not Verified') }}
                            </span>
                        @endif
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Created At') }}">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Actions') }}">
                        <a 
                            href="{{ route('admin.users.show', $user->id) }}" 
                            style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-block;"
                        >
                            {{ __('View Details') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #666;">
                        {{ __('No Data') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
<style>
    @media (max-width: 768px) {
        .users-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .users-filter-form input,
        .users-filter-form button {
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
        .responsive-table td a {
            width: 100%;
            text-align: center;
        }
    }
</style>
