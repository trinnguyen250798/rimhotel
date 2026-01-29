<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Root Administrator',
            'email' => 'root@rimhotel.com',
            'password' => bcrypt('password'), // Change this in production!
            'role' => User::ROLE_ROOT,
            'status' => true,
        ]);
    }
}
