<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php
Route::get('/_load_migrate_', function () {

    // 1. Chỉ cho chạy trên production
    if (!app()->environment('production')) {
        abort(403, 'Not allowed environment');
    }

    // 2. Check secret key
    if (request('key') !== env('MIGRATE_KEY')) {
        abort(403, 'Invalid key');
    }

    // 3. Kiểm tra DB đã migrate chưa
    try {
        if (DB::table('migrations')->count() > 0) {
            return response()->json([
                'status' => 'SKIPPED',
                'message' => 'Already migrated'
            ]);
        }
    } catch (\Throwable $e) {
        // Chưa có bảng migrations → OK, tiếp tục
    }

    // 4. Chạy migrate
    try {
        Artisan::call('migrate', [
            '--force' => true
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'output' => Artisan::output()
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'FAILED',
            'error' => $e->getMessage()
        ], 500);
    }

});