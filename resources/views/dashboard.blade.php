@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-stats-card 
            title="{{ __('Total Users') }}" 
            value="{{ $stats['total_users']['value'] }}" 
            change="{{ $stats['total_users']['change'] }} {{ __('from last month') }}"
        />
        <x-stats-card 
            title="{{ __('Active Users') }}" 
            value="{{ $stats['active_users']['value'] }}" 
            change="{{ $stats['active_users']['change'] }} {{ __('from last month') }}"
        />
        <x-stats-card 
            title="{{ __('Total Transactions') }}" 
            value="{{ $stats['total_bookings']['value'] }}" 
            change="{{ $stats['total_bookings']['change'] }} {{ __('from last month') }}"
        />
        <x-stats-card 
            title="{{ __('Revenue') }}" 
            value="{{ $stats['revenue']['value'] }}" 
            change="{{ $stats['revenue']['change'] }} {{ __('from last month') }}"
        />
    </div>
    
    {{-- Charts Row --}}
    <div class="grid gap-4 md:grid-cols-1">
        <x-card-container title="{{ __('Overview') }}">
            <x-chart-container id="overviewChart" height="350" />
        </x-card-container>
    </div>
    
    {{-- User Activity --}}
    <x-card-container title="{{ __('User Activity') }}">
        <x-chart-container id="userActivityChart" height="300" />
    </x-card-container>
</div>

@push('scripts')
<script>
    // Overview Chart
    document.addEventListener('DOMContentLoaded', function() {
        const overviewCtx = document.getElementById('overviewChart');
        if (overviewCtx) {
            new Chart(overviewCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($revenueData['labels']),
                    datasets: [{
                        label: '{{ __('Revenue') }}',
                        data: @json($revenueData['data']),
                        backgroundColor: '#adfa1d',
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: value => '$' + value } }
                    }
                }
            });
        }
        
        // User Activity Chart
        const activityCtx = document.getElementById('userActivityChart');
        if (activityCtx) {
            new Chart(activityCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($activityData['labels']),
                    datasets: [
                        { 
                            label: '{{ __('Logins') }}', 
                            data: @json($activityData['datasets']['logins']), 
                            borderColor: '#8884d8', 
                            tension: 0.3, 
                            fill: false 
                        },
                        { 
                            label: '{{ __('Transactions') }}', 
                            data: @json($activityData['datasets']['transactions']), 
                            borderColor: '#82ca9d', 
                            tension: 0.3, 
                            fill: false 
                        },
                        { 
                            label: '{{ __('API Calls') }}', 
                            data: @json($activityData['datasets']['api_calls']), 
                            borderColor: '#ffc658', 
                            tension: 0.3, 
                            fill: false 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    });
</script>
@endpush
@endsection

