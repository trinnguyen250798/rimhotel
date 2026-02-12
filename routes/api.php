<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\StaffGroupController;
use App\Http\Controllers\StaffManagementController;
use App\Http\Controllers\PermissionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public hotel routes (no authentication required)


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Admin & Staff Area
    Route::middleware('role:root,admin,staff')->prefix('admin')->group(function () {
        // Staff Management (Only Root/Admin can manage staff)
        Route::middleware('role:root,admin')->group(function () {
            Route::apiResource('staff', \App\Http\Controllers\StaffController::class);
        });

        // Hotel Management (Admin can manage all hotels)

        Route::get('/hotels', [HotelController::class, 'index']);
        Route::get('/hotels/{id}', [HotelController::class, 'show']);
        Route::get('/hotels/{hotelId}/room-types', [RoomTypeController::class, 'getByHotel']);



        Route::post('/hotels', [HotelController::class, 'store']);
        Route::put('/hotels/{id}', [HotelController::class, 'update']);
        Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);

        // Room Type Management

        // Public room type routes
        Route::get('/room-types', [RoomTypeController::class, 'index']);
        Route::get('/room-types/{id}', [RoomTypeController::class, 'show']);
        Route::get('/room-types/{roomTypeId}/rooms', [RoomController::class, 'getByRoomType']);

        // Public room routes
        Route::get('/rooms', [RoomController::class, 'index']);
        Route::get('/rooms/{id}', [RoomController::class, 'show']);



        Route::post('/room-types', [RoomTypeController::class, 'store']);
        Route::put('/room-types/{id}', [RoomTypeController::class, 'update']);
        Route::delete('/room-types/{id}', [RoomTypeController::class, 'destroy']);

        // Room Management
        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{id}', [RoomController::class, 'update']);
        Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);
        Route::patch('/rooms/{id}/status', [RoomController::class, 'updateStatus']);

        // Department Management
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::put('/departments/{id}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
        Route::patch('/departments/{id}/manager', [DepartmentController::class, 'assignManager']);

        // Position Management

        // Public staff management routes
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/departments/{id}', [DepartmentController::class, 'show']);
        Route::get('/hotels/{hotelId}/departments', [DepartmentController::class, 'getByHotel']);

        Route::get('/positions', [PositionController::class, 'index']);
        Route::get('/positions/{id}', [PositionController::class, 'show']);

        Route::post('/positions', [PositionController::class, 'store']);
        Route::put('/positions/{id}', [PositionController::class, 'update']);
        Route::delete('/positions/{id}', [PositionController::class, 'destroy']);
        Route::post('/positions/{id}/permissions', [PositionController::class, 'assignPermissions']);

        // Staff Group Management
        Route::post('/staff-groups', [StaffGroupController::class, 'store']);
        Route::put('/staff-groups/{id}', [StaffGroupController::class, 'update']);
        Route::delete('/staff-groups/{id}', [StaffGroupController::class, 'destroy']);


        Route::get('/staff-groups', [StaffGroupController::class, 'index']);
        Route::get('/staff-groups/{id}', [StaffGroupController::class, 'show']);
        Route::get('/hotels/{hotelId}/staff-groups', [StaffGroupController::class, 'getByHotel']);

        Route::get('/staff', [StaffManagementController::class, 'index']);
        Route::get('/staff/{id}', [StaffManagementController::class, 'show']);
        Route::get('/hotels/{hotelId}/staff', [StaffManagementController::class, 'getByHotel']);
        Route::get('/departments/{departmentId}/staff', [StaffManagementController::class, 'getByDepartment']);

        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::get('/permissions/{id}', [PermissionController::class, 'show']);
        Route::get('/permissions/module/{module}', [PermissionController::class, 'getByModule']);


        // Staff Management (New comprehensive staff system)
        Route::post('/staff-management', [StaffManagementController::class, 'store']);
        Route::put('/staff-management/{id}', [StaffManagementController::class, 'update']);
        Route::delete('/staff-management/{id}', [StaffManagementController::class, 'destroy']);
        Route::post('/staff-management/{id}/permissions', [StaffManagementController::class, 'grantPermission']);
        Route::delete('/staff-management/{staffId}/permissions/{permissionId}', [StaffManagementController::class, 'revokePermission']);

        // Permission Management
        Route::post('/permissions', [PermissionController::class, 'store']);
        Route::put('/permissions/{id}', [PermissionController::class, 'update']);
        Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);
    });

    // Customer Area
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        // Customer hotel management (customers can manage their own hotels)
        Route::get('/my-hotels', [HotelController::class, 'myHotels']);
        Route::post('/hotels', [HotelController::class, 'store']);
        Route::put('/hotels/{id}', [HotelController::class, 'update']);
        Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);

        // Customer room type management
        Route::post('/room-types', [RoomTypeController::class, 'store']);
        Route::put('/room-types/{id}', [RoomTypeController::class, 'update']);
        Route::delete('/room-types/{id}', [RoomTypeController::class, 'destroy']);

        // Customer room management
        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{id}', [RoomController::class, 'update']);
        Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);
        Route::patch('/rooms/{id}/status', [RoomController::class, 'updateStatus']);
    });
});
