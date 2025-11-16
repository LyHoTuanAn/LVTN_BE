<?php

namespace App\Services\Permission;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{
    /**
     * Get all permissions
     */
    public function getAllPermissions(): Collection
    {
        return Permission::with('roles')->get();
    }

    /**
     * Get permission by ID
     */
    public function getPermissionById(int $id): ?Permission
    {
        return Permission::with('roles')->find($id);
    }

    /**
     * Create a new permission
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Update permission
     */
    public function updatePermission(int $id, array $data): bool
    {
        $permission = Permission::find($id);
        
        if (!$permission) {
            return false;
        }

        return $permission->update($data);
    }

    /**
     * Delete permission
     */
    public function deletePermission(int $id): bool
    {
        $permission = Permission::find($id);
        
        if (!$permission) {
            return false;
        }

        return $permission->delete();
    }
}


