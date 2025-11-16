<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Movie permissions
            ['name' => 'Manage Movies', 'slug' => 'manage_movies', 'description' => 'Quản lý phim'],
            ['name' => 'View Movies', 'slug' => 'view_movies', 'description' => 'Xem danh sách phim'],
            
            // Cinema permissions
            ['name' => 'Manage Cinemas', 'slug' => 'manage_cinemas', 'description' => 'Quản lý rạp chiếu phim'],
            ['name' => 'View Cinemas', 'slug' => 'view_cinemas', 'description' => 'Xem danh sách rạp'],
            
            // Showtime permissions
            ['name' => 'Manage Showtimes', 'slug' => 'manage_showtimes', 'description' => 'Quản lý suất chiếu'],
            ['name' => 'View Showtimes', 'slug' => 'view_showtimes', 'description' => 'Xem suất chiếu'],
            
            // Booking permissions
            ['name' => 'Manage Bookings', 'slug' => 'manage_bookings', 'description' => 'Quản lý đặt vé'],
            ['name' => 'View Bookings', 'slug' => 'view_bookings', 'description' => 'Xem đặt vé'],
            
            // Role & Permission permissions
            ['name' => 'Manage Roles', 'slug' => 'manage_roles', 'description' => 'Quản lý vai trò'],
            ['name' => 'Manage Permissions', 'slug' => 'manage_permissions', 'description' => 'Quản lý quyền hạn'],
            
            // User permissions
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'description' => 'Quản lý người dùng'],
            ['name' => 'View Users', 'slug' => 'view_users', 'description' => 'Xem người dùng'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
