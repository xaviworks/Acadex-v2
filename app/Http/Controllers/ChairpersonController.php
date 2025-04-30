<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Student;

class ChairpersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================
    // Manage Instructors
    // ============================

    public function manageInstructors()
    {
        Gate::authorize('chairperson');

        $instructors = User::where('role', 0) // Instructor role
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->get();

        return view('chairperson.manage-instructors', compact('instructors'));
    }

    public function createInstructor()
    {
        Gate::authorize('chairperson');

        return view('chairperson.create-instructor');
    }

    public function storeInstructor(Request $request)
    {
        Gate::authorize('chairperson');
    
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        // Automatically set the department and course based on the chairperson's data
        $department_id = Auth::user()->department_id;
        $course_id = Auth::user()->course_id;
    
        // Create the instructor with the department and course set automatically from the chairperson
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 0, // Instructor role
            'department_id' => $department_id,  // Automatically assign department of the chairperson
            'course_id' => $course_id,  // Automatically assign course of the chairperson
            'is_active' => true,
        ]);
    
        // Redirect back to the instructors page with a success message
        return redirect()->route('chairperson.instructors')->with('success', 'Instructor created successfully.');
    }    

    public function deactivateInstructor($id)
    {
        Gate::authorize('chairperson');

        $instructor = User::where('id', $id)
            ->where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->firstOrFail();

        $instructor->update([
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', 'Instructor deactivated successfully.');
    }

    // ============================
    // Assign Subjects
    // ============================

    public function assignSubjects()
    {
        Gate::authorize('chairperson');

        $subjects = Subject::where('department_id', Auth::user()->department_id)
            ->where('is_deleted', false)
            ->get();

        $instructors = User::where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->get();

        return view('chairperson.assign-subjects', compact('subjects', 'instructors'));
    }

    public function storeAssignedSubject(Request $request)
    {
        Gate::authorize('chairperson');

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'instructor_id' => 'required|exists:users,id',
        ]);

        $subject = Subject::where('id', $request->subject_id)
            ->where('department_id', Auth::user()->department_id)
            ->firstOrFail();

        $subject->update([
            'instructor_id' => $request->instructor_id,
        ]);

        return redirect()->route('chairperson.assignSubjects')->with('success', 'Subject assigned successfully.');
    }

    // ============================
    // View Grades - Chairperson
    // ============================

    public function viewGrades(Request $request)
    {
        Gate::authorize('chairperson');

        $selectedYear = $request->input('year_level');

        $students = Student::with(['subjects', 'termGrades', 'course']) // <- include course now
            ->where('department_id', Auth::user()->department_id)
            ->when(Auth::user()->course_id, function ($query) {
                $query->where('course_id', Auth::user()->course_id);
            })
            ->when($selectedYear, function ($query) use ($selectedYear) {
                $query->where('year_level', $selectedYear);
            })
            ->where('is_deleted', false)
            ->orderBy('last_name')
            ->get();

        return view('chairperson.view-grades', compact('students', 'selectedYear'));
    }

    public function viewStudentsPerYear()
    {
        Gate::authorize('chairperson');

        $students = Student::with('course')
            ->where('department_id', Auth::user()->department_id)
            ->when(Auth::user()->course_id, function ($query) {
                $query->where('course_id', Auth::user()->course_id);
            })
            ->where('is_deleted', false)
            ->orderBy('year_level')
            ->orderBy('last_name')
            ->get();

        return view('chairperson.students-by-year', compact('students'));
    }
}
