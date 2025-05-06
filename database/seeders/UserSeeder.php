<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch Department and Course dynamically
        $department = Department::where('department_code', 'ASBM')->first();
        $course = Course::where('course_code', 'BSBA')->first();

        $users = [
            [
                'first_name' => 'Admin',
                'middle_name' => null,
                'last_name' => 'User',
                'email' => 'admin@brokenshire.edu.ph',
                'role' => 3,
            ],
            [
                'first_name' => 'Chairperson',
                'middle_name' => null,
                'last_name' => 'User',
                'email' => 'chairperson@brokenshire.edu.ph',
                'role' => 1,
            ],
            [
                'first_name' => 'Dean',
                'middle_name' => null,
                'last_name' => 'User',
                'email' => 'dean@brokenshire.edu.ph',
                'role' => 2,
            ],
            [
                'first_name' => 'Instructor',
                'middle_name' => null,
                'last_name' => 'User',
                'email' => 'instructor@brokenshire.edu.ph',
                'role' => 0,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'department_id' => $department?->id ?? 1,
                    'course_id' => $course?->id ?? 1,
                ])
            );
        }
    }
}
