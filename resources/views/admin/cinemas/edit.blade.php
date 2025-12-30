@extends('layouts.app')

@section('title', __('Edit Cinema'))
@section('page-title', __('Edit Cinema'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.cinemas.index') }}" style="color: #3498db; text-decoration: none;">
            ← {{ __('Back to Cinema List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 20px;">{{ __('Edit Cinema') }}: {{ $cinema->name }}</h2>

    @if ($errors->any())
        <div style="background: #fee2e2; border: 1px solid #f87171; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.cinemas.update', $cinema->id) }}" style="max-width: 800px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 16px;">
            <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Cinema Name') }} <span style="color: #e74c3c;">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name', $cinema->name) }}" 
                required
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
        </div>

        <div style="margin-bottom: 16px;">
            <label for="location" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Location') }} <span style="color: #e74c3c;">*</span>
            </label>
            <input 
                type="text" 
                id="location" 
                name="location" 
                value="{{ old('location', $cinema->location) }}" 
                placeholder="{{ __('e.g., Ho Chi Minh City, Hanoi...') }}"
                required
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
        </div>

        <div style="margin-bottom: 16px;">
            <label for="address" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Address') }} <span style="color: #e74c3c;">*</span>
                <span style="font-weight: normal; color: #6b7280; font-size: 0.85em; margin-left: 8px;">
                    ({{ __('Search or click on map') }})
                </span>
            </label>
            <div style="position: relative;">
                <input 
                    type="text" 
                    id="address-autocomplete" 
                    placeholder="{{ __('Search for new address...') }}"
                    autocomplete="off"
                    style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box; margin-bottom: 8px;"
                >
                <svg style="position: absolute; left: 12px; top: 12px; width: 20px; height: 20px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <textarea 
                id="address" 
                name="address" 
                rows="2"
                required
                readonly
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box; resize: vertical; background-color: #f9fafb; cursor: not-allowed;"
            >{{ old('address', $cinema->address) }}</textarea>
        </div>

        <!-- Map Preview -->
        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Map Preview') }}
                <span style="font-weight: normal; color: #6b7280; font-size: 0.85em; margin-left: 8px;">
                    ({{ __('Click on map or drag marker to change location') }})
                </span>
            </label>
            <div id="map-container" style="position: relative; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; background: #f3f4f6;">
                <div id="map" style="width: 100%; height: 350px;"></div>
            </div>
            <!-- Coordinates display -->
            <div id="coordinates-display" style="margin-top: 8px; padding: 8px 12px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; {{ ($cinema->latitude && $cinema->longitude) ? '' : 'display: none;' }}">
                <span style="color: #166534; font-size: 0.85em;">
                    <strong>{{ __('Coordinates') }}:</strong> 
                    <span id="coords-text">{{ $cinema->latitude ? number_format($cinema->latitude, 6) . ', ' . number_format($cinema->longitude, 6) : '' }}</span>
                </span>
            </div>
        </div>

        <!-- Hidden fields for coordinates -->
        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $cinema->latitude) }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $cinema->longitude) }}">

        <div style="margin-bottom: 16px;">
            <label for="phone" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Phone') }}
            </label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone', $cinema->phone) }}" 
                placeholder="{{ __('e.g., 0123456789') }}"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
        </div>

        <div style="margin-bottom: 24px;">
            <label for="user_id" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Manager') }}
            </label>
            <select 
                id="user_id" 
                name="user_id" 
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
                <option value="">{{ __('-- Select Manager --') }}</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $cinema->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 12px;">
            <button 
                type="submit" 
                style="padding: 12px 24px; background: #3498db; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer;"
            >
                {{ __('Update Cinema') }}
            </button>
            <a 
                href="{{ route('admin.cinemas.index') }}" 
                style="padding: 12px 24px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXVVX57lXbBHX8KWqqCPpo5HOQXhzx3kc&libraries=places&callback=initAutocomplete" async defer></script>

<script>
let map = null;
let marker = null;
let geocoder = null;

// Existing coordinates from database
const existingLat = {{ $cinema->latitude ?? 'null' }};
const existingLng = {{ $cinema->longitude ?? 'null' }};

function initAutocomplete() {
    const addressInput = document.getElementById('address-autocomplete');
    const addressTextarea = document.getElementById('address');
    const locationInput = document.getElementById('location');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const mapElement = document.getElementById('map');
    const coordsDisplay = document.getElementById('coordinates-display');
    const coordsText = document.getElementById('coords-text');

    // Default location: Ho Chi Minh City (or existing coordinates)
    const defaultLocation = { lat: 10.82302, lng: 106.62965 };
    let initialLocation = defaultLocation;
    let initialZoom = 13;

    // Use existing coordinates if available
    if (existingLat && existingLng) {
        initialLocation = { lat: existingLat, lng: existingLng };
        initialZoom = 17;
    }

    // Initialize geocoder
    geocoder = new google.maps.Geocoder();

    // Initialize map
    map = new google.maps.Map(mapElement, {
        center: initialLocation,
        zoom: initialZoom,
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true,
    });

    // Create draggable marker
    marker = new google.maps.Marker({
        map: map,
        position: initialLocation,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: '{{ __("Drag to change location") }}'
    });

    // Handle marker drag
    marker.addListener('dragend', function() {
        const position = marker.getPosition();
        handleLocationSelect(position.lat(), position.lng());
    });

    // Handle map click - move marker to clicked location
    map.addListener('click', function(event) {
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();
        
        // Move marker to clicked position
        marker.setPosition(event.latLng);
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => marker.setAnimation(null), 500);
        
        // Handle the location selection
        handleLocationSelect(lat, lng);
    });

    // Create autocomplete object
    const autocomplete = new google.maps.places.Autocomplete(addressInput, {
        types: ['establishment', 'geocode'],
        componentRestrictions: { country: 'vn' }
    });

    // Handle place selection from autocomplete
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            console.log('No details available for: ' + place.name);
            return;
        }

        // Set full address
        addressTextarea.value = place.formatted_address || place.name;

        // Set coordinates and update map
        if (place.geometry.location) {
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            
            updateCoordinates(lat, lng);
            updateMap(lat, lng, place.name);
        }

        // Extract city/location from address components
        if (place.address_components) {
            let city = '';
            let province = '';
            
            for (const component of place.address_components) {
                if (component.types.includes('administrative_area_level_1')) {
                    province = component.long_name;
                }
                if (component.types.includes('locality')) {
                    city = component.long_name;
                }
            }
            
            if (city || province) {
                locationInput.value = city || province;
            }
        }

        // Clear autocomplete input after selection
        addressInput.value = '';
    });

    // Prevent form submission on Enter in autocomplete field
    addressInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });

    // Handle location selection (from click or drag)
    function handleLocationSelect(lat, lng) {
        updateCoordinates(lat, lng);
        
        // Reverse geocode to get address
        geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
            if (status === 'OK' && results[0]) {
                addressTextarea.value = results[0].formatted_address;
                
                // Extract location/city from address components
                const components = results[0].address_components;
                let city = '';
                let province = '';
                
                for (const component of components) {
                    if (component.types.includes('administrative_area_level_1')) {
                        province = component.long_name;
                    }
                    if (component.types.includes('locality')) {
                        city = component.long_name;
                    }
                }
                
                if (city || province) {
                    locationInput.value = city || province;
                }
            }
        });
    }

    function updateCoordinates(lat, lng) {
        latitudeInput.value = lat.toFixed(8);
        longitudeInput.value = lng.toFixed(8);
        coordsText.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        coordsDisplay.style.display = 'block';
    }

    function updateMap(lat, lng, title) {
        const position = { lat: lat, lng: lng };
        
        // Update map
        map.setCenter(position);
        map.setZoom(17);
        
        // Update marker
        marker.setPosition(position);
        marker.setTitle(title || '');
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => marker.setAnimation(null), 500);
    }
}
</script>

<style>
/* Style Google Autocomplete dropdown */
.pac-container {
    z-index: 10000;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border: none;
    margin-top: 4px;
}

.pac-item {
    padding: 10px 12px;
    cursor: pointer;
    font-size: 0.9em;
}

.pac-item:hover {
    background-color: #f3f4f6;
}

.pac-item-selected {
    background-color: #e5e7eb;
}

.pac-icon {
    margin-right: 8px;
}

.pac-item-query {
    font-weight: 500;
    color: #1f2937;
}

/* Custom map cursor */
#map {
    cursor: crosshair;
}
</style>
@endsection
