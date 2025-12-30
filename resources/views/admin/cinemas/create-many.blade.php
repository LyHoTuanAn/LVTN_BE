@extends('layouts.app')

@section('title', __('Add Multiple Cinemas'))
@section('page-title', __('Add Multiple Cinemas'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.cinemas.index') }}" style="color: #3498db; text-decoration: none;">
            ← {{ __('Back to Cinema List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 20px;">{{ __('Add Multiple Cinemas') }}</h2>

    @if ($errors->any())
        <div style="background: #fee2e2; border: 1px solid #f87171; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('validation_errors'))
        <div style="background: #fee2e2; border: 1px solid #f87171; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <strong>{{ __('Validation errors:') }}</strong>
            <ul style="margin: 10px 0 0; padding-left: 20px;">
                @foreach (session('validation_errors') as $index => $errors)
                    <li>
                        <strong>{{ __('Cinema') }} #{{ $index + 1 }}:</strong>
                        <ul style="margin: 5px 0; padding-left: 20px;">
                            @foreach ($errors as $field => $message)
                                <li>{{ ucfirst($field) }}: {{ $message }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.cinemas.store-many') }}" id="createManyCinemasForm">
        @csrf

        <div id="cinemas-container">
            <!-- Cinema entries will be added here -->
        </div>

        <div style="margin-bottom: 24px;">
            <button 
                type="button" 
                id="addCinemaBtn"
                style="padding: 12px 24px; background: #8e44ad; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer;"
            >
                + {{ __('Add Cinema Entry') }}
            </button>
        </div>

        <div style="display: flex; gap: 12px;">
            <button 
                type="submit" 
                style="padding: 12px 24px; background: #27ae60; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer;"
            >
                {{ __('Create All Cinemas') }}
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXVVX57lXbBHX8KWqqCPpo5HOQXhzx3kc&libraries=places&callback=initGoogleMaps" async defer></script>

<script>
    let cinemaIndex = 0;
    let googleMapsLoaded = false;
    const users = @json($users);
    const mapInstances = {};
    const markerInstances = {};
    const geocoderInstances = {};
    const autocompleteInstances = {};
    
    // Default location: Ho Chi Minh City
    const defaultLocation = { lat: 10.82302, lng: 106.62965 };

    function initGoogleMaps() {
        googleMapsLoaded = true;
        // Initialize components for any existing entries
        document.querySelectorAll('.cinema-entry').forEach(entry => {
            const entryId = entry.dataset.entryId;
            initMapForEntry(entryId);
            initAutocompleteForEntry(entryId);
        });
    }

    function initMapForEntry(entryId) {
        if (!googleMapsLoaded) return;
        
        const mapElement = document.getElementById(`map-${entryId}`);
        if (!mapElement) return;

        const geocoder = new google.maps.Geocoder();

        const map = new google.maps.Map(mapElement, {
            center: defaultLocation,
            zoom: 13,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
        });

        const marker = new google.maps.Marker({
            map: map,
            position: defaultLocation,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: '{{ __("Drag or click to select location") }}'
        });

        // Handle marker drag
        marker.addListener('dragend', function() {
            const position = marker.getPosition();
            handleEntryLocationSelect(entryId, position.lat(), position.lng(), geocoder);
        });

        // Handle map click
        map.addListener('click', function(event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            
            marker.setPosition(event.latLng);
            marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(() => marker.setAnimation(null), 500);
            
            handleEntryLocationSelect(entryId, lat, lng, geocoder);
        });

        mapInstances[entryId] = map;
        markerInstances[entryId] = marker;
        geocoderInstances[entryId] = geocoder;
    }

    function handleEntryLocationSelect(entryId, lat, lng, geocoder) {
        updateEntryCoordinates(entryId, lat, lng);
        
        const addressTextarea = document.querySelector(`textarea[name="cinemas[${entryId}][address]"]`);
        const locationInput = document.querySelector(`input[name="cinemas[${entryId}][location]"]`);
        
        geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
            if (status === 'OK' && results[0]) {
                if (addressTextarea) {
                    addressTextarea.value = results[0].formatted_address;
                }
                
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
                
                if ((city || province) && locationInput) {
                    locationInput.value = city || province;
                }
            }
        });
    }

    function initAutocompleteForEntry(entryId) {
        if (!googleMapsLoaded) return;
        
        const input = document.querySelector(`#address-autocomplete-${entryId}`);
        if (!input) return;

        const addressTextarea = document.querySelector(`textarea[name="cinemas[${entryId}][address]"]`);
        const locationInput = document.querySelector(`input[name="cinemas[${entryId}][location]"]`);

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['establishment', 'geocode'],
            componentRestrictions: { country: 'vn' }
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                console.log('No details available for: ' + place.name);
                return;
            }

            if (addressTextarea) {
                addressTextarea.value = place.formatted_address || place.name;
            }

            if (place.geometry.location) {
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                
                updateEntryCoordinates(entryId, lat, lng);
                updateEntryMap(entryId, lat, lng, place.name);
            }

            if (place.address_components && locationInput) {
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

            input.value = '';
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        autocompleteInstances[entryId] = autocomplete;
    }

    function updateEntryCoordinates(entryId, lat, lng) {
        const latInput = document.querySelector(`input[name="cinemas[${entryId}][latitude]"]`);
        const lngInput = document.querySelector(`input[name="cinemas[${entryId}][longitude]"]`);
        const coordsDisplay = document.getElementById(`coords-display-${entryId}`);
        const coordsText = document.getElementById(`coords-text-${entryId}`);

        if (latInput) latInput.value = lat.toFixed(8);
        if (lngInput) lngInput.value = lng.toFixed(8);
        if (coordsText) coordsText.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        if (coordsDisplay) coordsDisplay.style.display = 'block';
    }

    function updateEntryMap(entryId, lat, lng, title) {
        const map = mapInstances[entryId];
        const marker = markerInstances[entryId];

        if (!map || !marker) return;

        const position = { lat: lat, lng: lng };
        
        map.setCenter(position);
        map.setZoom(17);
        
        marker.setPosition(position);
        marker.setTitle(title || '');
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => marker.setAnimation(null), 500);
    }

    function createCinemaEntry(index) {
        const container = document.createElement('div');
        container.className = 'cinema-entry';
        container.id = `cinema-entry-${index}`;
        container.dataset.entryId = index;
        container.style.cssText = 'background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px;';
        
        let userOptions = '<option value="">{{ __("-- Select Manager --") }}</option>';
        users.forEach(user => {
            userOptions += `<option value="${user.id}">${user.name} (${user.email})</option>`;
        });

        container.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="color: #2c3e50; font-size: 1.2em; margin: 0;">{{ __('Cinema') }} #${index + 1}</h3>
                <button 
                    type="button" 
                    onclick="removeCinemaEntry(${index})"
                    style="padding: 6px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 0.9em; cursor: pointer;"
                >
                    ✕ {{ __('Remove') }}
                </button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                        {{ __('Cinema Name') }} <span style="color: #e74c3c;">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="cinemas[${index}][name]" 
                        required
                        style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                    >
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                        {{ __('Location') }} <span style="color: #e74c3c;">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="cinemas[${index}][location]" 
                        placeholder="{{ __('e.g., Ho Chi Minh City, Hanoi...') }}"
                        required
                        style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                    >
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                        {{ __('Phone') }}
                    </label>
                    <input 
                        type="text" 
                        name="cinemas[${index}][phone]" 
                        placeholder="{{ __('e.g., 0123456789') }}"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                    >
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                        {{ __('Manager') }}
                    </label>
                    <select 
                        name="cinemas[${index}][user_id]" 
                        style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                    >
                        ${userOptions}
                    </select>
                </div>
            </div>

            <div style="margin-top: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                    {{ __('Address') }} <span style="color: #e74c3c;">*</span>
                    <span style="font-weight: normal; color: #6b7280; font-size: 0.85em; margin-left: 8px;">
                        ({{ __('Search or click on map') }})
                    </span>
                </label>
                <div style="position: relative; margin-bottom: 8px;">
                    <input 
                        type="text" 
                        id="address-autocomplete-${index}"
                        placeholder="{{ __('Search for address...') }}"
                        autocomplete="off"
                        style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                    >
                    <svg style="position: absolute; left: 12px; top: 12px; width: 20px; height: 20px; color: #9ca3af; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <textarea 
                    name="cinemas[${index}][address]" 
                    rows="2"
                    required
                    readonly
                    placeholder="{{ __('Full address will appear here...') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box; resize: vertical; background-color: #f9fafb; cursor: not-allowed;"
                ></textarea>
            </div>

            <!-- Map Preview -->
            <div style="margin-top: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                    {{ __('Map Preview') }}
                    <span style="font-weight: normal; color: #6b7280; font-size: 0.85em; margin-left: 8px;">
                        ({{ __('Click or drag marker') }})
                    </span>
                </label>
                <div style="position: relative; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; background: #f3f4f6;">
                    <div id="map-${index}" class="entry-map" style="width: 100%; height: 250px;"></div>
                </div>
                <div id="coords-display-${index}" style="margin-top: 8px; padding: 8px 12px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; display: none;">
                    <span style="color: #166534; font-size: 0.85em;">
                        <strong>{{ __('Coordinates') }}:</strong> 
                        <span id="coords-text-${index}"></span>
                    </span>
                </div>
            </div>

            <!-- Hidden fields for coordinates -->
            <input type="hidden" name="cinemas[${index}][latitude]">
            <input type="hidden" name="cinemas[${index}][longitude]">
        `;

        return container;
    }

    function removeCinemaEntry(index) {
        const entry = document.getElementById(`cinema-entry-${index}`);
        if (entry) {
            // Clean up instances
            if (autocompleteInstances[index]) {
                google.maps.event.clearInstanceListeners(autocompleteInstances[index]);
                delete autocompleteInstances[index];
            }
            if (mapInstances[index]) {
                delete mapInstances[index];
            }
            if (markerInstances[index]) {
                delete markerInstances[index];
            }
            if (geocoderInstances[index]) {
                delete geocoderInstances[index];
            }
            entry.remove();
            updateCinemaNumbers();
        }
    }

    function updateCinemaNumbers() {
        const entries = document.querySelectorAll('.cinema-entry');
        entries.forEach((entry, i) => {
            const title = entry.querySelector('h3');
            if (title) {
                title.textContent = `{{ __('Cinema') }} #${i + 1}`;
            }
        });
    }

    document.getElementById('addCinemaBtn').addEventListener('click', function() {
        const container = document.getElementById('cinemas-container');
        const entry = createCinemaEntry(cinemaIndex);
        container.appendChild(entry);
        
        // Initialize map and autocomplete
        if (googleMapsLoaded) {
            initMapForEntry(cinemaIndex);
            initAutocompleteForEntry(cinemaIndex);
        }
        
        cinemaIndex++;
    });

    // Add first entry on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addCinemaBtn').click();
    });
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
.entry-map {
    cursor: crosshair;
}
</style>
@endsection
