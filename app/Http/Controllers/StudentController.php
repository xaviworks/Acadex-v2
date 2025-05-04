<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 🎓 List Students in a Subject
    public function index(Request $request)
    {
        Gate::authorize('instructor');

        $subjects = Subject::where('instructor_id', Auth::id())->get();
        $students = [];

        if ($request->filled('subject_id')) {
            $students = Student::whereHas('subjects', function ($query) use ($request) {
                    $query->where('subject_id', $request->subject_id);
                })
                ->where('is_deleted', false)
                ->get();
        }

        return view('instructor.manage-students', compact('subjects', 'students'));
    }

    // ➕ Show Enrollment Form
    public function create()
    {
        Gate::authorize('instructor');

        $courses = Course::where('department_id', Auth::user()->department_id)->get();
        $subjects = Subject::where('instructor_id', Auth::id())->get();

        return view('instructor.add-student', compact('subjects', 'courses'));
    }

    // 💾 Enroll Student
    public function store(Request $request)
    {
        Gate::authorize('instructor');

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'year_level' => 'required|integer|min:1|max:5',
            'subject_id' => 'required|exists:subjects,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $subject = Subject::findOrFail($request->subject_id);

        $student = Student::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'year_level' => $request->year_level,
            'department_id' => Auth::user()->department_id,
            'course_id' => $request->course_id,
            'academic_period_id' => $subject->academic_period_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        StudentSubject::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
        ]);

        return redirect()->route('instructor.students.index')->with('success', 'Student enrolled successfully.');
    }

    // 🗑 Drop Student from a Subject
    public function drop(Request $request, $studentId)
    {
        Gate::authorize('instructor');

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        StudentSubject::where('student_id', $studentId)
            ->where('subject_id', $request->subject_id)
            ->delete();

        return redirect()->back()->with('success', 'Student dropped from subject.');
    }
}
