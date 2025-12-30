@extends('layouts.app')

@section('title', __('Edit Room Type'))
@section('page-title', __('Edit Room Type'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.room-types.index') }}" style="color: #3498db; text-decoration: none;">
            ← {{ __('Back to Room Types') }}
        </a>
    </div>

    <h2 style="color: #2c3e50; font-size: 1.5em; margin-bottom: 20px;">{{ __('Edit Room Type') }}: {{ $roomType->name }}</h2>

    @if ($errors->any())
        <div style="background: #fee2e2; border: 1px solid #f87171; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.room-types.update', $roomType->id) }}" enctype="multipart/form-data" style="max-width: 600px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 16px;">
            <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Type Name') }} <span style="color: #e74c3c;">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name', $roomType->name) }}" 
                required
                maxlength="100"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
        </div>

        <div style="margin-bottom: 16px;">
            <label for="description" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Description') }}
            </label>
            <textarea 
                id="description" 
                name="description" 
                rows="3"
                maxlength="500"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box; resize: vertical;"
            >{{ old('description', $roomType->description) }}</textarea>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="image" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Image') }}
            </label>
            <!-- Current Image -->
            @if($roomType->image)
                <div style="margin-bottom: 10px;">
                    <p style="color: #6b7280; font-size: 0.85em; margin-bottom: 6px;">{{ __('Current Image') }}:</p>
                    <img 
                        src="{{ Storage::url($roomType->image->file_path) }}" 
                        alt="{{ $roomType->name }}" 
                        style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #d1d5db;"
                    >
                </div>
            @endif
            <!-- Preview -->
            <div id="image-preview-container" style="margin-bottom: 10px; display: none;">
                <p style="color: #6b7280; font-size: 0.85em; margin-bottom: 6px;">{{ __('New Image Preview') }}:</p>
                <img id="image-preview" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #d1d5db;">
            </div>
            <input 
                type="file" 
                id="image" 
                name="image" 
                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                onchange="previewImage(this)"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box; background: white;"
            >
            <p style="color: #6b7280; font-size: 0.85em; margin-top: 6px;">{{ __('Leave empty to keep current image. Max size: 2MB.') }}</p>
        </div>

        <div style="margin-bottom: 24px;">
            <label for="status" style="display: block; margin-bottom: 6px; font-weight: 500; color: #374151;">
                {{ __('Status') }} <span style="color: #e74c3c;">*</span>
            </label>
            <select 
                id="status" 
                name="status" 
                required
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1em; box-sizing: border-box;"
            >
                <option value="active" {{ old('status', $roomType->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ old('status', $roomType->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
        </div>

        <div style="display: flex; gap: 12px;">
            <button 
                type="submit" 
                style="padding: 12px 24px; background: #3498db; color: white; border: none; border-radius: 6px; font-size: 1em; cursor: pointer;"
            >
                {{ __('Update Room Type') }}
            </button>
            <a 
                href="{{ route('admin.room-types.index') }}" 
                style="padding: 12px 24px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-size: 1em;"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const container = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        container.style.display = 'none';
    }
}
</script>
@endsection
