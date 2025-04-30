<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 3, // Admin
            'is_universal' => true,
        ]);

        // Chairperson / Program Head
        User::create([
            'name' => 'Chairperson User',
            'email' => 'chairperson@example.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 1, // Chairperson
            'is_universal' => false,
            'department_id' => 1, // Example department
            'course_id' => 1,      // Example course
        ]);

        // Dean User
        User::create([
            'name' => 'Dean User',
            'email' => 'dean@example.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 2, // Dean
            'is_universal' => false,
            'department_id' => 1, // Example department
            'course_id' => 1,      // Example course
        ]);

        // Instructor User
        User::create([
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 0, // Instructor
            'is_universal' => false, // set true if General Education instructor
            'department_id' => 1, // Example department
            'course_id' => 1,      // Example course
        ]);
    }
}
