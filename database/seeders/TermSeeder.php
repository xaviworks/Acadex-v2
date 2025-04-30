<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Term;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = ['Prelim', 'Midterm', 'Prefinal', 'Final'];

        foreach ($terms as $term) {
            Term::create([
                'name' => $term,
                'is_deleted' => false,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
