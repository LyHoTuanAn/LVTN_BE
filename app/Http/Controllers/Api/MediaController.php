<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadImageRequest;
use App\Http\Resources\MediaFileResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    use ApiResponseTrait;

    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Upload image
     *
     * @param UploadImageRequest $request
     * @return JsonResponse
     */
    public function uploadImage(UploadImageRequest $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $folderId = $request->input('folder_id');
            $image = $request->file('image');

            $mediaFile = $this->mediaService->uploadImage($image, $userId, $folderId);

            return $this->successResponse(
                'IMAGE_UPLOADED_SUCCESS',
                new MediaFileResource($mediaFile),
                __('success.IMAGE_UPLOADED_SUCCESS'),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'IMAGE_UPLOAD_FAILED',
                ['image' => $e->getMessage()],
                __('errors.IMAGE_UPLOAD_FAILED'),
                400
            );
        }
    }
}

