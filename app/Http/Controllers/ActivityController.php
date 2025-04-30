<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Subject;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List Activities for an Instructor's Subjects
    public function index(Request $request)
    {
        Gate::authorize('instructor');

        $subjects = Subject::where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->get();

        $activities = collect();

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

    // Standard Create Activity (full form)
    public function create()
    {
        Gate::authorize('instructor');

        $subjects = Subject::where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->get();

        $academicPeriods = AcademicPeriod::where('is_deleted', false)
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->get();

        return view('instructor.activities.create', compact('subjects', 'academicPeriods'));
    }

    // 🎯 New: Quick Add Activity inside Manage Grades
    public function addActivity(Request $request)
    {
        Gate::authorize('instructor');

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        $term = $request->term;

        return view('instructor.activities.add', compact('subject', 'term'));
    }

    // Store New Activity (both standard and quick add)
    public function store(Request $request)
    {
        Gate::authorize('instructor');

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
            'type' => 'required|in:quiz,ocr,exam',
            'title' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
        ]);

        Activity::create([
            'subject_id' => $request->subject_id,
            'term' => $request->term,
            'type' => $request->type,
            'title' => $request->title,
            'number_of_items' => $request->points, // ✅ internally saved to number_of_items
            'is_deleted' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('instructor.manageGrades', [
            'subject_id' => $request->subject_id,
            'term' => $request->term,
        ])->with('success', 'Activity created successfully.');
    }

    // Soft Delete Activity
    public function delete($id)
    {
        Gate::authorize('instructor');

        $activity = Activity::where('id', $id)
            ->where('is_deleted', false)
            ->firstOrFail();

        $activity->update([
            'is_deleted' => true,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Activity deleted successfully.');
    }
}
