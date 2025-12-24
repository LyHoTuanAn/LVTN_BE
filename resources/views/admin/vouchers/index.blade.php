@extends('layouts.app')

@section('title', __('Voucher Management'))
@section('page-title', __('Voucher Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Voucher List') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.vouchers.index') }}" class="voucher-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $filters['search'] ?? '' }}" 
                    placeholder="{{ __('Search by code or name...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <select name="status" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="expired" {{ ($filters['status'] ?? '') === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                    <option value="disabled" {{ ($filters['status'] ?? '') === 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                </select>
                <select name="type" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="percentage" {{ ($filters['type'] ?? '') === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
                    <option value="fixed" {{ ($filters['type'] ?? '') === 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                </select>
                <button type="submit" style="padding: 0 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9em; line-height: 1; height: 38px; box-sizing: border-box;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.vouchers.create') }}" 
                style="padding: 0 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                + {{ __('Create Voucher') }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div style="padding: 12px 16px; background: #e8f5e9; color: #2e7d32; border-radius: 6px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="padding: 12px 16px; background: #ffebee; color: #c62828; border-radius: 6px; margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="width: 100%; overflow-x: auto;">
        <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 1000px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('ID') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Code') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Name') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Type') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Amount') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Usage') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Valid Period') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($vouchers as $voucher)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;" data-label="{{ __('ID') }}">{{ $voucher->id }}</td>
                    <td style="padding: 12px; font-weight: 500;" data-label="{{ __('Code') }}">{{ $voucher->code }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Name') }}">{{ $voucher->name }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Type') }}">
                        <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 4px; font-size: 0.85em; text-transform: capitalize;">
                            {{ $voucher->type }}
                        </span>
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Amount') }}">
                        @if($voucher->type === 'percentage')
                            {{ number_format($voucher->amount, 0, ',', '.') }}%
                        @else
                            {{ number_format($voucher->amount, 0, ',', '.') }} VND
                        @endif
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Usage') }}">
                        {{ number_format($voucher->used_count, 0, ',', '.') }}/{{ $voucher->usage_limit ? number_format($voucher->usage_limit, 0, ',', '.') : '∞' }}
                        @if($voucher->per_user_limit)
                            <br><small style="color: #666;">({{ __('Per user') }}: {{ number_format($voucher->per_user_limit, 0, ',', '.') }})</small>
                        @endif
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Valid Period') }}">
                        <small>{{ $voucher->valid_from->format('Y-m-d') }}</small><br>
                        <small>{{ $voucher->valid_to->format('Y-m-d') }}</small>
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Status') }}">
                        @php
                            $statusColors = [
                                'active' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                                'expired' => ['bg' => '#ffebee', 'color' => '#c62828'],
                                'disabled' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                            ];
                        @endphp
                        <span style="padding: 4px 8px; background: {{ $statusColors[$voucher->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$voucher->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.85em; text-transform: capitalize;">
                            {{ __($voucher->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Actions') }}">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a 
                                href="{{ route('admin.vouchers.show', $voucher->id) }}" 
                                style="padding: 0 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.vouchers.edit', $voucher->id) }}" 
                                style="padding: 0 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this voucher?') }}');">
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
                    <td colspan="9" style="padding: 40px; text-align: center; color: #666;">
                        {{ __('No Data') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $vouchers->links() }}
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .voucher-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .voucher-filter-form input,
        .voucher-filter-form select,
        .voucher-filter-form button {
            width: 100%;
        }
        .responsive-table {
            min-width: unset !important;
        }
        .responsive-table thead {
            display: none;
        }
        .responsive-table tr {
            display: block;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .responsive-table td {
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #555;
            margin-right: 10px;
        }
    }
</style>

