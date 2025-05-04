<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FinalGrade;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DeanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================
    // View Instructors under Dean
    // ============================

    public function viewInstructors()
    {
        Gate::authorize('dean');

        $instructors = User::where('role', 0) // Instructor role
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dean.instructors', compact('instructors'));
    }

    // ============================
    // View Students under Dean
    // ============================

    public function viewStudents()
    {
        Gate::authorize('dean');

        $students = Student::with('course')
            ->where('department_id', Auth::user()->department_id)
            ->where('is_deleted', false)
            ->orderBy('last_name')
            ->get();

        return view('dean.students', compact('students'));
    }

    // ============================
    // View Final Grades by Course
    // ============================

    public function viewGrades(Request $request)
    {
        Gate::authorize('dean');

        $departmentId = Auth::user()->department_id;

        // List of courses in dean's department
        $courses = Course::where('department_id', $departmentId)
            ->where('is_deleted', false)
            ->orderBy('course_code')
            ->get();

        $students = collect();
        $finalGrades = collect();

        if ($request->filled('course_id')) {
            $courseId = $request->input('course_id');

            $students = Student::where('department_id', $departmentId)
                ->where('course_id', $courseId)
                ->where('is_deleted', false)
                ->orderBy('last_name')
                ->get();

            $finalGrades = FinalGrade::with(['student', 'subject'])
                ->whereIn('student_id', $students->pluck('id'))
                ->get();
        }

        return view('dean.grades', compact('courses', 'students', 'finalGrades'));
    }
}
