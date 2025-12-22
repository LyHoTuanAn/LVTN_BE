@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-stats-card 
            title="{{ __('Total Users') }}" 
            value="10,482" 
            change="+20.1% {{ __('from last month') }}"
        />
        <x-stats-card 
            title="{{ __('Active Users') }}" 
            value="8,350" 
            change="+15% {{ __('from last month') }}"
        />
        <x-stats-card 
            title="{{ __('Total Transactions') }}" 
            value="45,231" 
            change="+34% {{ __('from last month') }}"
        />
        <x-stats-card 
            title="{{ __('Revenue') }}" 
            value="$567,890" 
            change="+18.7% {{ __('from last month') }}"
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
                    labels: ['{{ __('Jan') }}', '{{ __('Feb') }}', '{{ __('Mar') }}', '{{ __('Apr') }}', '{{ __('May') }}', '{{ __('Jun') }}', '{{ __('Jul') }}', '{{ __('Aug') }}', '{{ __('Sep') }}', '{{ __('Oct') }}', '{{ __('Nov') }}', '{{ __('Dec') }}'],
                    datasets: [{
                        label: '{{ __('Revenue') }}',
                        data: [2500, 3200, 2800, 4100, 3500, 4800, 3900, 5200, 4600, 5800, 4200, 6100],
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
                    labels: ['2023-05-01', '2023-05-02', '2023-05-03', '2023-05-04', '2023-05-05', '2023-05-06', '2023-05-07'],
                    datasets: [
                        { label: '{{ __('Logins') }}', data: [200, 220, 240, 280, 300, 320, 340], borderColor: '#8884d8', tension: 0.3, fill: false },
                        { label: '{{ __('Transactions') }}', data: [150, 160, 180, 200, 220, 240, 260], borderColor: '#82ca9d', tension: 0.3, fill: false },
                        { label: '{{ __('API Calls') }}', data: [1000, 1100, 1200, 1300, 1400, 1500, 1600], borderColor: '#ffc658', tension: 0.3, fill: false }
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

