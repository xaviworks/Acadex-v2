<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentSubject;
use App\Models\Activity;
use App\Models\Score;
use App\Models\TermGrade;
use App\Models\FinalGrade;
use App\Models\Course;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================
    // Manage Students
    // ============================

    public function manageStudents(Request $request)
    {
        $subjects = Subject::where('instructor_id', Auth::id())->get();
        $students = [];

        if ($request->filled('subject_id')) {
            $students = Student::whereHas('subjects', function ($query) use ($request) {
                $query->where('subject_id', $request->subject_id);
            })->where('is_deleted', false)->get();
        }

        return view('instructor.manage-students', compact('subjects', 'students'));
    }

public function addStudentForm()
{
    // Get the courses based on the instructor's department
    $courses = Course::where('department_id', Auth::user()->department_id)
                     ->get();

    // Fetch the subjects as well
    $subjects = Subject::where('instructor_id', Auth::id())->get();

    return view('instructor.add-student', compact('subjects', 'courses'));
}

    

    public function enrollStudent(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'year_level' => 'required|integer|min:1|max:5',
            'subject_id' => 'required|exists:subjects,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Student::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'year_level' => $request->year_level,
            'department_id' => Auth::user()->department_id,
            'course_id' => $request->course_id,
            'academic_period_id' => Subject::find($request->subject_id)->academic_period_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);        

        StudentSubject::create([
            'student_id' => $student->id,
            'subject_id' => $request->subject_id,
        ]);

        return redirect()->route('instructor.manageStudents')->with('success', 'Student created and enrolled successfully.');
    }

    public function dropStudent(Request $request, $studentId)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        StudentSubject::where('student_id', $studentId)
            ->where('subject_id', $request->subject_id)
            ->delete();

        return redirect()->back()->with('success', 'Student dropped from subject.');
    }

    // ============================
    // Manage Activities
    // ============================

    public function activities(Request $request)
    {
        $subjects = Subject::where('instructor_id', Auth::id())->get();
        $activities = [];

        if ($request->filled('subject_id')) {
            $activities = Activity::where('subject_id', $request->subject_id)
                ->where('is_deleted', false)
                ->orderBy('term')
                ->orderBy('type')
                ->orderBy('created_at')
                ->get();
        }

        return view('instructor.activities.index', compact('subjects', 'activities'));
    }

    public function storeActivity(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
            'type' => 'required|in:quiz,ocr,exam',
            'title' => 'required|string|max:255',
            'number_of_items' => 'required|integer|min:1',
        ]);

        Activity::create([
            'subject_id' => $request->subject_id,
            'term' => $request->term,
            'type' => $request->type,
            'title' => $request->title,
            'number_of_items' => $request->number_of_items,
            'is_deleted' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Activity created successfully.');
    }

    public function deleteActivity($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->update([
            'is_deleted' => true,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Activity deleted successfully.');
    }

    // ============================
    // Manage Grades
    // ============================

    public function manageGrades(Request $request)
    {
        $subjects = Subject::where('instructor_id', Auth::id())->get();
        $students = [];
        $activities = [];
        $scores = [];
        $termGrades = [];
    
        $subject = null;
        $term = $request->term ?? 'prelim'; // Default to Prelim if none selected
    
        if ($request->filled('subject_id')) {
            $subject = Subject::find($request->subject_id);
    
            // Fetch enrolled students in the selected subject
            $students = Student::whereHas('subjects', function ($query) use ($request) {
                    $query->where('subject_id', $request->subject_id);
                })
                ->where('is_deleted', false)
                ->get();
    
            // Fetch all activities for the current subject & term
            $activities = Activity::where('subject_id', $request->subject_id)
                ->where('term', $term)
                ->where('is_deleted', false)
                ->orderBy('type')
                ->orderBy('created_at')
                ->get();
    
            // Auto-generate 3 quizzes, 3 OCRs, and 1 exam if none exist
            if ($activities->isEmpty()) {
                $defaultActivities = [];
    
                foreach (['quiz' => 3, 'ocr' => 3, 'exam' => 1] as $type => $count) {
                    for ($i = 1; $i <= $count; $i++) {
                        $defaultActivities[] = [
                            'subject_id' => $subject->id,
                            'term' => $term,
                            'type' => $type,
                            'title' => ucfirst($type) . ' ' . $i,
                            'number_of_items' => 100,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
    
                Activity::insert($defaultActivities);
    
                // Reload activities
                $activities = Activity::where('subject_id', $request->subject_id)
                    ->where('term', $term)
                    ->where('is_deleted', false)
                    ->orderBy('type')
                    ->orderBy('created_at')
                    ->get();
            }
    
            // Loop through each student and calculate scores and term grade
            foreach ($students as $student) {
                $totalEarned = 0;
                $totalPossible = 0;
                $allScored = true; // Flag to track if all activities have scores
    
                foreach ($activities as $activity) {
                    $score = $student->scores()
                                ->where('activity_id', $activity->id)
                                ->first()
                                ->score ?? null;
    
                    $scores[$student->id][$activity->id] = $score;
    
                    if ($score !== null) {
                        $totalEarned += $score;
                        $totalPossible += $activity->number_of_items;
                    } else {
                        $allScored = false; // If any score is missing, mark as incomplete
                    }
                }
    
                // Only calculate term grade if ALL scores are present
                if ($allScored && $totalPossible > 0) {
                    $termGrades[$student->id] = number_format(($totalEarned / $totalPossible) * 100, 2);
                } else {
                    $termGrades[$student->id] = null;
                }
            }
        }
    
        return view('instructor.manage-grades', compact(
            'subjects', 'subject', 'term', 'students', 'activities', 'scores', 'termGrades'
        ));
    }               

    public function saveGrades(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
            'scores' => 'required|array',
        ]);
    
        foreach ($request->scores as $studentId => $activityScores) {
            foreach ($activityScores as $activityId => $score) {
                if ($score !== null && $score !== '') {
                    Score::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'activity_id' => $activityId,
                        ],
                        [
                            'score' => $score,
                            'updated_by' => Auth::id(),
                        ]
                    );
                }
            }
        }
    
        return redirect()->back()->with('success', 'Scores saved successfully.');
    }
    

    public function finalGrades(Request $request)
    {
        $subjects = Subject::where('instructor_id', Auth::id())->get();
        $finalGrades = [];

        if ($request->filled('subject_id')) {
            $finalGrades = FinalGrade::where('subject_id', $request->subject_id)
                ->whereHas('student', function ($query) {
                    $query->where('is_deleted', false);
                })
                ->with('student')
                ->get();
        }

        return view('instructor.final-grades', compact('subjects', 'finalGrades'));
    }

    // ============================
    // Private Helpers
    // ============================

    private function getTermId($term)
    {
        $mapping = [
            'prelim' => 1,
            'midterm' => 2,
            'prefinal' => 3,
            'final' => 4,
        ];

        return $mapping[$term] ?? null;
    }
}
