<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;

class CoursesTableSeeder extends Seeder
{
    public function run(): void
    {
        $bsit = Department::where('department_code', 'BSIT')->first();
        $bsba = Department::where('department_code', 'BSBA')->first();

        Course::create([
            'course_code' => 'BSIT',
            'course_description' => 'Bachelor of Science in Information Technology',
            'department_id' => $bsit->id,
        ]);

        Course::create([
            'course_code' => 'BSBA',
            'course_description' => 'Bachelor of Science in Business Administration',
            'department_id' => $bsba->id,
        ]);
    }
}
