<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CurriculumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Gate::authorize('admin-chair');

        $curriculums = Curriculum::with('course')
            ->orderByDesc('created_at')
            ->get();

        return view('curriculum.index', compact('curriculums'));
    }

    public function show(Curriculum $curriculum)
    {
        Gate::authorize('admin-chair');

        $subjects = $curriculum->subjects()->orderBy('year_level')->orderBy('semester')->get();

        return view('curriculum.show', compact('curriculum', 'subjects'));
    }

    public function create()
    {
        Gate::authorize('admin-chair');

        $courses = Course::where('is_deleted', false)->orderBy('course_code')->get();

        return view('curriculum.create', compact('courses'));
    }

    public function store(Request $request)
    {
        Gate::authorize('admin-chair');

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255|unique:curriculums,name',
        ]);

        Curriculum::create([
            'course_id' => $request->course_id,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('curriculum.index')->with('success', 'Curriculum created successfully.');
    }

    public function destroy(Curriculum $curriculum)
    {
        Gate::authorize('admin-chair');

        $curriculum->delete();

        return redirect()->route('curriculum.index')->with('success', 'Curriculum deleted.');
    }

    public function addSubject(Request $request, Curriculum $curriculum)
    {
        Gate::authorize('admin-chair');

        $request->validate([
            'subject_code' => 'required|string|max:255',
            'subject_description' => 'required|string|max:255',
            'year_level' => 'required|integer',
            'semester' => 'required|string',
        ]);

        $curriculum->subjects()->create([
            'subject_code' => $request->subject_code,
            'subject_description' => $request->subject_description,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
        ]);

        return redirect()->route('curriculum.show', $curriculum)->with('success', 'Subject added to curriculum.');
    }

    public function removeSubject(CurriculumSubject $subject)
    {
        Gate::authorize('admin-chair');

        $subject->delete();

        return back()->with('success', 'Subject removed from curriculum.');
    }

    public function selectSubjects()
    {
        Gate::authorize('admin-chair');

        $curriculums = Curriculum::with('course')->get();
        return view('chairperson.select-curriculum-subjects', compact('curriculums'));
    }

    public function fetchSubjects(Curriculum $curriculum)
    {
        Gate::authorize('admin-chair');

        $subjects = $curriculum->subjects()
            ->orderBy('year_level')
            ->orderBy('semester')
            ->get();

        return response()->json($subjects);
    }

    public function confirmSubjects(Request $request)
    {
        Gate::authorize('admin-chair');

        $request->validate([
            'curriculum_id' => 'required|exists:curriculums,id',
            'subject_ids' => 'required|array',
        ]);

        $subjects = CurriculumSubject::where('curriculum_id', $request->curriculum_id)
            ->whereIn('id', $request->subject_ids)
            ->get();

        foreach ($subjects as $curriculumSubject) {
            Subject::firstOrCreate([
                'subject_code' => $curriculumSubject->subject_code
            ], [
                'subject_description' => $curriculumSubject->subject_description,
                'department_id' => Auth::user()->department_id,
                'course_id' => $curriculumSubject->curriculum->course_id,
                'academic_period_id' => session('active_academic_period_id'),
                'is_universal' => false,
                'is_deleted' => false,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Subjects confirmed and added to the subject list.');
    }
}
