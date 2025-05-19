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
use App\Models\Activity;

class StudentImportController extends Controller
{
    /**
     * Show the upload form and review pending imported students.
     */
    public function showUploadForm()
    {
        $academicPeriodId = session('active_academic_period_id');

        $subjects = Subject::with('course')
            ->where('instructor_id', Auth::id())
            ->where('academic_period_id', $academicPeriodId) // ✅ Filter by active period
            ->where('is_deleted', false)
            ->get();

        $reviewStudents = ReviewStudent::with('course', 'subject')
            ->where('instructor_id', Auth::id())
            ->orderByDesc('created_at')
            ->orderBy('is_confirmed')  // Show unconfirmed first
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

    // ✅ Check if list_name already exists
    $exists = ReviewStudent::where('list_name', $listName)
        ->where('instructor_id', Auth::id())
        ->exists();

    if ($exists) {
        return redirect()
            ->route('instructor.students.import')
            ->withErrors(['file' => "❌ A file with the name '{$listName}' already exists."]);
    }

    Excel::import(
        new StudentReviewImport(null, $listName),
        $request->file('file')
    );

    return redirect()
        ->route('instructor.students.import')
        ->with('status', '📥 Student list uploaded for review.');
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
    
        $academicPeriodId = session('active_academic_period_id');
        $subject = Subject::findOrFail($request->subject_id);
    
        // ✅ Check if selected subject belongs to current session period
        if ($subject->academic_period_id != $academicPeriodId) {
            return redirect()->back()->withErrors(['error' => '❌ Subject does not belong to the active academic period.']);
        }
    
        $listName = $request->list_name;
        $selectedIds = explode(',', $request->input('selected_student_ids'));
    
        $reviewStudents = ReviewStudent::where('instructor_id', Auth::id())
            ->where('list_name', $listName)
            ->whereIn('id', $selectedIds)
            ->get();
    
        foreach ($reviewStudents as $review) {
            // Check if a matching student already exists
            $existingStudent = Student::where('first_name', $review->first_name)
                ->where('last_name', $review->last_name)
                ->where('middle_name', $review->middle_name)
                ->where('year_level', $review->year_level)
                ->where('course_id', $review->course_id)
                ->where('academic_period_id', $subject->academic_period_id)
                ->first();
    
            if ($existingStudent) {
                // ✅ Check if already linked to this subject
                $alreadyEnrolled = StudentSubject::where('student_id', $existingStudent->id)
                    ->where('subject_id', $subject->id)
                    ->exists();
    
                if (!$alreadyEnrolled) {
                    StudentSubject::create([
                        'student_id' => $existingStudent->id,
                        'subject_id' => $subject->id,
                    ]);
                }
    
                continue; // Skip creating new Student
            }
    
            // Create new student
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
    
        // ✅ Check and create activities for all terms if missing
        $terms = ['prelim', 'midterm', 'prefinal', 'final'];
        foreach ($terms as $term) {
            $exists = Activity::where('subject_id', $subject->id)
                ->where('term', $term)
                ->exists();
    
            if (!$exists) {
                $defaultActivities = [];
                foreach (['quiz' => 3, 'ocr' => 3, 'exam' => 1] as $type => $count) {
                    for ($i = 1; $i <= $count; $i++) {
                        $defaultActivities[] = [
                            'subject_id' => $subject->id,
                            'term' => $term,
                            'type' => $type,
                            'title' => ucfirst($type) . " $i",
                            'number_of_items' => 100,
                            'is_deleted' => false,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                // Insert the default activities
                Activity::insert($defaultActivities);
            }
        }
    
                // Mark review students as confirmed instead of deleting them
        ReviewStudent::where('instructor_id', Auth::id())
            ->whereIn('id', $selectedIds)
            ->update(['is_confirmed' => true]);

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


