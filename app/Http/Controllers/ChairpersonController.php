<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use App\Models\UnverifiedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
            ->where('course_id', Auth::user()->course_id)
            ->orderBy('last_name')
            ->get();

        $pendingAccounts = UnverifiedUser::with('department', 'course')
            ->where('department_id', Auth::user()->department_id)
            ->where('course_id', Auth::user()->course_id)
            ->get();

        return view('chairperson.manage-instructors', compact('instructors', 'pendingAccounts'));
    }

    public function storeInstructor(Request $request)
    {
        Gate::authorize('chairperson');

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|string|regex:/^[^@]+$/|unique:unverified_users,email|max:255',
            'password'      => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'department_id' => 'required|exists:departments,id',
            'course_id'     => 'required|exists:courses,id',
        ]);

        $fullEmail = strtolower(trim($request->email)) . '@brokenshire.edu.ph';

        UnverifiedUser::create([
            'first_name'    => $request->first_name,
            'middle_name'   => $request->middle_name,
            'last_name'     => $request->last_name,
            'email'         => $fullEmail,
            'password'      => Hash::make($request->password),
            'department_id' => $request->department_id,
            'course_id'     => $request->course_id,
        ]);

        return redirect()->back()->with('status', 'Instructor account submitted for approval.');
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

    public function activateInstructor($id)
    {
        Gate::authorize('chairperson');

        $instructor = User::where('id', $id)
            ->where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->firstOrFail();

        $instructor->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Instructor activated successfully.');
    }

    // ============================
    // Subject Assignment
    // ============================

    public function assignSubjects()
    {
        Gate::authorize('chairperson');
    
        $academicPeriodId = session('active_academic_period_id');
    
        // Fetch subjects filtered by department, course, academic period, and not deleted
        $subjects = Subject::where('department_id', Auth::user()->department_id)
            ->where('course_id', Auth::user()->course_id)
            ->where('is_deleted', false)
            ->where('academic_period_id', $academicPeriodId)
            ->orderBy('subject_code')
            ->get();
    
        // Group subjects by year_level
        $yearLevels = $subjects->groupBy('year_level');
    
        // Fetch active instructors in the department and course
        $instructors = User::where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->where('course_id', Auth::user()->course_id)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();
    
        // Return view with grouped subjects by year level and instructors
        return view('chairperson.assign-subjects', compact('yearLevels', 'instructors'));
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
            ->where('course_id', Auth::user()->course_id)
            ->where('academic_period_id', $academicPeriodId)
            ->firstOrFail();

        $instructor = User::where('id', $request->instructor_id)
            ->where('role', 0)
            ->where('department_id', Auth::user()->department_id)
            ->where('course_id', Auth::user()->course_id)
            ->where('is_active', true)
            ->firstOrFail();

        $subject->update([
            'instructor_id' => $instructor->id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('chairperson.assignSubjects')->with('success', 'Subject assigned successfully.');
    }
    public function toggleAssignedSubject(Request $request)
    {
        Gate::authorize('chairperson');
        
        $academicPeriodId = session('active_academic_period_id');
        
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'instructor_id' => 'nullable|exists:users,id', // instructor_id is nullable for unassign
        ]);
    
        $subject = Subject::where('id', $request->subject_id)
            ->where('department_id', Auth::user()->department_id)
            ->where('course_id', Auth::user()->course_id)
            ->where('academic_period_id', $academicPeriodId)
            ->firstOrFail();
    
        // Check if there are any enrolled students in the subject
        $enrolledStudents = $subject->students()->count(); // Assuming the 'students' relationship is defined
    
        // If there are enrolled students and we are trying to unassign the subject, prevent the action
        if ($enrolledStudents > 0 && !$request->instructor_id) {
            return redirect()->route('chairperson.assignSubjects')->with('error', 'Cannot unassign subject as it has enrolled students.');
        }
    
        // If an instructor is selected, assign them, otherwise unassign the instructor
        if ($request->instructor_id) {
            $instructor = User::where('id', $request->instructor_id)
                ->where('role', 0) // Ensure the user is an instructor
                ->where('department_id', Auth::user()->department_id)
                ->where('course_id', Auth::user()->course_id)
                ->where('is_active', true)
                ->firstOrFail();
    
            $subject->update([
                'instructor_id' => $instructor->id,
                'updated_by' => Auth::id(),
            ]);
    
            return redirect()->route('chairperson.assignSubjects')->with('success', 'Instructor assigned successfully.');
        } else {
            // Unassign the instructor only if no students are enrolled
            $subject->update([
                'instructor_id' => null,
                'updated_by' => Auth::id(),
            ]);
    
            return redirect()->route('chairperson.assignSubjects')->with('success', 'Instructor unassigned successfully.');
        }
    }
        
    
    // ============================
    // View Grades
    // ============================

    public function viewGrades(Request $request)
    {
        Gate::authorize('chairperson');
        
        $selectedInstructorId = $request->input('instructor_id');
        $selectedSubjectId = $request->input('subject_id');
        
        $academicPeriodId = session('active_academic_period_id');
        $departmentId = Auth::user()->department_id;
        $courseId = Auth::user()->course_id;
        
        // Fetch instructors in department and course (role: 0 = instructor)
        $instructors = User::where([
            ['role', 0],
            ['department_id', $departmentId],
            ['course_id', $courseId],
            ['is_active', true],
        ])
        ->orderBy('last_name')
        ->get();
    
        // Subjects are loaded only when an instructor is selected
        $subjects = [];
        if ($selectedInstructorId) {
            $subjects = Subject::where([
                ['instructor_id', $selectedInstructorId],
                ['department_id', $departmentId],
                ['course_id', $courseId],
                ['academic_period_id', $academicPeriodId],
                ['is_deleted', false],
            ])
            ->orderBy('subject_code')
            ->get();
        }
    
        // Students and grades are only loaded when a subject is selected
        $students = [];
        if ($selectedSubjectId) {
            // Get the subject and the students enrolled in it
            $subject = Subject::where([
                ['id', $selectedSubjectId],
                ['department_id', $departmentId],
                ['course_id', $courseId],
            ])
            ->firstOrFail();
    
            $students = $subject->students()
                ->with(['termGrades' => function ($q) use ($selectedSubjectId) {
                    $q->where('subject_id', $selectedSubjectId);
                }])
                ->get();
        }
    
        return view('chairperson.view-grades', [
            'instructors' => $instructors,
            'subjects' => $subjects,
            'students' => $students,
            'selectedInstructorId' => $selectedInstructorId,
            'selectedSubjectId' => $selectedSubjectId,
        ]);
    }
    
      

    // ============================
    // Students by Year Level
    // ============================

    public function viewStudentsPerYear()
    {
        Gate::authorize('chairperson');

        $students = Student::where('department_id', Auth::user()->department_id)
            ->where('course_id', Auth::user()->course_id)
            ->where('is_deleted', false)
            ->with('course')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('chairperson.students-by-year', compact('students'));
    }
}