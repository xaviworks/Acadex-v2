<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ChairpersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================
    // Instructor Management
    // ============================

    public function manageInstructors()
    {
        Gate::authorize('chairperson');

        $instructors = User::where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->orderBy('name')
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 0, // Instructor
            'department_id' => Auth::user()->department_id,
            'course_id' => Auth::user()->course_id,
            'is_active' => true,
        ]);

        return redirect()->route('chairperson.instructors')->with('success', 'Instructor created successfully.');
    }

    public function deactivateInstructor($id)
    {
        Gate::authorize('chairperson');

        $instructor = User::where('id', $id)
            ->where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->firstOrFail();

        $instructor->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Instructor deactivated successfully.');
    }

    // ============================
    // Subject Assignment
    // ============================

    public function assignSubjects()
    {
        Gate::authorize('chairperson');
    
        $academicPeriodId = session('active_academic_period_id');
    
        $subjects = Subject::where('department_id', Auth::user()->department_id)
            ->where('is_deleted', false)
            ->where('academic_period_id', $academicPeriodId)
            ->orderBy('subject_code')
            ->get();
    
        $instructors = User::where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    
        return view('chairperson.assign-subjects', compact('subjects', 'instructors'));
    }
    
    public function storeAssignedSubject(Request $request)
    {
        Gate::authorize('chairperson');
    
        $academicPeriodId = session('active_academic_period_id');
    
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'instructor_id' => 'required|exists:users,id',
        ]);
    
        $subject = Subject::where('id', $request->subject_id)
            ->where('department_id', Auth::user()->department_id)
            ->where('academic_period_id', $academicPeriodId)
            ->firstOrFail();
    
        $instructor = User::where('id', $request->instructor_id)
            ->where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->firstOrFail();
    
        $subject->update([
            'instructor_id' => $instructor->id,
            'updated_by' => Auth::id(),
        ]);
    
        return redirect()->route('chairperson.assignSubjects')->with('success', 'Subject assigned successfully.');
    }    

    // ============================
    // View Grades
    // ============================

    public function viewGrades(Request $request)
    {
        Gate::authorize('chairperson');
    
        $selectedYear = $request->input('year_level');
        $selectedPeriodId = $request->input('academic_period_id');
    
        $students = Student::with(['subjects', 'termGrades', 'course'])
            ->where('department_id', Auth::user()->department_id)
            ->when(Auth::user()->course_id, fn($q) => $q->where('course_id', Auth::user()->course_id))
            ->when($selectedYear, fn($q) => $q->where('year_level', $selectedYear))
            ->when($selectedPeriodId, fn($q) => $q->where('academic_period_id', $selectedPeriodId))
            ->where('is_deleted', false)
            ->orderBy('last_name')
            ->get();
    
        $academicPeriods = \App\Models\AcademicPeriod::where('is_deleted', false)
            ->orderByDesc('academic_year')
            ->orderByRaw("FIELD(semester, '1st', '2nd', 'Summer')")
            ->get();
    
        return view('chairperson.view-grades', [
            'students' => $students,
            'selectedYear' => $selectedYear,
            'academicPeriods' => $academicPeriods,
        ]);
    }
    

    // ============================
    // Students by Year Level
    // ============================

    public function viewStudentsPerYear()
    {
        Gate::authorize('chairperson');

        $students = Student::with('course')
            ->where('department_id', Auth::user()->department_id)
            ->when(Auth::user()->course_id, fn($q) => $q->where('course_id', Auth::user()->course_id))
            ->where('is_deleted', false)
            ->orderBy('year_level')
            ->orderBy('last_name')
            ->get();

        return view('chairperson.students-by-year', compact('students'));
    }
}
