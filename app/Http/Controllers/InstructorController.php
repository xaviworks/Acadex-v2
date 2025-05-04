<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Optional: Instructor dashboard or summary page
    public function dashboard()
    {
        Gate::authorize('instructor');
        $instructor = Auth::user();

        return view('instructor.dashboard', compact('instructor'));
    }

    // Shared helper (if still needed across controllers)
    public static function getTermId($term)
    {
        return [
            'prelim' => 1,
            'midterm' => 2,
            'prefinal' => 3,
            'final' => 4,
        ][$term] ?? null;
    }
}
