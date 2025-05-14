<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TermGrade;
use App\Models\FinalGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;


class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        Gate::authorize('instructor');
    
        $academicPeriodId = session('active_academic_period_id');
        $term = $request->term ?? 'prelim';
    
        $subjects = Subject::where('instructor_id', Auth::id())
            ->when($academicPeriodId, fn($q) => $q->where('academic_period_id', $academicPeriodId))
            ->withCount('students')
            ->get();
    
        foreach ($subjects as $subject) {
            $total = $subject->students_count;
            $terms = ['prelim', 'midterm', 'prefinal', 'final'];
            $gradedCount = 0;
    
            foreach ($terms as $t) {
                $gradedTerms = TermGrade::where('subject_id', $subject->id)
                    ->where('term_id', $this->getTermId($t))
                    ->distinct('student_id')
                    ->count('student_id');
    
                if ($gradedTerms === $total && $total > 0) {
                    $gradedCount++;
                }
            }
    
            $subject->grade_status = match (true) {
                $total === 0 => 'not_started',
                $gradedCount === 0 => 'pending',
                $gradedCount < count($terms) => 'pending',
                default => 'completed',
            };
        }
    
        $students = $activities = $scores = $termGrades = [];
        $subject = null;
    
        if ($request->filled('subject_id')) {
            $subject = Subject::where('id', $request->subject_id)
                ->when($academicPeriodId, fn($q) => $q->where('academic_period_id', $academicPeriodId))
                ->firstOrFail();
    
            if ($academicPeriodId && $subject->academic_period_id !== (int) $academicPeriodId) {
                abort(403, 'Subject does not belong to the current academic period.');
            }
    
            $students = Student::whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
                ->where('is_deleted', false)->get();
    
            $activities = Activity::where('subject_id', $subject->id)
                ->where('term', $term)
                ->where('is_deleted', false)
                ->orderBy('type')->orderBy('created_at')->get();
    
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
                $activities = Activity::where('subject_id', $subject->id)
                    ->where('term', $term)
                    ->where('is_deleted', false)
                    ->orderBy('type')->orderBy('created_at')->get();
            }
    
            foreach ($students as $student) {
                $scoresByType = [
                    'quiz' => ['total' => 0, 'count' => 0],
                    'ocr' => ['total' => 0, 'count' => 0],
                    'exam' => ['total' => 0, 'count' => 0],
                ];
    
                $allScored = true;
    
                foreach ($activities as $activity) {
                    $scoreRecord = $student->scores()->where('activity_id', $activity->id)->first();
                    $score = $scoreRecord?->score;
                    $scores[$student->id][$activity->id] = $score;
    
                    if ($score !== null) {
                        $scaledScore = ($score / $activity->number_of_items) * 50 + 50;
                        $scoresByType[$activity->type]['total'] += $scaledScore;
                        $scoresByType[$activity->type]['count']++;
                    } else {
                        $allScored = false;
                    }
                }
    
                if ($allScored) {
                    $quizAvg = $scoresByType['quiz']['count'] > 0
                        ? $scoresByType['quiz']['total'] / $scoresByType['quiz']['count']
                        : 0;
    
                    $ocrAvg = $scoresByType['ocr']['count'] > 0
                        ? $scoresByType['ocr']['total'] / $scoresByType['ocr']['count']
                        : 0;
    
                    $examAvg = $scoresByType['exam']['count'] > 0
                        ? $scoresByType['exam']['total'] / $scoresByType['exam']['count']
                        : 0;
    
                    $termGrade = ($quizAvg * 0.4) + ($ocrAvg * 0.2) + ($examAvg * 0.4);
                    $termGrades[$student->id] = number_format($termGrade, 2);
                } else {
                    $termGrades[$student->id] = null;
                }
            }
        }
    
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('instructor.partials.grade-body', compact(
                'subject', 'term', 'students', 'activities', 'scores', 'termGrades'
            ));
        }
    
        return view('instructor.manage-grades', compact(
            'subjects', 'subject', 'term', 'students', 'activities', 'scores', 'termGrades'
        ));
    }
    
    
    
    

    public function store(Request $request)
    {
        Gate::authorize('instructor');
    
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
            'scores' => 'required|array',
        ]);
    
        $subject = Subject::findOrFail($request->subject_id);
        $termId = $this->getTermId($request->term);
    
        foreach ($request->scores as $studentId => $activityScores) {
            $allScored = true;
    
            foreach ($activityScores as $activityId => $score) {
                if ($score !== null && $score !== '') {
                    Score::updateOrCreate(
                        ['student_id' => $studentId, 'activity_id' => $activityId],
                        ['score' => $score, 'updated_by' => Auth::id()]
                    );
                } else {
                    $allScored = false;
                }
            }
    
            if ($allScored) {
                $activities = Activity::where('subject_id', $subject->id)
                    ->where('term', $request->term)
                    ->where('is_deleted', false)
                    ->get();
    
                $scoresByType = [
                    'quiz' => ['total' => 0, 'count' => 0],
                    'ocr' => ['total' => 0, 'count' => 0],
                    'exam' => ['total' => 0, 'count' => 0],
                ];
    
                foreach ($activities as $activity) {
                    $score = Score::where('student_id', $studentId)
                        ->where('activity_id', $activity->id)
                        ->first();
    
                    if ($score && $score->score !== null) {
                        $scaledScore = ($score->score / $activity->number_of_items) * 50 + 50;
                        $scoresByType[$activity->type]['total'] += $scaledScore;
                        $scoresByType[$activity->type]['count']++;
                    } else {
                        $allScored = false;
                        break;
                    }
                }
    
                if ($allScored) {
                    $quizAvg = $scoresByType['quiz']['count'] > 0
                        ? $scoresByType['quiz']['total'] / $scoresByType['quiz']['count']
                        : 0;
    
                    $ocrAvg = $scoresByType['ocr']['count'] > 0
                        ? $scoresByType['ocr']['total'] / $scoresByType['ocr']['count']
                        : 0;
    
                    $examAvg = $scoresByType['exam']['count'] > 0
                        ? $scoresByType['exam']['total'] / $scoresByType['exam']['count']
                        : 0;
    
                    $termGrade = round(
                        ($quizAvg * 0.4) + ($ocrAvg * 0.2) + ($examAvg * 0.4),
                        2
                    );
    
                    TermGrade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'subject_id' => $subject->id,
                            'term_id' => $termId
                        ],
                        [
                            'term_grade' => $termGrade,
                            'academic_period_id' => $subject->academic_period_id,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id()
                        ]
                    );
                } else {
                    TermGrade::where('student_id', $studentId)
                        ->where('subject_id', $subject->id)
                        ->where('term_id', $termId)
                        ->delete();
                }
            } else {
                TermGrade::where('student_id', $studentId)
                    ->where('subject_id', $subject->id)
                    ->where('term_id', $termId)
                    ->delete();
            }
    
            $this->tryUpdateFinalGrade($studentId, $subject);
        }
    
        return redirect()->route('instructor.grades.index', [
            'subject_id' => $request->subject_id,
            'term' => $request->term
        ])->with('success', 'Scores saved and grades updated successfully.');
    }
    
    
    

    public function ajaxSaveScore(Request $request)
    {
        Gate::authorize('instructor');
    
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'activity_id' => 'required|exists:activities,id',
            'score' => 'nullable|numeric|min:0',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
        ]);
    
        $studentId = $request->student_id;
        $subject = Subject::findOrFail($request->subject_id);
        $termId = $this->getTermId($request->term);
    
        // Save the individual score
        Score::updateOrCreate(
            ['student_id' => $studentId, 'activity_id' => $request->activity_id],
            ['score' => $request->score, 'updated_by' => Auth::id()]
        );
    
        $activities = Activity::where('subject_id', $subject->id)
            ->where('term', $request->term)
            ->where('is_deleted', false)
            ->get();
    
        $scoresByType = [
            'quiz' => ['total' => 0, 'count' => 0],
            'ocr' => ['total' => 0, 'count' => 0],
            'exam' => ['total' => 0, 'count' => 0],
        ];
    
        $allScored = true;
    
        foreach ($activities as $activity) {
            $score = Score::where('student_id', $studentId)
                ->where('activity_id', $activity->id)
                ->first();
    
            if ($score && $score->score !== null) {
                $scaledScore = ($score->score / $activity->number_of_items) * 50 + 50;
                $scoresByType[$activity->type]['total'] += $scaledScore;
                $scoresByType[$activity->type]['count']++;
            } else {
                $allScored = false;
                break;
            }
        }
    
        if ($allScored) {
            $quizAvg = $scoresByType['quiz']['count'] > 0
                ? $scoresByType['quiz']['total'] / $scoresByType['quiz']['count']
                : 0;
    
            $ocrAvg = $scoresByType['ocr']['count'] > 0
                ? $scoresByType['ocr']['total'] / $scoresByType['ocr']['count']
                : 0;
    
            $examAvg = $scoresByType['exam']['count'] > 0
                ? $scoresByType['exam']['total'] / $scoresByType['exam']['count']
                : 0;
    
            $termGrade = round(
                ($quizAvg * 0.4) + ($ocrAvg * 0.2) + ($examAvg * 0.4),
                2
            );
    
            TermGrade::updateOrCreate(
                ['student_id' => $studentId, 'subject_id' => $subject->id, 'term_id' => $termId],
                ['term_grade' => $termGrade, 'academic_period_id' => $subject->academic_period_id, 'created_by' => Auth::id(), 'updated_by' => Auth::id()]
            );
        } else {
            TermGrade::where('student_id', $studentId)
                ->where('subject_id', $subject->id)
                ->where('term_id', $termId)
                ->delete();
        }
    
        $this->tryUpdateFinalGrade($studentId, $subject);
    
        return response()->json(['status' => 'success']);
    }
    
    
    

    private function getTermId($term)
    {
        return [
            'prelim' => 1,
            'midterm' => 2,
            'prefinal' => 3,
            'final' => 4,
        ][$term] ?? null;
    }

    private function tryUpdateFinalGrade($studentId, $subject)
    {
        // Define the terms to check
        $terms = ['prelim', 'midterm', 'prefinal', 'final'];
        $totalEarned = 0;
        $totalPossible = 0;
        $validTermGrades = 0;
    
        // Loop through each term to check if all term grades are available
        foreach ($terms as $term) {
            $grade = TermGrade::where('student_id', $studentId)
                ->where('subject_id', $subject->id)
                ->where('term_id', $this->getTermId($term))
                ->first();
    
            // Check if the grade is missing or null
            if (!$grade || $grade->term_grade === null) {
                // Log the missing term grade
                Log::warning("Missing or null term grade for student {$studentId} in subject {$subject->id}, term {$term}.");
                continue;  // Skip this term if grade is missing
            }
    
            // If the grade is valid, add it to the total and count the valid grades
            $totalEarned += $grade->term_grade;
            $totalPossible += 100;  // Since each term grade is scaled to 100
            $validTermGrades++;
        }
    
        // If we have valid grades from at least one term
        if ($validTermGrades > 0) {
            // Calculate the weighted average (similar to the individual term grade calculation)
            $quizAvg = $this->calculateWeightedAverage($studentId, $subject, 'quiz');
            $ocrAvg = $this->calculateWeightedAverage($studentId, $subject, 'ocr');
            $examAvg = $this->calculateWeightedAverage($studentId, $subject, 'exam');
    
            // Calculate the final grade using the weighted formula
            $finalGrade = round(
                ($quizAvg * 0.4) + ($ocrAvg * 0.2) + ($examAvg * 0.4),
                2
            );
    
            // Determine the remarks based on the average grade
            $remarks = $finalGrade >= 75 ? 'Passed' : 'Failed';
    
            try {
                // Update or create the final grade record
                FinalGrade::updateOrCreate(
                    ['student_id' => $studentId, 'subject_id' => $subject->id],
                    [
                        'academic_period_id' => $subject->academic_period_id,
                        'final_grade' => $finalGrade,
                        'remarks' => $remarks,
                        'is_deleted' => false,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ]
                );
    
                // Log the successful update of the final grade
                Log::info("Final grade updated for student {$studentId} in subject {$subject->id}: {$finalGrade} ({$remarks})");
    
            } catch (\Exception $e) {
                // Log any error that happens during the final grade creation or update
                Log::error("Error updating final grade for student {$studentId} in subject {$subject->id}: {$e->getMessage()}");
            }
        } else {
            // Log if no valid grades are found for the student in any term
            Log::info("Final grade not updated for student {$studentId} in subject {$subject->id} due to missing term grades.");
        }
    }
    
    // Helper method to calculate weighted average for a specific activity type (quiz, ocr, exam)
    private function calculateWeightedAverage($studentId, $subject, $type)
    {
        // Get activities for the subject and type
        $activities = Activity::where('subject_id', $subject->id)
            ->where('type', $type)
            ->where('is_deleted', false)
            ->get();
    
        $totalEarned = 0;
        $totalPossible = 0;
    
        foreach ($activities as $activity) {
            $score = Score::where('student_id', $studentId)
                ->where('activity_id', $activity->id)
                ->first();
    
            if ($score && $score->score !== null) {
                // Apply the scoring formula
                $scaledScore = ($score->score / $activity->number_of_items) * 50 + 50;
                $totalEarned += $scaledScore;
                $totalPossible += 100;  // Since each activity is scaled to 100
            }
        }
    
        // Return the average score or 0 if no scores are found
        return $totalPossible > 0 ? ($totalEarned / $totalPossible) * 100 : 0;
    }
    
    
    

    public function partial(Request $request)
    {
        $subject = Subject::findOrFail($request->subject_id);
        $term = $request->term;
    
        // Fetch students enrolled in the subject
        $students = Student::whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->where('is_deleted', false)->get();
    
        // Fetch activities for the subject and term
        $activities = Activity::where('subject_id', $subject->id)
            ->where('term', $term)
            ->where('is_deleted', false)
            ->orderBy('type')
            ->orderBy('created_at')->get();
    
        $scores = [];
        $termGrades = [];
    
        // Loop through each student to calculate their scores
        foreach ($students as $student) {
            $totalEarned = 0;
            $totalPossible = 0;
            $allScored = true;
    
            // Loop through each activity to calculate the individual score
            foreach ($activities as $activity) {
                $score = $student->scores()->where('activity_id', $activity->id)->first()->score ?? null;
                $scores[$student->id][$activity->id] = $score;
    
                if ($score !== null) {
                    // Apply the new formula: (RS / N) * 50 + 50
                    $scaledScore = ($score / $activity->number_of_items) * 50 + 50;
    
                    // Add to the total earned and possible scores
                    $totalEarned += $scaledScore;
                    $totalPossible += 100; // Each activity's scaled score is out of 100
                } else {
                    // If any score is missing, mark as not scored
                    $allScored = false;
                }
            }
    
            // Calculate the term grade only if all activities have scores
            $termGrades[$student->id] = ($allScored && $totalPossible > 0)
                ? number_format(($totalEarned / $totalPossible) * 100, 2)
                : null;
        }
    
        // Return the partial view with the necessary data
        return view('instructor.partials.grade-body', compact('subject', 'term', 'students', 'activities', 'scores', 'termGrades'));
    }
    
    

}
