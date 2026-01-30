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

    // 3. Clear cache trước khi migrate
    try {
        $outputs = [];
        
        // Clear route cache
        Artisan::call('route:clear');
        $outputs['route_clear'] = Artisan::output();
        
        // Clear config cache
        Artisan::call('config:clear');
        $outputs['config_clear'] = Artisan::output();
        
        // Clear application cache
        Artisan::call('cache:clear');
        $outputs['cache_clear'] = Artisan::output();
        
        // 4. Chạy migrate (sẽ chỉ chạy các migration chưa được thực hiện)
        Artisan::call('migrate', [
            '--force' => true
        ]);
        $outputs['migrate'] = Artisan::output();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Migration và clear cache thành công!',
            'outputs' => $outputs
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'FAILED',
            'error' => $e->getMessage()
        ], 500);
    }

});

// Test simple user creation without factory
Route::get('/test-simple-user', function () {
    try {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test_' . time() . '@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'phone' => '123456789',
            'address' => 'Test Address',
            'role' => 'staff',
            'status' => true,
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'User created successfully without factory!',
            'user' => $user
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Test factory và seeder
Route::get('/test-factory', function () {
    try {
        // Xóa users cũ (trừ admin nếu có)
        DB::table('users')->where('email', '!=', 'admin@rimhotel.com')
            ->where('role', '!=', 'root') // Không xóa root
            ->delete();
        
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
        
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'FAILED',
            'message' => 'Không thể tạo dữ liệu mẫu',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Tạo tài khoản root
Route::get('/create-root', function () {
    try {
        $email = 'rimhotlroot@gmail.com';
        $password = '123123';

        // Kiểm tra xem đã tồn tại chưa
        $user = \App\Models\User::where('email', $email)->first();

        if ($user) {
            // Nếu đã tồn tại, cập nhật password và role
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'role' => 'root',
                'status' => true
            ]);
            $message = 'Tài khoản root đã tồn tại và đã được cập nhật mật khẩu!';
        } else {
            // Nếu chưa có, tạo mới
            $user = \App\Models\User::create([
                'name' => 'Root Admin',
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'role' => 'root',
                'status' => true,
                'phone' => '0999999999',
                'address' => 'System root',
            ]);
            $message = 'Đã tạo tài khoản root thành công!';
        }

        return response()->json([
            'status' => 'SUCCESS',
            'message' => $message,
            'user' => [
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ]
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});