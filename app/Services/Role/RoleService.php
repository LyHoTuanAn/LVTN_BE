<?php

namespace App\Services\Role;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    /**
     * Get all roles
     */
    public function getAllRoles(): Collection
    {
        return Role::with('permissions')->get();
    }

    /**
     * Get role by ID
     */
    public function getRoleById(int $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    /**
     * Create a new role
     */
    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update role
     */
    public function updateRole(int $id, array $data): bool
    {
        $role = Role::find($id);
        
        if (!$role) {
            return false;
        }

        return $role->update($data);
    }

    /**
     * Delete role
     */
    public function deleteRole(int $id): bool
    {
        $role = Role::find($id);
        
        if (!$role) {
            return false;
        }

        return $role->delete();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(int $roleId, array $permissionIds): void
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
    }
}


