<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', function () {
    return response()->json([
        'message' => 'API Login requires POST method. Please use Postman or a frontend application to login.',
        'help' => 'Send a POST request to this URL with email and password fields.'
    ], 405);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Staff Management
    Route::middleware('role:root,admin')->group(function () {
        Route::apiResource('staff', \App\Http\Controllers\StaffController::class);
    });
});
