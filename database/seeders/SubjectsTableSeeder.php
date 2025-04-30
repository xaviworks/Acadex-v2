<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\AcademicPeriod;

class SubjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        $period = AcademicPeriod::first();

        Subject::create([
            'subject_code' => 'IT101',
            'subject_description' => 'Introduction to Computing',
            'is_universal' => false,
            'academic_period_id' => $period->id,
        ]);

        Subject::create([
            'subject_code' => 'GE101',
            'subject_description' => 'English Grammar and Composition',
            'is_universal' => true,
            'academic_period_id' => $period->id,
        ]);

        Subject::create([
            'subject_code' => 'IT102',
            'subject_description' => 'Computer Programming 1',
            'is_universal' => false,
            'academic_period_id' => $period->id,
        ]);
    }
}
