<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AcademicPeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List Academic Periods
    public function index()
    {
        Gate::authorize('admin');

        $periods = AcademicPeriod::orderBy('created_at', 'desc')->get();
        return view('admin.academic-periods.index', compact('periods'));
    }

    // Auto Generate New Academic Periods
    public function generate()
    {
        Gate::authorize('admin');

        // Find latest academic period
        $latest = AcademicPeriod::orderBy('created_at', 'desc')->first();
        $currentYear = now()->year;

        // If latest exists and academic_year is "2025-2026", next should be "2026-2027"
        if ($latest && $latest->academic_year) {
            $years = explode('-', $latest->academic_year);
            if (count($years) == 2) {
                $startYear = intval($years[0]) + 1;
                $endYear = intval($years[1]) + 1;
            } else {
                // fallback
                $startYear = $currentYear;
                $endYear = $currentYear + 1;
            }
        } else {
            $startYear = $currentYear;
            $endYear = $currentYear + 1;
        }

        $newAcademicYear = "{$startYear}-{$endYear}";

        // Check if already exists
        if (!AcademicPeriod::where('academic_year', $newAcademicYear)->exists()) {

            // Create 1st Semester
            AcademicPeriod::create([
                'academic_year' => $newAcademicYear,
                'semester' => '1st',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create 2nd Semester
            AcademicPeriod::create([
                'academic_year' => $newAcademicYear,
                'semester' => '2nd',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create Summer (Year only, no range)
            AcademicPeriod::create([
                'academic_year' => $startYear,
                'semester' => 'Summer',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return redirect()->route('admin.academicPeriods')->with('success', 'New academic periods generated successfully.');
    }
}
