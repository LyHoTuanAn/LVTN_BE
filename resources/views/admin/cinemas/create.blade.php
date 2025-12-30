@extends('layouts.app')

@section('title', __('Add Cinema'))
@section('page-title', __('Add Cinema'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.cinemas.index') }}" style="color: #3498db; text-decoration: none;">
            ← {{ __('Back to Cinema List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 20px;">{{ __('Add New Cinema') }}</h2>

    @if ($errors->any())
        <div style="background: #fee2e2; border: 1px solid #f87171; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.cinemas.store') }}" style="max-width: 800px;">
        @csrf

        <div style="margin-bottom: 16px;">
            <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Cinema Name') }} <span style="color: #e74c3c;">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
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
                value="{{ old('location') }}" 
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
                    placeholder="{{ __('Search for address...') }}"
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
                placeholder="{{ __('Full address will appear here...') }}"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box; resize: vertical; background-color: #f9fafb; cursor: not-allowed;"
            >{{ old('address') }}</textarea>
        </div>

        <!-- Map Preview -->
        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Map Preview') }}
                <span style="font-weight: normal; color: #6b7280; font-size: 0.85em; margin-left: 8px;">
                    ({{ __('Click on map or drag marker to select location') }})
                </span>
            </label>
            <div id="map-container" style="position: relative; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; background: #f3f4f6;">
                <div id="map" style="width: 100%; height: 350px;"></div>
                <!-- Loading indicator -->
                <div id="map-loading" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f3f4f6;">
                    <div style="width: 40px; height: 40px; border: 4px solid #e5e7eb; border-top-color: #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="color: #6b7280; margin-top: 12px;">{{ __('Loading map...') }}</p>
                </div>
                <!-- Error message -->
                <div id="map-error" style="position: absolute; inset: 0; display: none; flex-direction: column; align-items: center; justify-content: center; background: #fef2f2; padding: 20px;">
                    <svg style="width: 48px; height: 48px; color: #ef4444; margin-bottom: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p style="color: #dc2626; margin: 0; text-align: center;">{{ __('Failed to load Google Maps. Please check your internet connection.') }}</p>
                    <button type="button" onclick="location.reload()" style="margin-top: 12px; padding: 8px 16px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                        {{ __('Retry') }}
                    </button>
                </div>
            </div>
            <!-- Coordinates display -->
            <div id="coordinates-display" style="margin-top: 8px; padding: 8px 12px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; display: none;">
                <span style="color: #166534; font-size: 0.85em;">
                    <strong>{{ __('Coordinates') }}:</strong> 
                    <span id="coords-text"></span>
                </span>
            </div>
        </div>

        <!-- Hidden fields for coordinates -->
        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

        <div style="margin-bottom: 16px;">
            <label for="phone" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Phone') }}
            </label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone') }}" 
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
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 12px;">
            <button 
                type="submit" 
                style="padding: 12px 24px; background: #27ae60; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer;"
            >
                {{ __('Create Cinema') }}
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

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}

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

<script>
let map = null;
let marker = null;
let geocoder = null;
let mapLoadTimeout = null;

// Set timeout for map loading
mapLoadTimeout = setTimeout(function() {
    document.getElementById('map-loading').style.display = 'none';
    document.getElementById('map-error').style.display = 'flex';
}, 15000); // 15 seconds timeout

function initAutocomplete() {
    // Clear timeout since map is loading
    if (mapLoadTimeout) {
        clearTimeout(mapLoadTimeout);
    }
    
    const mapLoading = document.getElementById('map-loading');
    const mapError = document.getElementById('map-error');
    const addressInput = document.getElementById('address-autocomplete');
    const addressTextarea = document.getElementById('address');
    const locationInput = document.getElementById('location');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const mapElement = document.getElementById('map');
    const coordsDisplay = document.getElementById('coordinates-display');
    const coordsText = document.getElementById('coords-text');

    try {
        // Default location: Ho Chi Minh City
        const defaultLocation = { lat: 10.82302, lng: 106.62965 };
        
        // Initialize geocoder
        geocoder = new google.maps.Geocoder();

        // Initialize map with default location
        map = new google.maps.Map(mapElement, {
            center: defaultLocation,
            zoom: 13,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true,
        });

        // Create draggable marker at default location
        marker = new google.maps.Marker({
            map: map,
            position: defaultLocation,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: '{{ __("Drag to select location") }}'
        });

        // Hide loading indicator when map is ready
        google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
            mapLoading.style.display = 'none';
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

    } catch (error) {
        console.error('Error initializing map:', error);
        mapLoading.style.display = 'none';
        mapError.style.display = 'flex';
    }

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

// Handle Google Maps API loading error
function gm_authFailure() {
    console.error('Google Maps authentication failed');
    document.getElementById('map-loading').style.display = 'none';
    document.getElementById('map-error').style.display = 'flex';
}
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXVVX57lXbBHX8KWqqCPpo5HOQXhzx3kc&libraries=places&callback=initAutocomplete" async defer></script>
@endsection
