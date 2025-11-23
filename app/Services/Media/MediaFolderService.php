<?php

namespace App\Services\Media;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;

class MediaFolderService
{
    /**
     * Get all folders with pagination
     */
    public function getAllFolders(array $filters = [])
    {
        $query = MediaFolder::with(['parent', 'children', 'files']);

        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null' || $filters['parent_id'] === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->orderBy('name')->paginate(20);
    }

    /**
     * Get folder by ID
     */
    public function getFolderById(int $id): ?MediaFolder
    {
        return MediaFolder::with(['parent', 'children', 'files'])->find($id);
    }

    /**
     * Create a new folder
     */
    public function createFolder(array $data): MediaFolder
    {
        return DB::transaction(function () use ($data) {
            return MediaFolder::create([
                'name' => $data['name'],
                'parent_id' => $data['parent_id'] ?? null,
            ]);
        });
    }

    /**
     * Delete a folder
     */
    public function deleteFolder(int $folderId): bool
    {
        return DB::transaction(function () use ($folderId) {
            $folder = MediaFolder::findOrFail($folderId);

            // Check if folder has children
            if ($folder->children()->count() > 0) {
                throw new \Exception(__('Cannot delete folder with subfolders'));
            }

            // Check if folder has files
            if ($folder->files()->count() > 0) {
                throw new \Exception(__('Cannot delete folder with files'));
            }

            return $folder->delete();
        });
    }

    /**
     * Get root folders (folders without parent)
     */
    public function getRootFolders()
    {
        return MediaFolder::whereNull('parent_id')
            ->with(['children', 'files'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all folders for dropdown
     */
    public function getAllFoldersForDropdown(?int $excludeId = null)
    {
        $query = MediaFolder::orderBy('name');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }
}

