@extends('layouts.app')

@section('title', __('Voucher Details'))
@section('page-title', __('Voucher Details'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <h2 style="color: #2c3e50; font-size: 1.5em; margin: 0;">{{ __('Voucher Details') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.vouchers.index') }}" style="padding: 10px 16px; background: #e0e0e0; color: #2c3e50; text-decoration: none; border-radius: 6px; font-weight: 600;">
                {{ __('Back to List') }}
            </a>
            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" style="padding: 10px 16px; background: #f39c12; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                {{ __('Edit') }}
            </a>
        </div>
    </div>

    <div style="display: grid; gap: 24px;">
        <!-- Basic Information -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Code') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; font-family: monospace; font-weight: 600;">
                    {{ $voucher->code }}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Status') }}</label>
                <div style="padding: 12px;">
                    @php
                        $statusColors = [
                            'active' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                            'expired' => ['bg' => '#ffebee', 'color' => '#c62828'],
                            'disabled' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                        ];
                    @endphp
                    <span style="padding: 6px 12px; background: {{ $statusColors[$voucher->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$voucher->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.9em; text-transform: capitalize; display: inline-block;">
                        {{ __($voucher->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Name') }}</label>
            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                {{ $voucher->name }}
            </div>
        </div>

        <!-- Type and Amount -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Type') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 4px; font-size: 0.9em; text-transform: capitalize;">
                        {{ $voucher->type }}
                    </span>
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Amount') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; font-weight: 600; font-size: 1.1em;">
                    @if($voucher->type === 'percentage')
                        {{ number_format($voucher->amount, 0, ',', '.') }}%
                    @else
                        {{ number_format($voucher->amount, 0, ',', '.') }} VND
                    @endif
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Total Usage Limit') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $voucher->usage_limit ? number_format($voucher->usage_limit, 0, ',', '.') : __('Unlimited') }}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Per User Limit') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $voucher->per_user_limit ? number_format($voucher->per_user_limit, 0, ',', '.') : __('Unlimited') }}
                </div>
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Used Count') }}</label>
            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                {{ number_format($voucher->used_count, 0, ',', '.') }}
            </div>
        </div>

        <!-- Valid Period -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Valid From') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $voucher->valid_from->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Valid To') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $voucher->valid_to->format('Y-m-d H:i:s') }}
                </div>
            </div>
        </div>

        <!-- Applies To -->
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Applies To') }}</label>
            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $voucher->applies_to) }}</span>
            </div>
        </div>

        @if($voucher->applies_to === 'specific_users' && $voucher->only_for_user)
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Only For User IDs') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; font-family: monospace;">
                    {{ $voucher->only_for_user }}
                </div>
            </div>
        @endif

        @if($voucher->applies_to === 'specific_movies' && $voucher->only_for_movie)
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Only For Movie IDs') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; font-family: monospace;">
                    {{ $voucher->only_for_movie }}
                </div>
            </div>
        @endif

        <!-- Timestamps -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Created At') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $voucher->created_at->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Updated At') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $voucher->updated_at->format('Y-m-d H:i:s') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

