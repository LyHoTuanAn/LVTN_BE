@extends('layouts.app')

@section('title', __('Review Details'))
@section('page-title', __('Review Details'))

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    @if(session('success'))
        <div style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Back Button -->
    <a href="{{ route('admin.reviews.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; margin-bottom: 20px;">
        ← {{ __('Back to Reviews') }}
    </a>

    <!-- Main Review Card -->
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); margin-bottom: 20px;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 5px;">{{ __('Review') }} #{{ $review->id }}</h2>
                <p style="color: #7f8c8d; font-size: 0.95em;">{{ __('Created') }}: {{ $review->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div style="display: flex; gap: 10px;">
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
                <span style="padding: 8px 16px; background: {{ $statusColors[$review->status]['bg'] }}; color: {{ $statusColors[$review->status]['color'] }}; border-radius: 20px; font-size: 0.95em; font-weight: 600;">
                    {{ $statusLabels[$review->status] ?? $review->status }}
                </span>
            </div>
        </div>

        <!-- Rating -->
        <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%); border-radius: 12px; margin-bottom: 25px;">
            <div style="font-size: 3em; color: #f39c12; margin-bottom: 10px;">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <div style="font-size: 1.5em; font-weight: 700; color: #2c3e50;">{{ $review->rating }}/5 {{ __('Stars') }}</div>
        </div>

        <!-- User Info -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px;">
            <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h4 style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; text-transform: uppercase;">{{ __('User') }}</h4>
                <div style="font-weight: 600; color: #2c3e50; font-size: 1.1em;">{{ $review->user->name }}</div>
                <div style="color: #7f8c8d; font-size: 0.95em;">{{ $review->user->email }}</div>
            </div>
            <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h4 style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; text-transform: uppercase;">{{ __('Movie') }}</h4>
                <div style="font-weight: 600; color: #2c3e50; font-size: 1.1em;">{{ $review->movie->title }}</div>
                @if($review->movie->poster)
                    <img src="{{ $review->movie->poster->url }}" alt="{{ $review->movie->title }}" style="width: 80px; height: 120px; object-fit: cover; border-radius: 6px; margin-top: 10px;">
                @endif
            </div>
        </div>

        <!-- Comment -->
        <div style="margin-bottom: 25px;">
            <h4 style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; text-transform: uppercase;">{{ __('Comment') }}</h4>
            <div style="padding: 20px; background: #f8f9fa; border-radius: 10px; min-height: 80px;">
                @if($review->comment)
                    <p style="color: #2c3e50; line-height: 1.6; white-space: pre-wrap;">{{ $review->comment }}</p>
                @else
                    <p style="color: #95a5a6; font-style: italic;">{{ __('No comment provided') }}</p>
                @endif
            </div>
        </div>

        <!-- Media -->
        @if($review->media)
        <div style="margin-bottom: 25px;">
            <h4 style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; text-transform: uppercase;">{{ __('Attached Media') }}</h4>
            <div style="padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <img src="{{ $review->media->url }}" alt="Review Media" style="max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px;">
            </div>
        </div>
        @endif

        <!-- Booking Info (if exists) -->
        @if($review->booking)
        <div style="margin-bottom: 25px;">
            <h4 style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; text-transform: uppercase;">{{ __('Associated Booking') }}</h4>
            <div style="padding: 20px; background: #e3f2fd; border-radius: 10px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                    <div>
                        <div style="color: #7f8c8d; font-size: 0.85em;">{{ __('Booking Code') }}</div>
                        <div style="font-weight: 600; color: #1976d2; font-family: monospace; font-size: 1.1em;">{{ $review->booking->code }}</div>
                    </div>
                    @if($review->booking->showtime)
                    <div>
                        <div style="color: #7f8c8d; font-size: 0.85em;">{{ __('Showtime') }}</div>
                        <div style="font-weight: 600; color: #2c3e50;">{{ $review->booking->showtime->date->format('d/m/Y') }} {{ $review->booking->showtime->start_time }}</div>
                    </div>
                    <div>
                        <div style="color: #7f8c8d; font-size: 0.85em;">{{ __('Room') }}</div>
                        <div style="font-weight: 600; color: #2c3e50;">{{ $review->booking->showtime->room->name ?? 'N/A' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Status Update Card -->
    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); margin-bottom: 20px;">
        <h3 style="color: #2c3e50; margin-bottom: 20px;">{{ __('Update Review Status') }}</h3>
        <form method="POST" action="{{ route('admin.reviews.update-status', $review->id) }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            @csrf
            <select name="status" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; min-width: 200px;">
                <option value="pending" {{ $review->status == 'pending' ? 'selected' : '' }}>⏳ {{ __('Pending') }}</option>
                <option value="approved" {{ $review->status == 'approved' ? 'selected' : '' }}>✅ {{ __('Approved') }}</option>
                <option value="rejected" {{ $review->status == 'rejected' ? 'selected' : '' }}>❌ {{ __('Rejected') }}</option>
            </select>
            <button type="submit" style="padding: 10px 25px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1em; font-weight: 500;">
                {{ __('Update Status') }}
            </button>
        </form>
    </div>

    <!-- Delete Card -->
    <div style="background: #fff5f5; border: 1px solid #ffcdd2; border-radius: 12px; padding: 25px;">
        <h3 style="color: #c62828; margin-bottom: 15px;">{{ __('Danger Zone') }}</h3>
        <p style="color: #666; margin-bottom: 20px;">{{ __('Once you delete a review, there is no going back. Please be certain.') }}</p>
        <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this review? This action cannot be undone.') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" style="padding: 10px 25px; background: #e74c3c; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1em; font-weight: 500;">
                {{ __('Delete Review') }}
            </button>
        </form>
    </div>
</div>
@endsection
