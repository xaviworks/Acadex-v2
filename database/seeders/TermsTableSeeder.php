<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Term;

class TermsTableSeeder extends Seeder
{
    public function run(): void
    {
        $terms = ['Prelim', 'Midterm', 'Prefinal', 'Final'];

        foreach ($terms as $term) {
            Term::create([
                'term_name' => $term,
            ]);
        }
    }
}
