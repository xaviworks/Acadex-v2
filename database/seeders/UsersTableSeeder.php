<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Default password
            'role' => 3, 
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'), // Default password
            'role' => 0, 
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Chairperson User',
            'email' => 'chairperson@example.com',
            'password' => Hash::make('password'), // Default password
            'role' => 1, 
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dean User',
            'email' => 'dean@example.com',
            'password' => Hash::make('password'), // Default password
            'role' => 2, 
            'is_active' => true,
        ]);
    }
}
