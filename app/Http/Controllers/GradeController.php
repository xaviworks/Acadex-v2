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

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        Gate::authorize('instructor');

        $academicPeriodId = session('active_academic_period_id'); // You can change to request()->input('academic_period_id') if passed in URL

        $subjects = Subject::where('instructor_id', Auth::id())
            ->when($academicPeriodId, fn($q) => $q->where('academic_period_id', $academicPeriodId))
            ->get();
                $term = $request->term ?? 'prelim';
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
                ->where('term', $term)->where('is_deleted', false)
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
                    ->where('term', $term)->where('is_deleted', false)
                    ->orderBy('type')->orderBy('created_at')->get();
            }

            foreach ($students as $student) {
                $totalEarned = 0;
                $totalPossible = 0;
                $allScored = true;

                foreach ($activities as $activity) {
                    $score = $student->scores()->where('activity_id', $activity->id)->first()->score ?? null;
                    $scores[$student->id][$activity->id] = $score;

                    if ($score !== null) {
                        $totalEarned += $score;
                        $totalPossible += $activity->number_of_items;
                    } else {
                        $allScored = false;
                    }
                }

                $termGrades[$student->id] = ($allScored && $totalPossible > 0)
                    ? number_format(($totalEarned / $totalPossible) * 100, 2)
                    : null;
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
            $totalEarned = 0;
            $totalPossible = 0;
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
                    ->where('is_deleted', false)->get();

                foreach ($activities as $activity) {
                    $score = Score::where('student_id', $studentId)->where('activity_id', $activity->id)->first();

                    if ($score && $score->score !== null) {
                        $totalEarned += $score->score;
                        $totalPossible += $activity->number_of_items;
                    } else {
                        $allScored = false;
                        break;
                    }
                }

                if ($allScored && $totalPossible > 0) {
                    $termGrade = round(($totalEarned / $totalPossible) * 100, 2);

                    TermGrade::updateOrCreate(
                        ['student_id' => $studentId, 'subject_id' => $subject->id, 'term_id' => $termId],
                        ['term_grade' => $termGrade, 'academic_period_id' => $subject->academic_period_id, 'created_by' => Auth::id(), 'updated_by' => Auth::id()]
                    );
                }
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

        Score::updateOrCreate(
            ['student_id' => $studentId, 'activity_id' => $request->activity_id],
            ['score' => $request->score, 'updated_by' => Auth::id()]
        );

        $activities = Activity::where('subject_id', $subject->id)
            ->where('term', $request->term)
            ->where('is_deleted', false)->get();

        $totalEarned = 0;
        $totalPossible = 0;
        $allScored = true;

        foreach ($activities as $activity) {
            $score = Score::where('student_id', $studentId)
                ->where('activity_id', $activity->id)->first();

            if ($score && $score->score !== null) {
                $totalEarned += $score->score;
                $totalPossible += $activity->number_of_items;
            } else {
                $allScored = false;
                break;
            }
        }

        if ($allScored && $totalPossible > 0) {
            $termGrade = round(($totalEarned / $totalPossible) * 100, 2);

            TermGrade::updateOrCreate(
                ['student_id' => $studentId, 'subject_id' => $subject->id, 'term_id' => $termId],
                ['term_grade' => $termGrade, 'academic_period_id' => $subject->academic_period_id, 'created_by' => Auth::id(), 'updated_by' => Auth::id()]
            );
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
        $terms = ['prelim', 'midterm', 'prefinal', 'final'];
        $total = 0;
        $hasAll = true;

        foreach ($terms as $term) {
            $grade = TermGrade::where('student_id', $studentId)
                ->where('subject_id', $subject->id)
                ->where('term_id', $this->getTermId($term))
                ->first();

            if (!$grade || $grade->term_grade === null) {
                $hasAll = false;
                break;
            }

            $total += $grade->term_grade;
        }

        if ($hasAll) {
            $average = round($total / 4, 2);
            $remarks = $average >= 75 ? 'Passed' : 'Failed';

            FinalGrade::updateOrCreate(
                ['student_id' => $studentId, 'subject_id' => $subject->id],
                ['academic_period_id' => $subject->academic_period_id, 'final_grade' => $average, 'remarks' => $remarks, 'is_deleted' => false, 'created_by' => Auth::id(), 'updated_by' => Auth::id()]
            );
        }
    }

    public function partial(Request $request)
{
    $subject = Subject::findOrFail($request->subject_id);
    $term = $request->term;

    $students = Student::whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
        ->where('is_deleted', false)->get();

    $activities = Activity::where('subject_id', $subject->id)
        ->where('term', $term)
        ->where('is_deleted', false)
        ->orderBy('type')
        ->orderBy('created_at')->get();

    $scores = [];
    $termGrades = [];

    foreach ($students as $student) {
        $totalEarned = 0;
        $totalPossible = 0;
        $allScored = true;

        foreach ($activities as $activity) {
            $score = $student->scores()->where('activity_id', $activity->id)->first()->score ?? null;
            $scores[$student->id][$activity->id] = $score;

            if ($score !== null) {
                $totalEarned += $score;
                $totalPossible += $activity->number_of_items;
            } else {
                $allScored = false;
            }
        }

        $termGrades[$student->id] = ($allScored && $totalPossible > 0)
            ? number_format(($totalEarned / $totalPossible) * 100, 2)
            : null;
    }

    return view('instructor.partials.grade-body', compact('subject', 'term', 'students', 'activities', 'scores', 'termGrades'));
}

}
