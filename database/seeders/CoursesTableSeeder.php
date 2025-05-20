<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;

class CoursesTableSeeder extends Seeder
{
    public function run(): void
    {
        $asbm = Department::where('department_code', 'ASBM')->first();
        $nursing = Department::where('department_code', 'NURSING')->first();
        $medicine = Department::where('department_code', 'MEDICINE')->first();
        $allied = Department::where('department_code', 'ALLIED')->first();

        if (!$asbm || !$nursing || !$medicine || !$allied) {
            throw new \Exception('Required departments not found. Seed departments first.');
        }

        // ASBM Courses
        Course::create([
            'course_code' => 'BSIT',
            'course_description' => 'Bachelor of Science in Information Technology',
            'department_id' => $asbm->id,
        ]);

        Course::create([
            'course_code' => 'BSBA',
            'course_description' => 'Bachelor of Science in Business Administration',
            'department_id' => $asbm->id,
        ]);

        Course::create([
            'course_code' => 'BSPSY',
            'course_description' => 'Bachelor of Science in Psychology',
            'department_id' => $asbm->id,
        ]);

        // Nursing Course
        Course::create([
            'course_code' => 'BSN',
            'course_description' => 'Bachelor of Science in Nursing',
            'department_id' => $nursing->id,
        ]);

        // Medicine Course
        Course::create([
            'course_code' => 'MED',
            'course_description' => 'Doctor of Medicine',
            'department_id' => $medicine->id,
        ]);

        // Allied Health Courses
        Course::create([
            'course_code' => 'BSPHARM',
            'course_description' => 'Bachelor of Science in Pharmacy',
            'department_id' => $allied->id,
        ]);

        Course::create([
            'course_code' => 'BSMLS',
            'course_description' => 'Bachelor of Science in Medical Laboratory Science',
            'department_id' => $allied->id,
        ]);
    }
}
