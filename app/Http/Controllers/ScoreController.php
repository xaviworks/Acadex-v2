<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Subject;
use App\Models\Activity;
use App\Models\Student;
use App\Models\Score;
use App\Models\TermGrade;
use App\Models\FinalGrade;

class ScoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================
    // View Scores Page
    // ============================
    public function index(Request $request)
    {
        Gate::authorize('instructor');

        $subjects = Subject::where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->get();

        $students = collect();
        $activities = collect();
        $savedScores = collect();
        $termGrades = collect();

        if ($request->filled('subject_id') && $request->filled('term')) {
            $students = Student::whereHas('subjects', function ($query) use ($request) {
                $query->where('subject_id', $request->subject_id);
            })->get();

            $activities = Activity::where('subject_id', $request->subject_id)
                ->where('term', $request->term)
                ->where('is_deleted', false)
                ->orderBy('type')
                ->orderBy('created_at')
                ->get();

            $scores = Score::whereIn('student_id', $students->pluck('id'))
                ->whereIn('activity_id', $activities->pluck('id'))
                ->get()
                ->groupBy(['student_id', 'activity_id']);

            $savedScores = $scores;

            $termGradesCollection = TermGrade::whereIn('student_id', $students->pluck('id'))
                ->where('subject_id', $request->subject_id)
                ->where('term_id', $this->getTermId($request->term))
                ->get()
                ->keyBy('student_id');

            foreach ($termGradesCollection as $studentId => $termGrade) {
                $termGrades[$studentId] = $termGrade->term_grade;
            }
        }

        return view('instructor.scores.index', compact('subjects', 'students', 'activities', 'savedScores', 'termGrades'));
    }

    // ============================
    // Save Scores
    // ============================
    public function save(Request $request)
    {
        Gate::authorize('instructor');

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
            'scores' => 'required|array',
        ]);

        $subjectId = $request->subject_id;
        $term = $request->term;

        foreach ($request->scores as $studentId => $activityScores) {
            foreach ($activityScores as $activityId => $scoreValue) {
                if ($scoreValue !== null) {
                    Score::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'activity_id' => $activityId,
                        ],
                        [
                            'score' => $scoreValue,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                        ]
                    );
                }
            }
        }

        $this->recomputeTermGrades($subjectId, $term);

        return redirect()->back()->with('success', 'Scores saved and term grades recalculated successfully.');
    }

    // ============================
    // Recompute Term Grades
    // ============================
    private function recomputeTermGrades($subjectId, $term)
    {
        $activities = Activity::where('subject_id', $subjectId)
            ->where('term', $term)
            ->where('is_deleted', false)
            ->get();

        $students = Student::whereHas('subjects', function ($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })->get();

        foreach ($students as $student) {
            $quizTotal = $quizScore = 0;
            $ocrTotal = $ocrScore = 0;
            $examTotal = $examScore = 0;

            foreach ($activities as $activity) {
                $score = Score::where('student_id', $student->id)
                    ->where('activity_id', $activity->id)
                    ->first();

                if ($score) {
                    switch ($activity->type) {
                        case 'quiz':
                            $quizScore += $score->score;
                            $quizTotal += $activity->number_of_items;
                            break;
                        case 'ocr':
                            $ocrScore += $score->score;
                            $ocrTotal += $activity->number_of_items;
                            break;
                        case 'exam':
                            $examScore += $score->score;
                            $examTotal += $activity->number_of_items;
                            break;
                    }
                }
            }

            $quizGrade = $quizTotal ? ($quizScore / $quizTotal) * 100 : null;
            $ocrGrade = $ocrTotal ? ($ocrScore / $ocrTotal) * 100 : null;
            $examGrade = $examTotal ? ($examScore / $examTotal) * 100 : null;

            $termGrade = null;
            if (!is_null($quizGrade) && !is_null($ocrGrade) && !is_null($examGrade)) {
                $termGrade = round(($quizGrade * 0.4) + ($ocrGrade * 0.2) + ($examGrade * 0.4), 2);
            }

            TermGrade::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                    'term_id' => $this->getTermId($term),
                ],
                [
                    'quiz_grade' => $quizGrade,
                    'ocr_grade' => $ocrGrade,
                    'exam_grade' => $examGrade,
                    'term_grade' => $termGrade,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]
            );

            $this->recomputeFinalGrade($student->id, $subjectId);
        }
    }

    // ============================
    // Recompute Final Grade
    // ============================
    private function recomputeFinalGrade($studentId, $subjectId)
    {
        $terms = [1, 2, 3, 4];

        $grades = TermGrade::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->whereIn('term_id', $terms)
            ->pluck('term_grade', 'term_id');

        if ($grades->count() === 4) {
            $final = round($grades->avg(), 2);

            FinalGrade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                ],
                [
                    'final_grade' => $final,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]
            );
        }
    }

    // ============================
    // Helper: Get Term ID
    // ============================
    private function getTermId($term)
    {
        return [
            'prelim' => 1,
            'midterm' => 2,
            'prefinal' => 3,
            'final' => 4,
        ][$term] ?? null;
    }

    // ============================
    // View Final Grades
    // ============================
    public function finalGrades(Request $request)
    {
        Gate::authorize('instructor');

        $subjects = Subject::where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->get();

        $finalData = [];

        if ($request->filled('subject_id')) {
            $students = Student::whereHas('subjects', function ($query) use ($request) {
                $query->where('subject_id', $request->subject_id);
            })->get();

            foreach ($students as $student) {
                $termGrades = TermGrade::where('student_id', $student->id)
                    ->where('subject_id', $request->subject_id)
                    ->pluck('term_grade', 'term_id');

                $prelim = $termGrades[1] ?? null;
                $midterm = $termGrades[2] ?? null;
                $prefinal = $termGrades[3] ?? null;
                $final = $termGrades[4] ?? null;

                $average = (!is_null($prelim) && !is_null($midterm) && !is_null($prefinal) && !is_null($final))
                    ? round(($prelim + $midterm + $prefinal + $final) / 4, 2)
                    : null;

                $finalData[] = [
                    'student' => $student,
                    'prelim' => $prelim,
                    'midterm' => $midterm,
                    'prefinal' => $prefinal,
                    'final' => $final,
                    'average' => $average,
                ];
            }
        }

        return view('instructor.scores.final-grades', compact('subjects', 'finalData'));
    }
}
