@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">
        <i class="bi bi-card-checklist text-success me-2"></i>
        View Grades
    </h1>

    {{-- Course Selection --}}
    <form method="GET" action="{{ route('dean.grades') }}">
        <div class="mb-6">
            <label class="form-label fw-medium mb-1">Select Course:</label>
            <select name="course_id" class="form-select w-auto shadow-sm" onchange="this.form.submit()">
                <option value="">-- Choose Course --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }} - {{ $course->course_description }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Students and Grades --}}
    @if($students->count())
        <div class="bg-white shadow-lg rounded-4 overflow-x-auto">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student Name</th>
                        <th class="text-center">Prelim</th>
                        <th class="text-center">Midterm</th>
                        <th class="text-center">Prefinal</th>
                        <th class="text-center">Final</th>
                        <th class="text-center">Final Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        @php
                            $termGrades = $student->termGrades->keyBy('term_id');
                            $prelim = $termGrades[1]->term_grade ?? null;
                            $midterm = $termGrades[2]->term_grade ?? null;
                            $prefinal = $termGrades[3]->term_grade ?? null;
                            $final = $termGrades[4]->term_grade ?? null;

                            $hasAll = !is_null($prelim) && !is_null($midterm) && !is_null($prefinal) && !is_null($final);
                            $average = $hasAll ? number_format(($prelim + $midterm + $prefinal + $final) / 4, 2) : null;
                            $averageClass = is_numeric($average)
                                ? ($average >= 75 ? 'text-success' : 'text-danger')
                                : 'text-muted';
                        @endphp
                        <tr class="hover:bg-light">
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="text-center">{{ $prelim !== null ? number_format($prelim, 2) : '-' }}</td>
                            <td class="text-center">{{ $midterm !== null ? number_format($midterm, 2) : '-' }}</td>
                            <td class="text-center">{{ $prefinal !== null ? number_format($prefinal, 2) : '-' }}</td>
                            <td class="text-center">{{ $final !== null ? number_format($final, 2) : '-' }}</td>
                            <td class="text-center fw-semibold {{ $averageClass }}">
                                {{ $average ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning text-center rounded-4 mt-5">
            No students or grades found for the selected course.
        </div>
    @endif
</div>
@endsection
