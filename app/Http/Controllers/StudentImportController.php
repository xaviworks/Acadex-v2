<?php

namespace App\Http\Controllers;

use App\Models\ReviewStudent;
use App\Models\Student;
use App\Models\StudentSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentReviewImport;

class StudentImportController extends Controller
{
    /**
     * Show the upload form and review pending imported students.
     */
    public function showUploadForm()
    {
        $subjects = Subject::with('course')
            ->where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->get();

        $reviewStudents = ReviewStudent::with('course', 'subject')
            ->where('instructor_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('instructor.excel.import-students', compact('subjects', 'reviewStudents'));
    }

    /**
     * Handle Excel upload and store to review table.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file'       => 'required|file|mimes:xlsx,xls',
            'list_name'  => 'nullable|string|max:255',
        ]);

        $listName = $request->list_name ?? pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);

        Excel::import(
            new StudentReviewImport(null, $listName), // ✅ Only 2 arguments
            $request->file('file')
        );

        return redirect()->route('instructor.students.import')->with('status', '📥 Student list uploaded for review.');
    }

    /**
     * Confirm reviewed students and move to main students table.
     */
    public function confirmImport(Request $request)
    {
        $request->validate([
            'list_name'  => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        $listName = $request->list_name;

        $selectedIds = explode(',', $request->input('selected_student_ids'));

        $reviewStudents = ReviewStudent::where('instructor_id', Auth::id())
            ->where('list_name', $listName)
            ->whereIn('id', $selectedIds)
            ->get();

        foreach ($reviewStudents as $review) {
            $existing = Student::where('first_name', $review->first_name)
                ->where('last_name', $review->last_name)
                ->where('middle_name', $review->middle_name)
                ->where('year_level', $review->year_level)
                ->where('course_id', $review->course_id)
                ->where('academic_period_id', $subject->academic_period_id)
                ->first();

            if ($existing) {
                continue;
            }

            $student = Student::create([
                'first_name'         => $review->first_name,
                'middle_name'        => $review->middle_name,
                'last_name'          => $review->last_name,
                'year_level'         => $review->year_level,
                'course_id'          => $review->course_id,
                'department_id'      => Auth::user()->department_id,
                'academic_period_id' => $subject->academic_period_id,
                'created_by'         => Auth::id(),
                'updated_by'         => Auth::id(),
            ]);

            StudentSubject::create([
                'student_id' => $student->id,
                'subject_id' => $subject->id,
            ]);
        }

        ReviewStudent::where('instructor_id', Auth::id())
            ->whereIn('id', $selectedIds)
            ->delete();

        return redirect()->route('instructor.students.import')->with('status', '✅ Selected students successfully imported to the selected subject.');
    }

    /**
     * API: Get students enrolled in a specific subject (AJAX).
     */
    public function getSubjectStudents($subjectId)
    {
        $subject = Subject::with(['students' => function ($query) {
            $query->where('students.is_deleted', 0)->with('course');
        }])
        ->where('instructor_id', Auth::id())
        ->where('id', $subjectId)
        ->firstOrFail();

        $students = $subject->students->map(function ($student) {
            return [
                'full_name'   => $student->full_name,
                'course_code' => $student->course->course_code ?? 'N/A',
                'year_level'  => $student->formatted_year_level,
            ];
        });

        return response()->json($students);
    }
}
