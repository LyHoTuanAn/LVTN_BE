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
            title="{{ __('Total Movies') }}" 
            value="{{ $stats['total_movies']['value'] }}" 
            change="{{ $stats['total_movies']['change'] }} {{ __('from last month') }}"
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
    
    {{-- Popular Movies --}}
    <x-card-container title="{{ __('Popular Movies') }}">
        <x-chart-container id="popularMoviesChart" height="300" />
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
        
        // Popular Movies Chart
        const moviesCtx = document.getElementById('popularMoviesChart');
        if (moviesCtx) {
            new Chart(moviesCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($moviesData['labels']),
                    datasets: [{
                        label: '{{ __('Number of Bookings') }}',
                        data: @json($moviesData['data']),
                        backgroundColor: [
                            '#8884d8',
                            '#82ca9d',
                            '#ffc658',
                            '#ff8042',
                            '#a4de6c',
                            '#d0ed57',
                            '#83a6ed',
                            '#8dd1e1',
                            '#d084d8',
                            '#ffbb96'
                        ],
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection

