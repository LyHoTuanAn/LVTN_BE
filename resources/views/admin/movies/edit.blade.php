@extends('layouts.app')

@section('title', __('Edit Movie'))
@section('page-title', __('Edit Movie'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.movies.show', $movie->id) }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to Details') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 30px;">{{ __('Edit Movie') }}: {{ $movie->title }}</h2>

    <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="movie-form-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Title') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $movie->title) }}"
                    required
                    maxlength="255"
                    placeholder="{{ __('Enter movie title') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('title')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="duration" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Duration (minutes)') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="number" 
                    name="duration" 
                    id="duration" 
                    value="{{ old('duration', $movie->duration) }}"
                    required
                    min="1"
                    max="600"
                    placeholder="{{ __('Enter duration in minutes') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('duration')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="release_date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Release Date') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="release_date" 
                    id="release_date" 
                    class="datepicker"
                    value="{{ old('release_date', $movie->release_date->format('Y-m-d')) }}"
                    required
                    placeholder="{{ __('Select date') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box; background: white;"
                >
                @error('release_date')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Status') }}
                </label>
                @php
                    $computedStatus = $movie->getComputedStatus();
                    $statusColors = [
                        'COMING_SOON' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                        'NOW_SHOWING' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                    ];
                    $statusLabels = [
                        'COMING_SOON' => __('Coming Soon'),
                        'NOW_SHOWING' => __('Now Showing'),
                    ];
                @endphp
                <div style="padding: 12px 15px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <span style="font-size: 0.9em; color: #666;">{{ __('Current Status:') }}</span>
                        <span style="padding: 6px 14px; background: {{ $statusColors[$computedStatus]['bg'] ?? '#eee' }}; color: {{ $statusColors[$computedStatus]['color'] ?? '#666' }}; border-radius: 6px; font-size: 0.9em; font-weight: 600;">
                            {{ $statusLabels[$computedStatus] ?? $computedStatus }}
                        </span>
                    </div>
                    <p style="margin: 0; color: #6c757d; font-size: 0.85em; line-height: 1.5;">
                        <strong>{{ __('Note:') }}</strong> {{ __('Status is automatically calculated based on showtimes.') }}
                        <br>{{ __('To change the status, add or modify showtimes for this movie.') }}
                    </p>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="genre" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Genre') }}
                </label>
                <input 
                    type="text" 
                    name="genre" 
                    id="genre" 
                    value="{{ old('genre', $movie->genre) }}"
                    placeholder="{{ __('e.g., Action, Drama, Comedy') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('genre')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="age_classification" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Age Classification') }} <span style="color: #e74c3c;">*</span>
                </label>
                <select 
                    name="age_classification" 
                    id="age_classification" 
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; background: white; box-sizing: border-box;"
                >
                    <option value="P" {{ old('age_classification', $movie->age_classification) == 'P' ? 'selected' : '' }}>P - {{ __('All Ages') }}</option>
                    <option value="K" {{ old('age_classification', $movie->age_classification) == 'K' ? 'selected' : '' }}>K - {{ __('Children (Parental Guidance)') }}</option>
                    <option value="T13" {{ old('age_classification', $movie->age_classification) == 'T13' ? 'selected' : '' }}>T13 - 13+</option>
                    <option value="T16" {{ old('age_classification', $movie->age_classification) == 'T16' ? 'selected' : '' }}>T16 - 16+</option>
                    <option value="T18" {{ old('age_classification', $movie->age_classification) == 'T18' ? 'selected' : '' }}>T18 - 18+</option>
                    <option value="C" {{ old('age_classification', $movie->age_classification) == 'C' ? 'selected' : '' }}>C - {{ __('Prohibited') }}</option>
                </select>
                @error('age_classification')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Directors Section -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Directors') }}
                </label>
                <div style="border: 1px solid #ddd; border-radius: 6px; overflow: hidden;">
                    <div style="background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #ddd; display: grid; grid-template-columns: 1fr 150px 60px 50px; gap: 10px; font-weight: 600; color: #2c3e50;">
                        <span>{{ __('Name') }}</span>
                        <span>{{ __('Avatar') }}</span>
                        <span>{{ __('Current') }}</span>
                        <span></span>
                    </div>
                    <div id="directors-container">
                        @foreach ($movie->directors as $index => $director)
                        <div class="director-row" data-director-id="{{ $director->id }}" style="display: grid; grid-template-columns: 1fr 150px 60px 50px; gap: 10px; padding: 10px 15px; border-bottom: 1px solid #eee; align-items: center;">
                            <input type="hidden" name="existing_directors[{{ $index }}][id]" value="{{ $director->id }}">
                            <input type="text" name="existing_directors[{{ $index }}][name]" value="{{ $director->name }}" placeholder="{{ __('Director name') }}" 
                                   style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                            <input type="file" name="existing_directors[{{ $index }}][avatar]" accept="image/*" 
                                   style="width: 100%; font-size: 0.8em;">
                            <div>
                                @if ($director->avatar)
                                    <img src="{{ asset('storage/' . $director->avatar->file_path) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9em;">
                                        {{ substr($director->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="removeExistingRow(this, 'director')" 
                                    style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                🗑
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button type="button" onclick="addDirectorRow()" style="margin-top: 10px; padding: 8px 16px; background: #17a2b8; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em;">
                    ⊕ {{ __('Add item') }}
                </button>
                @error('directors')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Actors Section -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Actors') }}
                </label>
                <div style="border: 1px solid #ddd; border-radius: 6px; overflow: hidden;">
                    <div style="background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #ddd; display: grid; grid-template-columns: 1fr 150px 60px 50px; gap: 10px; font-weight: 600; color: #2c3e50;">
                        <span>{{ __('Name') }}</span>
                        <span>{{ __('Avatar') }}</span>
                        <span>{{ __('Current') }}</span>
                        <span></span>
                    </div>
                    <div id="actors-container">
                        @foreach ($movie->actors as $index => $actor)
                        <div class="actor-row" data-actor-id="{{ $actor->id }}" style="display: grid; grid-template-columns: 1fr 150px 60px 50px; gap: 10px; padding: 10px 15px; border-bottom: 1px solid #eee; align-items: center;">
                            <input type="hidden" name="existing_actors[{{ $index }}][id]" value="{{ $actor->id }}">
                            <input type="text" name="existing_actors[{{ $index }}][name]" value="{{ $actor->name }}" placeholder="{{ __('Actor name') }}" 
                                   style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                            <input type="file" name="existing_actors[{{ $index }}][avatar]" accept="image/*" 
                                   style="width: 100%; font-size: 0.8em;">
                            <div>
                                @if ($actor->avatar)
                                    <img src="{{ asset('storage/' . $actor->avatar->file_path) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9em;">
                                        {{ substr($actor->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="removeExistingRow(this, 'actor')" 
                                    style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                🗑
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button type="button" onclick="addActorRow()" style="margin-top: 10px; padding: 8px 16px; background: #17a2b8; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em;">
                    ⊕ {{ __('Add item') }}
                </button>
                @error('actors')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="language" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Language') }}
                </label>
                <input 
                    type="text" 
                    name="language" 
                    id="language" 
                    value="{{ old('language', $movie->language) }}"
                    placeholder="{{ __('e.g., Vietnamese, English') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                @error('language')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                {{ __('Description') }}
            </label>
            <textarea 
                name="description" 
                id="description" 
                rows="4"
                placeholder="{{ __('Enter movie description') }}"
                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box; resize: vertical;"
            >{{ old('description', $movie->description) }}</textarea>
            @error('description')
                <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="movie-form-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label for="poster" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Poster Image') }}
                </label>
                @if ($movie->poster)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ asset('storage/' . $movie->poster->file_path) }}" alt="{{ $movie->title }}" style="width: 100px; height: 150px; object-fit: cover; border-radius: 4px;">
                        <p style="color: #7f8c8d; font-size: 0.85em; margin-top: 5px;">{{ __('Current poster') }}</p>
                    </div>
                @endif
                <input 
                    type="file" 
                    name="poster" 
                    id="poster" 
                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                <small style="color: #7f8c8d; margin-top: 5px; display: block;">{{ __('Leave empty to keep current. Max size: 5MB') }}</small>
                @error('poster')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="trailer" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Trailer Video') }}
                </label>
                @if ($movie->trailer)
                    <div style="margin-bottom: 10px;">
                        <p style="color: #27ae60; font-size: 0.9em;">✓ {{ __('Trailer uploaded') }}: {{ $movie->trailer->file_name }}</p>
                    </div>
                @endif
                <input 
                    type="file" 
                    name="trailer" 
                    id="trailer" 
                    accept="video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                <small style="color: #7f8c8d; margin-top: 5px; display: block;">{{ __('Leave empty to keep current. Max size: 100MB') }}</small>
                @error('trailer')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="movie-actions" style="display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid #eee; flex-wrap: wrap;">
            <button 
                type="submit" 
                style="padding: 12px 30px; background: #3498db; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; font-weight: 600;"
            >
                {{ __('Update Movie') }}
            </button>
            <a 
                href="{{ route('admin.movies.show', $movie->id) }}" 
                style="padding: 12px 30px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em; font-weight: 600;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
let directorIndex = {{ $movie->directors->count() }};
let actorIndex = {{ $movie->actors->count() }};
const deletedDirectors = [];
const deletedActors = [];

// Create hidden input container for deleted items if not exists
function ensureDeletedInputsContainer() {
    let container = document.getElementById('deleted-items-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'deleted-items-container';
        container.style.display = 'none';
        document.querySelector('form').appendChild(container);
    }
    return container;
}

function addDirectorRow(name = '') {
    const container = document.getElementById('directors-container');
    const row = document.createElement('div');
    row.className = 'director-row';
    row.style.cssText = 'display: grid; grid-template-columns: 1fr 150px 60px 50px; gap: 10px; padding: 10px 15px; border-bottom: 1px solid #eee; align-items: center;';
    row.innerHTML = `
        <input type="text" name="directors[${directorIndex}][name]" value="${name}" placeholder="{{ __('Director name') }}" 
               style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
        <input type="file" name="directors[${directorIndex}][avatar]" accept="image/*" 
               style="width: 100%; font-size: 0.8em;">
        <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9em;">
            New
        </div>
        <button type="button" onclick="removeRow(this)" 
                style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🗑
        </button>
    `;
    container.appendChild(row);
    directorIndex++;
}

function addActorRow(name = '') {
    const container = document.getElementById('actors-container');
    const row = document.createElement('div');
    row.className = 'actor-row';
    row.style.cssText = 'display: grid; grid-template-columns: 1fr 150px 60px 50px; gap: 10px; padding: 10px 15px; border-bottom: 1px solid #eee; align-items: center;';
    row.innerHTML = `
        <input type="text" name="actors[${actorIndex}][name]" value="${name}" placeholder="{{ __('Actor name') }}" 
               style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
        <input type="file" name="actors[${actorIndex}][avatar]" accept="image/*" 
               style="width: 100%; font-size: 0.8em;">
        <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9em;">
            New
        </div>
        <button type="button" onclick="removeRow(this)" 
                style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🗑
        </button>
    `;
    container.appendChild(row);
    actorIndex++;
}

function removeRow(button) {
    button.closest('div').remove();
}

function removeExistingRow(button, type) {
    const row = button.closest('div');
    const id = row.getAttribute(`data-${type}-id`);
    
    if (id) {
        if (type === 'director') {
            deletedDirectors.push(id);
        } else if (type === 'actor') {
            deletedActors.push(id);
        }
        
        // Add hidden inputs for deleted items
        const container = ensureDeletedInputsContainer();
        if (type === 'director') {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_directors[]';
            input.value = id;
            container.appendChild(input);
        } else if (type === 'actor') {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_actors[]';
            input.value = id;
            container.appendChild(input);
        }
    }
    
    row.remove();
}
</script>

<style>
    @media (max-width: 768px) {
        .movie-form-grid-2 {
            grid-template-columns: 1fr !important;
        }
        #directors-container .director-row,
        #actors-container .actor-row {
            grid-template-columns: 1fr !important;
        }
        .movie-actions {
            flex-direction: column;
        }
        .movie-actions button,
        .movie-actions a {
            width: 100%;
            text-align: center;
        }
    }
</style>

@endsection
