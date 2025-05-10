<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicPeriod;

class AcademicPeriodsTableSeeder extends Seeder
{
    public function run(): void
    {
        AcademicPeriod::create([
            'academic_year' => '2025-2026',
            'semester' => '1st',
        ]);

        AcademicPeriod::create([
            'academic_year' => '2025-2026',
            'semester' => '2nd',
        ]);

        AcademicPeriod::create([
            'academic_year' => '2025',
            'semester' => 'Summer',
        ]);
    }
}
