<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::where('slug', 'admin')->first();
        $partner = Role::where('slug', 'partner')->first();
        $customer = Role::where('slug', 'customer')->first();

        // Admin gets all permissions
        if ($admin) {
            $allPermissions = Permission::all();
            $admin->permissions()->sync($allPermissions->pluck('id'));
        }

        // Partner permissions
        if ($partner) {
            $partnerPermissions = Permission::whereIn('slug', [
                'manage_cinemas',
                'view_cinemas',
                'manage_showtimes',
                'view_showtimes',
                'manage_bookings',
                'view_bookings',
            ])->get();
            $partner->permissions()->sync($partnerPermissions->pluck('id'));
        }

        // Customer permissions (view only)
        if ($customer) {
            $customerPermissions = Permission::whereIn('slug', [
                'view_movies',
                'view_cinemas',
                'view_showtimes',
                'view_bookings',
            ])->get();
            $customer->permissions()->sync($customerPermissions->pluck('id'));
        }
    }
}
