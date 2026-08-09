<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@perpustakaan.local',
                'password' => 'admin123',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'pustakawan'],
            [
                'name' => 'Pustakawan Utama',
                'email' => 'pustakawan@perpustakaan.local',
                'password' => 'pustaka123',
                'role' => 'pustakawan',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'viewer'],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@perpustakaan.local',
                'password' => 'viewer123',
                'role' => 'viewer',
                'is_active' => true,
            ]
        );
    }
}
