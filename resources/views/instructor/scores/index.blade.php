@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Manage Scores</h1>

    {{-- Subject & Term Selection --}}
    <form method="GET" action="{{ route('instructor.scores') }}">
        <div class="flex items-center gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium">Subject</label>
                <select name="subject_id" class="border rounded px-3 py-2 w-64" onchange="this.form.submit()">
                    <option value="">-- Select Subject --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_code }} - {{ $subject->subject_description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Term</label>
                <select name="term" class="border rounded px-3 py-2 w-64" onchange="this.form.submit()">
                    <option value="">-- Select Term --</option>
                    @foreach(['prelim', 'midterm', 'prefinal', 'final'] as $termOption)
                        <option value="{{ $termOption }}" {{ request('term') == $termOption ? 'selected' : '' }}>
                            {{ ucfirst($termOption) }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('subject_id') && request('term'))
                <div>
                    <a href="{{ route('instructor.activities.create') }}?subject_id={{ request('subject_id') }}&term={{ request('term') }}"
                       class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                        + Add Activity
                    </a>
                </div>
            @endif
        </div>
    </form>

    {{-- Scores Table --}}
    @if($students && $activities)
    <form method="POST" action="{{ route('instructor.scores.save') }}">
        @csrf
        <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
        <input type="hidden" name="term" value="{{ request('term') }}">

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3 text-left border">Student</th>
                        @foreach($activities as $activity)
                            <th class="p-3 text-center border">
                                {{ ucfirst($activity->type) }}<br>
                                <small>{{ $activity->title }} ({{ $activity->number_of_items }} pts)</small>
                            </th>
                        @endforeach
                        <th class="p-3 text-center border">Term Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr class="hover:bg-gray-100">
                        <td class="p-3 border font-semibold">{{ $student->first_name }} {{ $student->last_name }}</td>

                        @foreach($activities as $activity)
                            @php
                                $score = $savedScores[$student->id][$activity->id][0] ?? null;
                            @endphp
                            <td class="p-2 text-center border">
                                <input type="number"
                                       name="scores[{{ $student->id }}][{{ $activity->id }}]"
                                       class="score-input student-{{ $student->id }} w-24 border rounded text-center {{ $score ? 'bg-green-100 font-bold' : '' }}"
                                       data-activity-type="{{ $activity->type }}"
                                       data-max="{{ $activity->number_of_items }}"
                                       step="0.01" min="0" max="{{ $activity->number_of_items }}"
                                       value="{{ $score ? $score->score : '' }}"
                                       placeholder="{{ $score ? '' : 'Pending' }}">
                                <div class="text-xs text-gray-500 mt-1">/ {{ $activity->number_of_items }} pts</div>
                            </td>
                        @endforeach

                        @php
                        $missingScore = false;
                        foreach($activities as $activity) {
                            if (!isset($savedScores[$student->id][$activity->id][0])) {
                                $missingScore = true;
                                break;
                            }
                        }
                        @endphp
                    
                        <td class="p-2 text-center border font-bold text-indigo-600">
                            <span id="term-grade-{{ $student->id }}">
                                @if($missingScore)
                                    -
                                @else
                                    {{ isset($termGrades[$student->id]) ? number_format($termGrades[$student->id], 2) : '-' }}
                                @endif
                            </span>
                        </td>
                    
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-right">
            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                Save Scores
            </button>
        </div>
    </form>
    @elseif(request('subject_id') && request('term'))
        <div class="text-center text-gray-500 mt-8">
            No students or activities found for this subject and term.
        </div>
    @endif
</div>

{{-- Live Term Grade Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateTermGrade(studentId) {
        let quizScore = 0, quizTotal = 0;
        let ocrScore = 0, ocrTotal = 0;
        let examScore = 0, examTotal = 0;
        let allFilled = true;

        document.querySelectorAll(`.student-${studentId}`).forEach(input => {
            let type = input.dataset.activityType;
            let max = parseFloat(input.dataset.max);
            let score = parseFloat(input.value);

            if (isNaN(score)) {
                allFilled = false;
            } else {
                if (type === 'quiz') { quizScore += score; quizTotal += max; }
                if (type === 'ocr') { ocrScore += score; ocrTotal += max; }
                if (type === 'exam') { examScore += score; examTotal += max; }
            }
        });

        const gradeCell = document.getElementById(`term-grade-${studentId}`);
        if (!gradeCell) return;

        if (allFilled && quizTotal && ocrTotal && examTotal) {
            let quizGrade = (quizScore / quizTotal) * 100;
            let ocrGrade = (ocrScore / ocrTotal) * 100;
            let examGrade = (examScore / examTotal) * 100;
            let finalGrade = ((quizGrade * 0.3) + (ocrGrade * 0.3) + (examGrade * 0.4)).toFixed(2);
            gradeCell.innerText = finalGrade;
        } else {
            gradeCell.innerText = '-';
        }
    }

    function handleInput(event) {
        let studentClass = Array.from(event.target.classList).find(c => c.startsWith('student-'));
        if (studentClass) {
            let studentId = studentClass.replace('student-', '');
            calculateTermGrade(studentId);
        }
    }

    function attachInputListeners() {
        document.querySelectorAll('.score-input').forEach(input => {
            input.removeEventListener('input', handleInput);
            input.addEventListener('input', handleInput);
        });
    }

    function recalculateAllStudents() {
        const studentIds = new Set();
        document.querySelectorAll('.score-input').forEach(input => {
            const studentClass = Array.from(input.classList).find(c => c.startsWith('student-'));
            if (studentClass) {
                studentIds.add(studentClass.replace('student-', ''));
            }
        });

        studentIds.forEach(id => calculateTermGrade(id));
    }

    attachInputListeners();
    recalculateAllStudents(); // Initial term grade calculation

    let timeout; // debounced recalculation (important!)

    const observer = new MutationObserver(mutationsList => {
        let shouldRecalculate = false;

        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && (mutation.addedNodes.length || mutation.removedNodes.length)) {
                shouldRecalculate = true;
                break;
            }
        }

        if (shouldRecalculate) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                attachInputListeners();   // new inputs (if added)
                recalculateAllStudents(); // recheck all students (if column/activity deleted)
            }, 100); // wait 100ms to avoid spamming recalculations
        }
    });

    observer.observe(document.querySelector('table'), { childList: true, subtree: true });
});
</script>
@endsection
