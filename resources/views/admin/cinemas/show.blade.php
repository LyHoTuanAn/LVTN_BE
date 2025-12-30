@extends('layouts.app')

@section('title', __('Cinema Details'))
@section('page-title', __('Cinema Details'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.cinemas.index') }}" style="color: #3498db; text-decoration: none;">
            ← {{ __('Back to Cinema List') }}
        </a>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <h2 style="color: #2c3e50; font-size: 1.5em; margin: 0;">{{ $cinema->name }}</h2>
        <div style="display: flex; gap: 10px;">
            <a 
                href="{{ route('admin.cinemas.edit', $cinema->id) }}" 
                style="padding: 8px 16px; background: #f39c12; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em;"
            >
                {{ __('Edit') }}
            </a>
            <form method="POST" action="{{ route('admin.cinemas.destroy', $cinema->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this cinema?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 8px 16px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 0.9em; cursor: pointer;">
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        <!-- Basic Information -->
        <div style="background: #f8f9fa; border-radius: 8px; padding: 20px;">
            <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 16px; border-bottom: 2px solid #3498db; padding-bottom: 8px;">
                {{ __('Basic Information') }}
            </h3>
            
            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('ID') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->id }}</span>
            </div>

            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Name') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->name }}</span>
            </div>

            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Location') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->location }}</span>
            </div>

            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Address') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->address }}</span>
            </div>

            @if($cinema->latitude && $cinema->longitude)
            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Coordinates') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->latitude }}, {{ $cinema->longitude }}</span>
            </div>
            @endif

            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Phone') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->phone ?? '-' }}</span>
            </div>

            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Manager') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->user?->name ?? '-' }}</span>
            </div>

            <div style="margin-bottom: 12px;">
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Created At') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->created_at?->format('d/m/Y H:i') }}</span>
            </div>

            <div>
                <strong style="color: #7f8c8d; display: block; margin-bottom: 4px;">{{ __('Updated At') }}:</strong>
                <span style="color: #2c3e50;">{{ $cinema->updated_at?->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Rooms -->
        <div style="background: #f8f9fa; border-radius: 8px; padding: 20px;">
            <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 16px; border-bottom: 2px solid #27ae60; padding-bottom: 8px;">
                {{ __('Rooms') }} ({{ $cinema->rooms->count() }})
            </h3>
            
            @if($cinema->rooms->count() > 0)
                <div style="max-height: 300px; overflow-y: auto;">
                    @foreach($cinema->rooms as $room)
                        <div style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 10px; border: 1px solid #e0e0e0;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #2c3e50;">{{ $room->name }}</strong>
                                    <span style="color: #7f8c8d; font-size: 0.85em; margin-left: 8px;">
                                        ({{ $room->seats->count() }} {{ __('seats') }})
                                    </span>
                                </div>
                                <a 
                                    href="{{ route('admin.rooms.show', $room->id) }}" 
                                    style="padding: 4px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.8em;"
                                >
                                    {{ __('View') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: #666; text-align: center; padding: 20px 0;">
                    {{ __('No rooms in this cinema') }}
                </p>
            @endif

            <div style="margin-top: 16px;">
                <a 
                    href="{{ route('admin.rooms.create', ['cinema_id' => $cinema->id]) }}" 
                    style="display: inline-block; padding: 8px 16px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em;"
                >
                    + {{ __('Add Room') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Map View -->
    @if($cinema->latitude && $cinema->longitude)
    <div style="margin-top: 24px;">
        <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 16px; border-bottom: 2px solid #9b59b6; padding-bottom: 8px;">
            {{ __('Location Map') }}
        </h3>
        <div id="map" style="width: 100%; height: 400px; border-radius: 8px; overflow: hidden; border: 1px solid #d1d5db;"></div>
    </div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXVVX57lXbBHX8KWqqCPpo5HOQXhzx3kc&callback=initMap" async defer></script>
    <script>
    function initMap() {
        const location = { lat: {{ $cinema->latitude }}, lng: {{ $cinema->longitude }} };
        
        const map = new google.maps.Map(document.getElementById('map'), {
            center: location,
            zoom: 16,
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true,
        });

        const marker = new google.maps.Marker({
            position: location,
            map: map,
            title: '{{ $cinema->name }}',
            animation: google.maps.Animation.DROP,
        });

        // Info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 8px;">
                    <h4 style="margin: 0 0 8px 0; color: #2c3e50;">{{ $cinema->name }}</h4>
                    <p style="margin: 0; color: #666; font-size: 0.9em;">{{ $cinema->address }}</p>
                </div>
            `
        });

        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });

        // Open info window by default
        infoWindow.open(map, marker);
    }
    </script>
    @else
    <div style="margin-top: 24px;">
        <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 16px; border-bottom: 2px solid #9b59b6; padding-bottom: 8px;">
            {{ __('Location Map') }}
        </h3>
        <div style="background: #f8f9fa; border-radius: 8px; padding: 40px; text-align: center; border: 1px solid #d1d5db;">
            <svg style="width: 48px; height: 48px; color: #9ca3af; margin-bottom: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <p style="color: #6b7280; margin: 0;">{{ __('No coordinates available. Edit the cinema to add location.') }}</p>
            <a 
                href="{{ route('admin.cinemas.edit', $cinema->id) }}" 
                style="display: inline-block; margin-top: 16px; padding: 8px 16px; background: #3498db; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em;"
            >
                {{ __('Edit Cinema') }}
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
