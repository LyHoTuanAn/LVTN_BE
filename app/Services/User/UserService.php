<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;
use App\Services\Media\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    /**
     * Get all users with pagination
     */
    public function getAllUsers(array $filters = [])
    {
        $query = User::with(['role', 'avatar']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?User
    {
        return User::with(['role', 'avatar'])->find($id);
    }

    /**
     * Update user information
     */
    public function updateUser(int $userId, array $data, ?UploadedFile $avatarFile = null): User
    {
        $user = User::findOrFail($userId);

        return DB::transaction(function () use ($user, $data, $avatarFile) {
            // Email is not allowed to be changed, remove it from data
            unset($data['email']);

            // Handle avatar upload
            if ($avatarFile) {
                // Upload and convert to WebP
                $mediaFile = $this->mediaService->uploadImage($avatarFile, $user->id);

                // Delete old avatar if exists
                if ($user->avatar_id) {
                    $this->mediaService->deleteMediaFile($user->avatar_id);
                }

                // Set new avatar
                $data['avatar_id'] = $mediaFile->id;
            }

            // Update password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Handle email verification toggle
            // email_verified will always be in request (via hidden input)
            // "1" means verified, "0" means not verified
            if (isset($data['email_verified'])) {
                if ($data['email_verified'] == '1' || $data['email_verified'] === 1 || $data['email_verified'] === true) {
                    $data['email_verified_at'] = now();
                } else {
                    $data['email_verified_at'] = null;
                }
                unset($data['email_verified']);
            }

            // Update user
            $user->update($data);

            // Reload relationships
            $user->load(['role', 'avatar']);

            return $user;
        });
    }

    /**
     * Verify user email
     */
    public function verifyUserEmail(int $userId): User
    {
        $user = User::findOrFail($userId);

        // Always update email_verified_at
        $user->email_verified_at = now();
        $saved = $user->save();

        if (!$saved) {
            throw new \Exception(__('Failed to save email verification status'));
        }

        // Reload to get fresh data
        $user->refresh();
        $user->load(['role', 'avatar']);

        return $user;
    }

    /**
     * Get all roles for dropdown
     */
    public function getAllRoles()
    {
        return Role::orderBy('name')->get();
    }
}

