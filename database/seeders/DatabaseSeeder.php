<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentsTableSeeder::class,
            CoursesTableSeeder::class,
            AcademicPeriodsTableSeeder::class,
            TermsTableSeeder::class,
            SubjectsTableSeeder::class,
            UserSeeder::class,
            CurriculumSeeder::class,
        ]);
    }
    
}
