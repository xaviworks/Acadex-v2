<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Instructor dashboard
    public function dashboard()
    {
        Gate::authorize('instructor');
        $instructor = Auth::user();

        return view('instructor.dashboard', compact('instructor'));
    }

    // Manage Students Page
    public function index(Request $request)
    {
        Gate::authorize('instructor');

        $academicPeriodId = session('active_academic_period_id');

        $subjects = Subject::where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->where('academic_period_id', $academicPeriodId)
            ->get();

        $courses = Course::all();

        $students = collect();

        if ($request->filled('subject_id')) {
            $subject = Subject::findOrFail($request->subject_id);

            if ($subject->instructor_id !== Auth::id()) {
                abort(403, 'Unauthorized access to subject.');
            }

            $students = $subject->students()
                ->where('students.is_deleted', 0)
                ->get();

        }

        return view('instructor.manage-students', compact('subjects', 'students', 'courses'));
    }

    // Shared helper for term mapping
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
