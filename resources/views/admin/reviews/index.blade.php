@extends('layouts.app')

@section('title', __('Review Management'))
@section('page-title', __('Review Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Review List') }}</h2>
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="review-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="{{ __('Search by user or comment') }}"
                style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em; min-width: 200px;"
            >
            <select name="status" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                <option value="">{{ __('All Status') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
            </select>
            <select name="rating" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                <option value="">{{ __('All Ratings') }}</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
                @endfor
            </select>
            <select name="movie_id" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em; max-width: 200px;">
                <option value="">{{ __('All Movies') }}</option>
                @foreach($movies as $movie)
                    <option value="{{ $movie->id }}" {{ request('movie_id') == $movie->id ? 'selected' : '' }}>{{ Str::limit($movie->title, 30) }}</option>
                @endforeach
            </select>
            <button type="submit" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                {{ __('Filter') }}
            </button>
            @if(request()->hasAny(['search', 'status', 'rating', 'movie_id']))
                <a href="{{ route('admin.reviews.index') }}" style="padding: 8px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px;">
                    {{ __('Clear') }}
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="width: 100%; overflow-x: auto;">
        <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 900px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('User') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Movie') }}</th>
                    <th style="padding: 12px; text-align: center; color: #2c3e50; font-weight: 600;">{{ __('Rating') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Comment') }}</th>
                    <th style="padding: 12px; text-align: center; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Date') }}</th>
                    <th style="padding: 12px; text-align: center; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reviews as $review)
            <tr style="border-bottom: 1px solid #dee2e6; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                <td style="padding: 12px;" data-label="{{ __('User') }}">
                    <div>
                        <div style="font-weight: 600; color: #2c3e50;">{{ $review->user->name }}</div>
                        <div style="font-size: 0.85em; color: #7f8c8d;">{{ $review->user->email }}</div>
                    </div>
                </td>
                <td style="padding: 12px;" data-label="{{ __('Movie') }}">
                    <div style="font-weight: 600; color: #2c3e50;">{{ Str::limit($review->movie->title, 30) }}</div>
                </td>
                <td style="padding: 12px; text-align: center;" data-label="{{ __('Rating') }}">
                    <div style="display: flex; justify-content: center; align-items: center; gap: 4px;">
                        <span style="font-size: 1.2em; color: #f39c12;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </span>
                        <span style="font-weight: 600; color: #2c3e50;">({{ $review->rating }})</span>
                    </div>
                </td>
                <td style="padding: 12px;" data-label="{{ __('Comment') }}">
                    @if($review->comment)
                        <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $review->comment }}">
                            {{ Str::limit($review->comment, 50) }}
                        </div>
                    @else
                        <span style="color: #95a5a6; font-style: italic;">{{ __('No comment') }}</span>
                    @endif
                </td>
                <td style="padding: 12px; text-align: center;" data-label="{{ __('Status') }}">
                    @php
                        $statusLabels = [
                            'pending' => __('Pending'),
                            'approved' => __('Approved'),
                            'rejected' => __('Rejected'),
                        ];
                        $statusColors = [
                            'pending' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                            'approved' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                            'rejected' => ['bg' => '#ffebee', 'color' => '#c62828'],
                        ];
                    @endphp
                    <span style="padding: 4px 10px; background: {{ $statusColors[$review->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$review->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.85em; font-weight: 500;">
                        {{ $statusLabels[$review->status] ?? $review->status }}
                    </span>
                </td>
                <td style="padding: 12px;" data-label="{{ __('Date') }}">
                    <div style="font-size: 0.9em; color: #2c3e50;">{{ $review->created_at->format('d/m/Y') }}</div>
                    <div style="font-size: 0.85em; color: #7f8c8d;">{{ $review->created_at->format('H:i') }}</div>
                </td>
                <td style="padding: 12px; text-align: center;" data-label="{{ __('Actions') }}">
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <a 
                            href="{{ route('admin.reviews.show', $review->id) }}" 
                            style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em;"
                        >
                            {{ __('View') }}
                        </a>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}')">
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
                <td colspan="7" style="padding: 40px; text-align: center; color: #7f8c8d;">
                    {{ __('No reviews found') }}
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $reviews->links() }}
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .review-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .review-filter-form input,
        .review-filter-form select,
        .review-filter-form button,
        .review-filter-form a {
            width: 100%;
            text-align: center;
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
            min-width: 100px;
        }
        .responsive-table td div {
            text-align: right;
        }
    }
</style>
