<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Quản trị viên hệ thống',
            ],
            [
                'name' => 'Partner',
                'slug' => 'partner',
                'description' => 'Đối tác quản lý rạp phim',
            ],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Khách hàng',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
