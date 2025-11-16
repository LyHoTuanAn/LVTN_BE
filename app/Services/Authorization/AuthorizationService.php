<?php

namespace App\Services\Authorization;

use App\Models\User;

class AuthorizationService
{
    /**
     * Check if user has a specific permission
     */
    public function hasPermission(User $user, string $permissionSlug): bool
    {
        if (!$user->role) {
            return false;
        }

        return $user->role
            ->permissions()
            ->where('slug', $permissionSlug)
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(User $user, array $permissionSlugs): bool
    {
        if (!$user->role) {
            return false;
        }

        return $user->role
            ->permissions()
            ->whereIn('slug', $permissionSlugs)
            ->exists();
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(User $user, array $permissionSlugs): bool
    {
        if (!$user->role) {
            return false;
        }

        $userPermissions = $user->role->permissions()->pluck('slug')->toArray();

        foreach ($permissionSlugs as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(User $user, string $roleSlug): bool
    {
        return $user->role && $user->role->slug === $roleSlug;
    }
}


