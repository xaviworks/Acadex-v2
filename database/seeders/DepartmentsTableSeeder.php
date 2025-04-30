<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        Department::create([
            'department_code' => 'ASBM',
            'department_description' => 'Arts, Science, and Business Management',
        ]);

        Department::create([
            'department_code' => 'N',
            'department_description' => 'Nursing Dept',
        ]);
    }
}
