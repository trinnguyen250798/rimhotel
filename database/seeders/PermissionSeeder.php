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
            // Hotels
            ['name' => 'view_hotels', 'display_name' => 'Xem khách sạn', 'module' => 'hotels'],
            ['name' => 'manage_hotels', 'display_name' => 'Quản lý khách sạn', 'module' => 'hotels'],

            // Room Types
            ['name' => 'view_room_types', 'display_name' => 'Xem loại phòng', 'module' => 'room_types'],
            ['name' => 'manage_room_types', 'display_name' => 'Quản lý loại phòng', 'module' => 'room_types'],

            // Rooms
            ['name' => 'view_rooms', 'display_name' => 'Xem phòng', 'module' => 'rooms'],
            ['name' => 'manage_rooms', 'display_name' => 'Quản lý phòng', 'module' => 'rooms'],
            ['name' => 'update_room_status', 'display_name' => 'Cập nhật trạng thái phòng', 'module' => 'rooms'],

            // Staff Management
            ['name' => 'view_staff', 'display_name' => 'Xem nhân viên', 'module' => 'staff'],
            ['name' => 'manage_staff', 'display_name' => 'Quản lý nhân viên', 'module' => 'staff'],
            ['name' => 'manage_departments', 'display_name' => 'Quản lý phòng ban', 'module' => 'staff'],
            ['name' => 'manage_positions', 'display_name' => 'Quản lý chức vụ', 'module' => 'staff'],
            ['name' => 'manage_permissions', 'display_name' => 'Quản lý quyền hạn', 'module' => 'staff'],

            // Bookings (Future use)
            ['name' => 'view_bookings', 'display_name' => 'Xem đặt phòng', 'module' => 'bookings'],
            ['name' => 'manage_bookings', 'display_name' => 'Quản lý đặt phòng', 'module' => 'bookings'],
            ['name' => 'check_in_out', 'display_name' => 'Check-in/Check-out', 'module' => 'bookings'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'module' => $permission['module'],
                    'description' => $permission['display_name'] . ' trong hệ thống.'
                ]
            );
        }
    }
}
