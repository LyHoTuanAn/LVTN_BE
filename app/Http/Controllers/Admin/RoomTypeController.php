<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of room types.
     */
    public function index(Request $request)
    {
        $query = RoomType::with('image');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $roomTypes = $query->orderBy('name')->paginate(15);

        return view('admin.room-types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new room type.
     */
    public function create()
    {
        return view('admin.room-types.create');
    }

    /**
     * Store a newly created room type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:room_types,name',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        $imageId = null;
        if ($request->hasFile('image')) {
            $imageId = $this->uploadImage($request->file('image'));
        }

        RoomType::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_id' => $imageId,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.room-types.index')
            ->with('success', __('Room type created successfully'));
    }

    /**
     * Display the specified room type.
     */
    public function show(RoomType $roomType)
    {
        $roomType->load(['image', 'rooms']);
        return view('admin.room-types.show', compact('roomType'));
    }

    /**
     * Show the form for editing the specified room type.
     */
    public function edit(RoomType $roomType)
    {
        $roomType->load('image');
        return view('admin.room-types.edit', compact('roomType'));
    }

    /**
     * Update the specified room type.
     */
    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:room_types,name,' . $roomType->id,
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($roomType->image_id) {
                $this->deleteImage($roomType->image_id);
            }
            $validated['image_id'] = $this->uploadImage($request->file('image'));
        }

        $roomType->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_id' => $validated['image_id'] ?? $roomType->image_id,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.room-types.index')
            ->with('success', __('Room type updated successfully'));
    }

    /**
     * Remove the specified room type.
     */
    public function destroy(RoomType $roomType)
    {
        // Check if room type is being used
        if ($roomType->rooms()->exists()) {
            return redirect()
                ->route('admin.room-types.index')
                ->with('error', __('Cannot delete room type that is being used by rooms'));
        }

        // Delete image if exists
        if ($roomType->image_id) {
            $this->deleteImage($roomType->image_id);
        }

        $roomType->delete();

        return redirect()
            ->route('admin.room-types.index')
            ->with('success', __('Room type deleted successfully'));
    }

    /**
     * Upload image and create media file record
     */
    private function uploadImage($file): int
    {
        $path = $file->store('room-types', 'public');
        
        $mediaFile = MediaFile::create([
            'folder_id' => null,
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'type' => 'image',
            'size' => $file->getSize(),
        ]);

        return $mediaFile->id;
    }

    /**
     * Delete image from storage and database
     */
    private function deleteImage(int $imageId): void
    {
        $mediaFile = MediaFile::find($imageId);
        if ($mediaFile) {
            Storage::disk('public')->delete($mediaFile->file_path);
            $mediaFile->delete();
        }
    }
}
