<?php

namespace App\Services\Media;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    protected string $disk = 'public';
    protected string $basePath = 'media/avatars';

    /**
     * Upload and convert image to WebP
     */
    public function uploadImage(UploadedFile $file, int $userId, ?int $folderId = null): MediaFile
    {
        // Validate image
        if (!$file->isValid() || !$this->isImage($file)) {
            throw new \Exception(__('Invalid image file'));
        }

        // Generate unique filename
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = 'webp';
        $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

        // Create directory if not exists
        $directory = $this->basePath . '/' . date('Y/m');
        Storage::disk($this->disk)->makeDirectory($directory);

        // Full path
        $filePath = $directory . '/' . $fileName;

        // Convert to WebP and save
        $this->convertToWebP($file, $filePath);

        // Get file size
        $fileSize = Storage::disk($this->disk)->size($filePath);

        // Create media file record
        $mediaFile = MediaFile::create([
            'folder_id' => $folderId,
            'user_id' => $userId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => 'image/webp',
            'size' => $fileSize,
            'type' => 'image',
        ]);

        return $mediaFile;
    }

    /**
     * Check if file is an image
     */
    protected function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Convert image to WebP format using GD
     */
    protected function convertToWebP(UploadedFile $file, string $outputPath): void
    {
        try {
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                throw new \Exception(__('GD extension is not available'));
            }

            // Get image info
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                throw new \Exception(__('Invalid image file'));
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mimeType = $imageInfo['mime'];

            // Create image resource from file
            $image = match ($mimeType) {
                'image/jpeg', 'image/jpg' => imagecreatefromjpeg($file->getRealPath()),
                'image/png' => imagecreatefrompng($file->getRealPath()),
                'image/gif' => imagecreatefromgif($file->getRealPath()),
                'image/webp' => imagecreatefromwebp($file->getRealPath()),
                default => throw new \Exception(__('Unsupported image format: :format', ['format' => $mimeType])),
            };

            if ($image === false) {
                throw new \Exception(__('Failed to create image resource'));
            }

            // Convert palette images to truecolor (required for WebP)
            if (!imageistruecolor($image)) {
                $truecolorImage = imagecreatetruecolor($width, $height);
                
                // Preserve transparency for PNG and GIF
                if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                    imagealphablending($truecolorImage, false);
                    imagesavealpha($truecolorImage, true);
                    $transparent = imagecolorallocatealpha($truecolorImage, 255, 255, 255, 127);
                    imagefilledrectangle($truecolorImage, 0, 0, $width, $height, $transparent);
                }
                
                // Copy image to truecolor
                imagecopy($truecolorImage, $image, 0, 0, 0, 0, $width, $height);
                
                // Free old image and use truecolor version
                imagedestroy($image);
                $image = $truecolorImage;
            }

            // Get full path for saving
            $fullPath = Storage::disk($this->disk)->path($outputPath);

            // Ensure directory exists
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Convert and save as WebP (quality: 90)
            $success = imagewebp($image, $fullPath, 90);

            // Free memory
            imagedestroy($image);

            if (!$success) {
                throw new \Exception(__('Failed to save WebP image'));
            }
        } catch (\Exception $e) {
            throw new \Exception(__('Failed to convert image to WebP: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Delete media file
     */
    public function deleteMediaFile(int $mediaFileId): bool
    {
        $mediaFile = MediaFile::find($mediaFileId);

        if (!$mediaFile) {
            return false;
        }

        // Delete physical file
        if (Storage::disk($this->disk)->exists($mediaFile->file_path)) {
            Storage::disk($this->disk)->delete($mediaFile->file_path);
        }

        // Delete database record
        $mediaFile->delete();

        return true;
    }

    /**
     * Get public URL for media file
     */
    public function getUrl(MediaFile $mediaFile): string
    {
        return asset('storage/' . $mediaFile->file_path);
    }

    /**
     * Move file to another folder
     */
    public function moveFile(int $fileId, ?int $folderId): MediaFile
    {
        $mediaFile = MediaFile::findOrFail($fileId);
        
        $mediaFile->folder_id = $folderId;
        $mediaFile->save();
        
        return $mediaFile;
    }
}

