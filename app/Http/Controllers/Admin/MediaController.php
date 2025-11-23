<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateMediaFolderRequest;
use App\Http\Requests\Admin\UploadMediaFileRequest;
use App\Http\Requests\Admin\MoveMediaFileRequest;
use App\Services\Media\MediaFolderService;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected MediaFolderService $folderService;
    protected MediaService $mediaService;

    public function __construct(MediaFolderService $folderService, MediaService $mediaService)
    {
        $this->folderService = $folderService;
        $this->mediaService = $mediaService;
    }

    /**
     * Display media management page
     */
    public function index(Request $request)
    {
        $folders = $this->folderService->getAllFolders($request->all());
        $rootFolders = $this->folderService->getRootFolders();
        $allFolders = $this->folderService->getAllFoldersForDropdown();

        return view('admin.media.index', compact('folders', 'rootFolders', 'allFolders'));
    }

    /**
     * Create a new folder
     */
    public function createFolder(CreateMediaFolderRequest $request)
    {
        try {
            $folder = $this->folderService->createFolder($request->validated());

            return redirect()
                ->route('admin.media.index')
                ->with('success', __('Folder created successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to create folder: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Delete a folder
     */
    public function deleteFolder(Request $request, int $id)
    {
        try {
            $this->folderService->deleteFolder($id);

            return redirect()
                ->route('admin.media.index')
                ->with('success', __('Folder deleted successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to delete folder: :message', ['message' => $e->getMessage()])]);
        }
    }

    /**
     * Upload a new image file
     */
    public function uploadFile(UploadMediaFileRequest $request)
    {
        try {
            $file = $request->file('file');
            $folderId = $request->input('folder_id');
            $userId = auth()->id();

            $mediaFile = $this->mediaService->uploadImage($file, $userId, $folderId);

            return redirect()
                ->route('admin.media.index')
                ->with('success', __('File uploaded successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to upload file: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Move file to another folder
     */
    public function moveFile(MoveMediaFileRequest $request, int $id)
    {
        try {
            $folderId = $request->input('folder_id');
            $this->mediaService->moveFile($id, $folderId);

            return redirect()
                ->route('admin.media.index')
                ->with('success', __('File moved successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to move file: :message', ['message' => $e->getMessage()])]);
        }
    }

    /**
     * Delete a file
     */
    public function deleteFile(Request $request, int $id)
    {
        try {
            $deleted = $this->mediaService->deleteMediaFile($id);

            if (!$deleted) {
                throw new \Exception(__('File not found'));
            }

            return redirect()
                ->route('admin.media.index')
                ->with('success', __('File deleted successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to delete file: :message', ['message' => $e->getMessage()])]);
        }
    }
}

