@extends('layouts.admin')

@section('title', __('Media Management'))
@section('page-title', __('Media Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #2c3e50; font-size: 1.8em; margin: 0;">{{ __('Media Folders') }}</h2>
        <button 
            onclick="document.getElementById('createFolderForm').style.display = document.getElementById('createFolderForm').style.display === 'none' ? 'block' : 'none';"
            style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em; font-weight: 600;"
        >
            + {{ __('Create Folder') }}
        </button>
    </div>

    <!-- Create Folder Form -->
    <div id="createFolderForm" style="display: none; margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px;">
        <h3 style="color: #2c3e50; font-size: 1.2em; margin-bottom: 15px;">{{ __('Create New Folder') }}</h3>
        <form method="POST" action="{{ route('admin.media.create-folder') }}">
            @csrf
            <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                        {{ __('Folder Name') }} <span style="color: #e74c3c;">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        maxlength="150"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                        placeholder="{{ __('Enter folder name') }}"
                    >
                    @error('name')
                        <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                        {{ __('Parent Folder') }}
                    </label>
                    <select 
                        name="parent_id" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                    >
                        <option value="">{{ __('Root (No Parent)') }}</option>
                        @foreach ($allFolders as $folder)
                            <option value="{{ $folder->id }}" {{ old('parent_id') == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                <div style="display: flex; gap: 10px;">
                    <button 
                        type="submit"
                        style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em; font-weight: 600;"
                    >
                        {{ __('Create') }}
                    </button>
                    <button 
                        type="button"
                        onclick="document.getElementById('createFolderForm').style.display = 'none';"
                        style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em;"
                    >
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Folders List -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 1.3em; margin-bottom: 20px;">{{ __('Folders') }}</h3>
        
        @if ($folders->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                @foreach ($folders as $folder)
                    <div style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: #f8f9fa; transition: box-shadow 0.3s;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <div style="flex: 1;">
                                <h4 style="color: #2c3e50; font-size: 1.1em; margin: 0 0 5px 0;">
                                    📁 {{ $folder->name }}
                                </h4>
                                @if ($folder->parent)
                                    <div style="color: #666; font-size: 0.85em; margin-bottom: 5px;">
                                        {{ __('Parent') }}: {{ $folder->parent->name }}
                                    </div>
                                @else
                                    <div style="color: #666; font-size: 0.85em; margin-bottom: 5px;">
                                        {{ __('Root Folder') }}
                                    </div>
                                @endif
                                <div style="color: #999; font-size: 0.8em;">
                                    {{ __('Files') }}: {{ $folder->files->count() }} | 
                                    {{ __('Subfolders') }}: {{ $folder->children->count() }}
                                </div>
                            </div>
                            <form 
                                method="POST" 
                                action="{{ route('admin.media.delete-folder', $folder->id) }}"
                                style="display: inline;"
                                onsubmit="return confirm('{{ __('Are you sure you want to delete this folder?') }}');"
                            >
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    style="padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8em;"
                                    title="{{ __('Delete Folder') }}"
                                >
                                    🗑️
                                </button>
                            </form>
                        </div>
                        <div style="color: #999; font-size: 0.75em;">
                            {{ __('Created') }}: {{ $folder->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 30px;">
                {{ $folders->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #999;">
                <p style="font-size: 1.1em;">{{ __('No folders found') }}</p>
                <p style="font-size: 0.9em; margin-top: 10px;">{{ __('Create your first folder to get started') }}</p>
            </div>
        @endif
    </div>

    <!-- Upload File Form -->
    <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px;">
        <h3 style="color: #2c3e50; font-size: 1.3em; margin-bottom: 15px;">{{ __('Upload Image') }}</h3>
        <form method="POST" action="{{ route('admin.media.upload-file') }}" enctype="multipart/form-data">
            @csrf
            <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: start;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                        {{ __('Select Image') }} <span style="color: #e74c3c;">*</span>
                    </label>
                    <input 
                        type="file" 
                        name="file" 
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                        required
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                    >
                    <div style="margin-top: 5px; color: #666; font-size: 0.85em;">
                        {{ __('Max size: 10MB. Image will be converted to WebP format.') }}
                    </div>
                    @error('file')
                        <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                        {{ __('Target Folder') }}
                    </label>
                    <select 
                        name="folder_id" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                    >
                        <option value="">{{ __('No Folder (Root)') }}</option>
                        @foreach ($allFolders as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                    @error('folder_id')
                        <div style="color: #e74c3c; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                <div style="display: flex; align-items: flex-start; padding-top: 28px;">
                    <button 
                        type="submit"
                        style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em; font-weight: 600;"
                    >
                        {{ __('Upload') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Recent Files -->
    <div>
        <h3 style="color: #2c3e50; font-size: 1.3em; margin-bottom: 20px;">{{ __('Recent Files') }}</h3>
        
        @php
            $recentFiles = \App\Models\MediaFile::with(['folder', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        @endphp

        @if ($recentFiles->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('File Name') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Folder') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Type') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Size') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Uploaded By') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Created At') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Preview') }}</th>
                            <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentFiles as $file)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px; color: #2c3e50;">{{ $file->file_name }}</td>
                                <td style="padding: 12px; color: #666;">
                                    {{ $file->folder ? $file->folder->name : __('No Folder') }}
                                </td>
                                <td style="padding: 12px; color: #666;">{{ $file->mime_type }}</td>
                                <td style="padding: 12px; color: #666;">{{ number_format($file->size / 1024, 2) }} KB</td>
                                <td style="padding: 12px; color: #666;">{{ $file->user->name ?? __('Unknown') }}</td>
                                <td style="padding: 12px; color: #666;">{{ $file->created_at->format('Y-m-d H:i') }}</td>
                                <td style="padding: 12px;">
                                    @if (str_starts_with($file->mime_type, 'image/'))
                                        <img 
                                            src="{{ asset('storage/' . $file->file_path) }}" 
                                            alt="{{ $file->file_name }}"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;"
                                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'50\' height=\'50\'%3E%3Crect fill=\'%23ddd\' width=\'50\' height=\'50\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'10\'%3ENo Image%3C/text%3E%3C/svg%3E'"
                                        >
                                    @else
                                        <span style="color: #999;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 12px;">
                                    <div style="display: flex; gap: 5px;">
                                        <button 
                                            onclick="showMoveModal({{ $file->id }}, {{ $file->folder_id ?? 'null' }})"
                                            style="padding: 5px 10px; background: #f39c12; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8em;"
                                            title="{{ __('Move File') }}"
                                        >
                                            📁
                                        </button>
                                        <form 
                                            method="POST" 
                                            action="{{ route('admin.media.delete-file', $file->id) }}"
                                            style="display: inline;"
                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this file?') }}');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit"
                                                style="padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8em;"
                                                title="{{ __('Delete File') }}"
                                            >
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #999;">
                <p style="font-size: 1.1em;">{{ __('No files found') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Move File Modal -->
<div id="moveFileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <h3 style="color: #2c3e50; font-size: 1.3em; margin-bottom: 20px;">{{ __('Move File') }}</h3>
        <form method="POST" id="moveFileForm">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 500;">
                    {{ __('Select Folder') }}
                </label>
                <select 
                    name="folder_id" 
                    id="moveFileFolderSelect"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;"
                >
                    <option value="">{{ __('No Folder (Root)') }}</option>
                    @foreach ($allFolders as $folder)
                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button 
                    type="button"
                    onclick="document.getElementById('moveFileModal').style.display = 'none';"
                    style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em;"
                >
                    {{ __('Cancel') }}
                </button>
                <button 
                    type="submit"
                    style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em; font-weight: 600;"
                >
                    {{ __('Move') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showMoveModal(fileId, currentFolderId) {
        const modal = document.getElementById('moveFileModal');
        const form = document.getElementById('moveFileForm');
        const select = document.getElementById('moveFileFolderSelect');
        
        form.action = '{{ route("admin.media.move-file", ":id") }}'.replace(':id', fileId);
        
        if (currentFolderId) {
            select.value = currentFolderId;
        } else {
            select.value = '';
        }
        
        modal.style.display = 'flex';
    }
    
    // Close modal when clicking outside
    document.getElementById('moveFileModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
</script>
@endsection

