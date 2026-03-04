<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'phone' => '+966500000000',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        // Create sample users
        $users = [
            [
                'name' => 'أحمد محمد',
                'phone' => '+966501111111',
                'email' => 'ahmed@example.com',
                'password' => 'password123',
                'role' => 'user',
                'is_active' => true
            ],
            [
                'name' => 'سارة عبدالله',
                'phone' => '+966502222222',
                'email' => 'sara@example.com',
                'password' => 'password123',
                'role' => 'user',
                'is_active' => true
            ],
            [
                'name' => 'Mohamed Ali',
                'phone' => '+201000000000',
                'email' => 'mohamed@example.com',
                'password' => 'password123',
                'role' => 'user',
                'is_active' => true
            ],
            [
                'name' => 'Fatima Ahmed',
                'phone' => '+971500000000',
                'email' => 'fatima@example.com',
                'password' => 'password123',
                'role' => 'user',
                'is_active' => true
            ],
            [
                'name' => 'John Smith',
                'phone' => '+1234567890',
                'email' => 'john@example.com',
                'password' => 'password123',
                'role' => 'user',
                'is_active' => true
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['phone' => $userData['phone']],
                [
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                    'is_active' => $userData['is_active'],
                    'email_verified_at' => now()
                ]
            );
        }
    }
}
