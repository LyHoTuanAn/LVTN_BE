@extends('layouts.admin')

@section('title', $movie->title)
@section('page-title', __('Movie Details'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <!-- Header Actions -->
    <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('admin.movies.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em; display: inline-flex; align-items: center; gap: 5px;">
            <span>←</span> <span>{{ __('Back to List') }}</span>
        </a>
        <div style="display: flex; gap: 10px;">
            <a 
                href="{{ route('admin.movies.edit', $movie->id) }}" 
                style="padding: 10px 24px; background: #f39c12; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; font-weight: 500; transition: background 0.2s;"
                onmouseover="this.style.background='#e67e22'"
                onmouseout="this.style.background='#f39c12'"
            >
                {{ __('Edit') }}
            </a>
            <form method="POST" action="{{ route('admin.movies.destroy', $movie->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this movie?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 10px 24px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 0.9em; font-weight: 500; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'">
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Movie Header Section -->
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 40px; margin-bottom: 40px;">
        <!-- Poster -->
        <div>
            @if ($movie->poster)
                <img 
                    src="{{ asset('storage/' . $movie->poster->file_path) }}" 
                    alt="{{ $movie->title }}" 
                    id="poster-image"
                    style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); cursor: pointer; transition: all 0.3s ease;"
                    onclick="openPosterModal('{{ asset('storage/' . $movie->poster->file_path) }}', '{{ $movie->title }}')"
                    onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.2)'"
                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.15)'"
                >
            @else
                <div style="width: 100%; aspect-ratio: 2/3; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9em; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    {{ __('No Poster') }}
                </div>
            @endif
        </div>

        <!-- Movie Basic Info -->
        <div>
            <h2 style="color: #2c3e50; font-size: 2em; margin-bottom: 15px; font-weight: 700; line-height: 1.2;">{{ $movie->title }}</h2>
            
            @php
                $statusColors = [
                    'coming_soon' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                    'now_showing' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                    'trending' => ['bg' => '#fff8e1', 'color' => '#f57c00'],
                ];
                $statusLabels = [
                    'coming_soon' => __('Coming Soon'),
                    'now_showing' => __('Now Showing'),
                    'trending' => __('Trending'),
                ];
                $ageLabels = \App\Models\Movie::getAgeClassifications();
            @endphp

            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
                <span style="padding: 6px 14px; background: {{ $statusColors[$movie->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$movie->status]['color'] ?? '#666' }}; border-radius: 6px; font-size: 0.85em; font-weight: 600;">
                    {{ $statusLabels[$movie->status] ?? $movie->status }}
                </span>
                <span style="padding: 6px 14px; background: #e3f2fd; color: #1976d2; border-radius: 6px; font-size: 0.85em; font-weight: 600;">
                    {{ $movie->age_classification ?? 'P' }} - {{ $ageLabels[$movie->age_classification ?? 'P'] }}
                </span>
            </div>

            <!-- Info Cards Grid -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px;">
                <div style="padding: 18px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #3498db;">
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 8px; font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Duration') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1.2em; font-weight: 600;">{{ $movie->duration }} {{ __('mins') }}</p>
                </div>

                <div style="padding: 18px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #27ae60;">
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 8px; font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Release Date') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1.2em; font-weight: 600;">{{ $movie->release_date->format('d/m/Y') }}</p>
                </div>

                <div style="padding: 18px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #9b59b6;">
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 8px; font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Created At') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1.2em; font-weight: 600;">{{ $movie->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            @if ($movie->genre || $movie->language)
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 25px;">
                @if ($movie->genre)
                <div style="padding: 18px; background: #f8f9fa; border-radius: 8px;">
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 8px; font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Genre') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1.1em; font-weight: 500;">{{ $movie->genre }}</p>
                </div>
                @endif

                @if ($movie->language)
                <div style="padding: 18px; background: #f8f9fa; border-radius: 8px;">
                    <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 8px; font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Language') }}</label>
                    <p style="margin: 0; color: #2c3e50; font-size: 1.1em; font-weight: 500;">{{ $movie->language }}</p>
                </div>
                @endif
            </div>
            @endif

            @if ($movie->description)
            <div style="margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #3498db;">
                <label style="display: block; font-weight: 600; color: #7f8c8d; margin-bottom: 10px; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Description') }}</label>
                <p style="margin: 0; color: #2c3e50; line-height: 1.7; font-size: 0.95em;">{{ $movie->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Directors & Actors Section -->
    @if (($movie->directors && $movie->directors->count() > 0) || ($movie->actors && $movie->actors->count() > 0))
    <div style="margin-bottom: 40px; padding-top: 30px; border-top: 2px solid #eee;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
            @if ($movie->directors && $movie->directors->count() > 0)
            <div>
                <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 4px; height: 20px; background: #3498db; border-radius: 2px;"></span>
                    {{ __('Directors') }} <span style="color: #7f8c8d; font-size: 0.85em; font-weight: 400;">({{ $movie->directors->count() }})</span>
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    @foreach ($movie->directors as $director)
                        <div style="display: flex; align-items: center; gap: 12px; background: #f8f9fa; padding: 12px 16px; border-radius: 8px; transition: all 0.2s; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateY(0)'">
                            @if ($director->avatar)
                                <img src="{{ asset('storage/' . $director->avatar->file_path) }}" alt="{{ $director->name }}" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                            @else
                                <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2em; font-weight: 600; border: 2px solid #ddd;">
                                    {{ substr($director->name, 0, 1) }}
                                </div>
                            @endif
                            <span style="color: #2c3e50; font-weight: 500; font-size: 0.95em;">{{ $director->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if ($movie->actors && $movie->actors->count() > 0)
            <div>
                <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 4px; height: 20px; background: #e74c3c; border-radius: 2px;"></span>
                    {{ __('Actors') }} <span style="color: #7f8c8d; font-size: 0.85em; font-weight: 400;">({{ $movie->actors->count() }})</span>
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    @foreach ($movie->actors as $actor)
                        <div style="display: flex; align-items: center; gap: 12px; background: #f8f9fa; padding: 12px 16px; border-radius: 8px; transition: all 0.2s; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateY(0)'">
                            @if ($actor->avatar)
                                <img src="{{ asset('storage/' . $actor->avatar->file_path) }}" alt="{{ $actor->name }}" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                            @else
                                <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2em; font-weight: 600; border: 2px solid #ddd;">
                                    {{ substr($actor->name, 0, 1) }}
                                </div>
                            @endif
                            <span style="color: #2c3e50; font-weight: 500; font-size: 0.95em;">{{ $actor->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Trailer Section -->
    @if ($movie->trailer)
    <div style="margin-bottom: 40px; padding-top: 30px; border-top: 2px solid #eee;">
        <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span style="width: 4px; height: 20px; background: #f39c12; border-radius: 2px;"></span>
            {{ __('Trailer') }}
        </h3>
        <div style="max-width: 800px;">
            <video controls style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <source src="{{ asset('storage/' . $movie->trailer->file_path) }}" type="{{ $movie->trailer->mime_type }}">
                {{ __('Your browser does not support the video tag.') }}
            </video>
        </div>
    </div>
    @endif

    <!-- Showtimes Section -->
    <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #eee;">
        <h3 style="color: #2c3e50; font-size: 1.3em; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span style="width: 4px; height: 24px; background: #27ae60; border-radius: 2px;"></span>
            {{ __('Showtimes') }} 
            <span style="padding: 4px 12px; background: #e8f5e9; color: #2e7d32; border-radius: 6px; font-size: 0.7em; font-weight: 600; margin-left: 10px;">
                {{ $movie->showtimes->count() }}
            </span>
        </h3>

        @if($movie->showtimes->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 14px 12px; text-align: left; color: #2c3e50; font-weight: 600; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Date') }}</th>
                            <th style="padding: 14px 12px; text-align: left; color: #2c3e50; font-weight: 600; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Time') }}</th>
                            <th style="padding: 14px 12px; text-align: left; color: #2c3e50; font-weight: 600; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Room') }}</th>
                            <th style="padding: 14px 12px; text-align: left; color: #2c3e50; font-weight: 600; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Price') }}</th>
                            <th style="padding: 14px 12px; text-align: left; color: #2c3e50; font-weight: 600; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movie->showtimes as $showtime)
                            <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 14px 12px; color: #2c3e50; font-weight: 500;">{{ $showtime->date->format('d/m/Y') }}</td>
                                <td style="padding: 14px 12px; color: #2c3e50;">
                                    <span style="font-weight: 600;">{{ $showtime->start_time }}</span>
                                    <span style="color: #7f8c8d; margin: 0 5px;">-</span>
                                    <span style="font-weight: 600;">{{ $showtime->end_time }}</span>
                                </td>
                                <td style="padding: 14px 12px; color: #2c3e50; font-weight: 500;">{{ $showtime->room?->name ?? '-' }}</td>
                                <td style="padding: 14px 12px; color: #2c3e50; font-weight: 600;">{{ number_format($showtime->price, 0, ',', '.') }} VNĐ</td>
                                <td style="padding: 14px 12px;">
                                    <span style="padding: 6px 12px; background: #e3f2fd; color: #1976d2; border-radius: 6px; font-size: 0.85em; font-weight: 600;">
                                        {{ ucfirst($showtime->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px; border: 2px dashed #dee2e6;">
                <div style="font-size: 2em; margin-bottom: 10px; color: #bdc3c7;">📅</div>
                <p style="margin: 0; font-size: 1em; font-weight: 500;">{{ __('No showtimes found for this movie') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Poster Modal -->
<div id="poster-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.95); cursor: pointer;" onclick="closePosterModal()">
    <div style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <img id="modal-poster-image" src="" alt="" style="max-width: 90%; max-height: 90%; object-fit: contain; border-radius: 12px; box-shadow: 0 8px 30px rgba(255,255,255,0.1);">
        <span id="close-modal" style="position: absolute; top: 30px; right: 50px; color: #fff; font-size: 45px; font-weight: 300; cursor: pointer; z-index: 1001; line-height: 1; transition: all 0.2s; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(255,255,255,0.1);" onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='rotate(90deg)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='rotate(0deg)'">&times;</span>
    </div>
</div>

@push('styles')
<style>
    #poster-modal {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    #modal-poster-image {
        animation: zoomIn 0.3s ease-in-out;
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 280px 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function openPosterModal(imageSrc, imageAlt) {
        const modal = document.getElementById('poster-modal');
        const modalImage = document.getElementById('modal-poster-image');
        
        if (modal && modalImage) {
            modalImage.src = imageSrc;
            modalImage.alt = imageAlt;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    function closePosterModal() {
        const modal = document.getElementById('poster-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('poster-modal');
                if (modal && modal.style.display === 'block') {
                    closePosterModal();
                }
            }
        });

        const modalImage = document.getElementById('modal-poster-image');
        if (modalImage) {
            modalImage.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        }

        const closeButton = document.getElementById('close-modal');
        if (closeButton) {
            closeButton.addEventListener('click', function(event) {
                event.stopPropagation();
                closePosterModal();
            });
        }
    });
</script>
@endpush
@endsection
