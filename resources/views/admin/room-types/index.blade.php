@extends('layouts.app')

@section('title', __('Room Types'))
@section('page-title', __('Room Types'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('Room Type List') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.room-types.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select 
                    name="status" 
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em; background: white;"
                >
                    <option value="">{{ __('All Status') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by name...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <button type="submit" style="padding: 0 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; height: 38px;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.room-types.create') }}" 
                style="padding: 0 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; height: 38px;"
            >
                + {{ __('Add Room Type') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        @forelse ($roomTypes as $roomType)
            <div style="background: #f8f9fa; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; transition: transform 0.2s, box-shadow 0.2s;">
                <!-- Image -->
                <div style="height: 160px; background: #e0e0e0; position: relative; overflow: hidden;">
                    @if($roomType->image)
                        <img 
                            src="{{ Storage::url($roomType->image->file_path) }}" 
                            alt="{{ $roomType->name }}"
                            style="width: 100%; height: 100%; object-fit: cover;"
                        >
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <svg style="width: 48px; height: 48px; color: white; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                    <!-- Status Badge -->
                    <span style="position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 20px; font-size: 0.75em; font-weight: 600; {{ $roomType->status == 'active' ? 'background: #27ae60; color: white;' : 'background: #e74c3c; color: white;' }}">
                        {{ $roomType->status == 'active' ? __('Active') : __('Inactive') }}
                    </span>
                </div>
                <!-- Content -->
                <div style="padding: 16px;">
                    <h3 style="color: #2c3e50; font-size: 1.1em; margin: 0 0 8px 0;">{{ $roomType->name }}</h3>
                    @if($roomType->description)
                        <p style="color: #666; font-size: 0.85em; margin: 0 0 12px 0; line-height: 1.4;">
                            {{ Str::limit($roomType->description, 80) }}
                        </p>
                    @endif
                    <div style="display: flex; gap: 8px; margin-top: 12px;">
                        <a 
                            href="{{ route('admin.room-types.edit', $roomType->id) }}" 
                            style="flex: 1; padding: 8px 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; text-align: center;"
                        >
                            {{ __('Edit') }}
                        </a>
                        <form method="POST" action="{{ route('admin.room-types.destroy', $roomType->id) }}" style="flex: 1;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width: 100%; padding: 8px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 0.85em; cursor: pointer;">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #666;">
                <svg style="width: 64px; height: 64px; margin: 0 auto 16px; color: #ccc;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p style="font-size: 1.1em; margin-bottom: 8px;">{{ __('No room types found') }}</p>
                <a href="{{ route('admin.room-types.create') }}" style="color: #3498db; text-decoration: none;">{{ __('Create your first room type') }}</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($roomTypes->hasPages())
        <div style="margin-top: 24px;">
            {{ $roomTypes->links() }}
        </div>
    @endif
</div>
@endsection
