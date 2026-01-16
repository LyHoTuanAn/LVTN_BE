@extends('layouts.app')

@section('title', __('Add Movie'))
@section('page-title', __('Add Movie'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.movies.index') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
            ← {{ __('Back to List') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 30px;">{{ __('Create New Movie') }}</h2>

    <form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="movie-form-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Title') }} <span style="color: #e74c3c;">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}"
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
                    value="{{ old('duration') }}"
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
                    value="{{ old('release_date') }}"
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
                <div style="padding: 12px 15px; background: #e8f5e9; border-radius: 6px; border: 1px solid #c8e6c9;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="padding: 4px 10px; background: #fff3e0; color: #e65100; border-radius: 4px; font-size: 0.85em; font-weight: 600;">{{ __('Coming Soon') }}</span>
                        <span style="color: #666; font-size: 0.85em;">{{ __('(Default)') }}</span>
                    </div>
                    <p style="margin: 0; color: #2e7d32; font-size: 0.85em; line-height: 1.5;">
                        <strong>{{ __('Note:') }}</strong> {{ __('Status is automatically calculated based on release_date:') }}
                        <br>• <strong>{{ __('Coming Soon') }}</strong>: {{ __('release_date > today') }}
                        <br>• <strong>{{ __('Now Showing') }}</strong>: {{ __('release_date <= today') }}
                        <br><em style="color: #666;">{{ __('Status does not depend on showtimes') }}</em>
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
                    value="{{ old('genre') }}"
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
                    <option value="P" {{ old('age_classification', 'P') == 'P' ? 'selected' : '' }}>P - {{ __('All Ages') }}</option>
                    <option value="K" {{ old('age_classification') == 'K' ? 'selected' : '' }}>K - {{ __('Children (Parental Guidance)') }}</option>
                    <option value="T13" {{ old('age_classification') == 'T13' ? 'selected' : '' }}>T13 - 13+</option>
                    <option value="T16" {{ old('age_classification') == 'T16' ? 'selected' : '' }}>T16 - 16+</option>
                    <option value="T18" {{ old('age_classification') == 'T18' ? 'selected' : '' }}>T18 - 18+</option>
                    <option value="C" {{ old('age_classification') == 'C' ? 'selected' : '' }}>C - {{ __('Prohibited') }}</option>
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
                    <div style="background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #ddd; display: grid; grid-template-columns: 1fr 150px 50px; gap: 10px; font-weight: 600; color: #2c3e50;">
                        <span>{{ __('Name') }}</span>
                        <span>{{ __('Avatar') }}</span>
                        <span></span>
                    </div>
                    <div id="directors-container">
                        <!-- Dynamic rows will be added here -->
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
                    <div style="background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #ddd; display: grid; grid-template-columns: 1fr 150px 50px; gap: 10px; font-weight: 600; color: #2c3e50;">
                        <span>{{ __('Name') }}</span>
                        <span>{{ __('Avatar') }}</span>
                        <span></span>
                    </div>
                    <div id="actors-container">
                        <!-- Dynamic rows will be added here -->
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
                    value="{{ old('language') }}"
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
            >{{ old('description') }}</textarea>
            @error('description')
                <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="movie-form-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label for="poster" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Poster Image') }}
                </label>
                <input 
                    type="file" 
                    name="poster" 
                    id="poster" 
                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                <small style="color: #7f8c8d; margin-top: 5px; display: block;">{{ __('Accepted formats: JPEG, PNG, JPG, GIF, WebP. Max size: 5MB') }}</small>
                @error('poster')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="trailer" style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    {{ __('Trailer Video') }}
                </label>
                <input 
                    type="file" 
                    name="trailer" 
                    id="trailer" 
                    accept="video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
                >
                <small style="color: #7f8c8d; margin-top: 5px; display: block;">{{ __('Accepted formats: MP4, MOV, AVI, WMV. Max size: 100MB') }}</small>
                @error('trailer')
                    <span style="color: #e74c3c; font-size: 0.85em; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="movie-actions" style="display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid #eee; flex-wrap: wrap;">
            <button 
                type="submit" 
                style="padding: 12px 30px; background: #27ae60; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; font-weight: 600;"
            >
                {{ __('Create Movie') }}
            </button>
            <a 
                href="{{ route('admin.movies.index') }}" 
                style="padding: 12px 30px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em; font-weight: 600;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
let directorIndex = 0;
let actorIndex = 0;

function addDirectorRow(name = '', avatarPreview = '') {
    const container = document.getElementById('directors-container');
    const row = document.createElement('div');
    row.className = 'director-row';
    row.style.cssText = 'display: grid; grid-template-columns: 1fr 150px 50px; gap: 10px; padding: 10px 15px; border-bottom: 1px solid #eee; align-items: center;';
    row.innerHTML = `
        <input type="text" name="directors[${directorIndex}][name]" value="${name}" placeholder="{{ __('Director name') }}" 
               style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
        <input type="file" name="directors[${directorIndex}][avatar]" accept="image/*" 
               style="width: 100%; font-size: 0.8em;" onchange="previewImage(this)">
        <button type="button" onclick="removeRow(this)" 
                style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🗑
        </button>
    `;
    container.appendChild(row);
    directorIndex++;
}

function addActorRow(name = '', avatarPreview = '') {
    const container = document.getElementById('actors-container');
    const row = document.createElement('div');
    row.className = 'actor-row';
    row.style.cssText = 'display: grid; grid-template-columns: 1fr 150px 50px; gap: 10px; padding: 10px 15px; border-bottom: 1px solid #eee; align-items: center;';
    row.innerHTML = `
        <input type="text" name="actors[${actorIndex}][name]" value="${name}" placeholder="{{ __('Actor name') }}" 
               style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
        <input type="file" name="actors[${actorIndex}][avatar]" accept="image/*" 
               style="width: 100%; font-size: 0.8em;" onchange="previewImage(this)">
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

function previewImage(input) {
    // Optional: Add image preview functionality here
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
