<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
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

        // Add more admin-only routes here (rooms management, etc.)
    });

    // Customer Area
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        // Add customer-specific routes here (booking history, etc.)
    });
});
