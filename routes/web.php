<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

// Test database connection
Route::get('/test-db', function () {
    try {
        // Test connection
        DB::connection()->getPdo();
        
        // Get table columns info
        $columns = DB::select("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns
            WHERE table_name = 'users'
            ORDER BY ordinal_position
        ");
        
        // Get users count
        $usersCount = DB::table('users')->count();
        
        // Get sample users
        $users = DB::table('users')->limit(5)->get();
        
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Database connected successfully!',
            'database' => env('DB_DATABASE'),
            'connection' => env('DB_CONNECTION'),
            'users_count' => $usersCount,
            'table_structure' => $columns,
            'sample_users' => $users
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'FAILED',
            'message' => 'Database connection failed',
            'error' => $e->getMessage()
        ], 500);
    }
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

    // 3. Chạy migrate (sẽ chỉ chạy các migration chưa được thực hiện)
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

// Test factory và seeder
Route::get('/test-factory', function () {
    try {
        // Xóa users cũ (trừ admin nếu có)
        DB::table('users')->where('email', '!=', 'admin@rimhotel.com')->delete();
        
        // Tạo 10 users mẫu bằng factory
        $users = \App\Models\User::factory()->count(10)->create();
        
        // Lấy tất cả users
        $allUsers = DB::table('users')->get();
        
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Factory đã tạo thành công!',
            'created_count' => $users->count(),
            'total_users' => $allUsers->count(),
            'sample_users' => $allUsers->take(5)
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'FAILED',
            'message' => 'Không thể tạo dữ liệu mẫu',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});