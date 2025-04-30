<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\TermGrade;
use App\Models\FinalGrade;

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

        $instructors = User::where('role', 0)  // Instructor role
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->get();

        return view('dean.instructors', compact('instructors'));
    }

    // ============================
    // View Students under Dean
    // ============================

    public function viewStudents()
    {
        Gate::authorize('dean');

        $students = Student::with('course')  // Eager load the course relationship
            ->where('department_id', Auth::user()->department_id)
            ->where('is_deleted', false)
            ->get();

        return view('dean.students', compact('students'));
    }

    // ============================
    // View Final Grades under Dean
    // ============================

    public function viewGrades(Request $request)
    {
        Gate::authorize('dean');

        $departmentId = Auth::user()->department_id;

        // Fetch courses for the department
        $courses = Course::where('department_id', $departmentId)
            ->where('is_deleted', false)
            ->get();

        $students = collect();
        $finalGrades = collect();

        if ($request->filled('course_id')) {
            // Fetch students filtered by course_id
            $students = Student::where('department_id', $departmentId)
                ->where('course_id', $request->course_id)
                ->where('is_deleted', false)
                ->get();

            // Fetch final grades for the selected students
            $finalGrades = FinalGrade::whereIn('student_id', $students->pluck('id'))
                ->with('student', 'subject')  // Eager load student and subject
                ->get();
        }

        return view('dean.grades', compact('courses', 'students', 'finalGrades'));
    }
}
