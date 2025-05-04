@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        <i class="bi bi-bar-chart-fill text-success me-2"></i>
        Students' Final Grades
    </h1>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('chairperson.viewGrades') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Academic Period --}}
        <div>
            <label class="block text-sm font-medium mb-2">Academic Period:</label>
            <select name="academic_period_id"
                    class="form-select shadow-sm"
                    onchange="this.form.submit()">
                <option value="">-- All Periods --</option>
                @foreach($academicPeriods as $period)
                    <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                        {{ $period->academic_year }} - {{ ucfirst($period->semester) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Year Level --}}
        <div>
            <label class="block text-sm font-medium mb-2">Year Level:</label>
            <select name="year_level"
                    class="form-select shadow-sm"
                    onchange="this.form.submit()">
                <option value="">-- All Year Levels --</option>
                @foreach([1,2,3,4,5] as $year)
                    <option value="{{ $year }}" {{ request('year_level') == $year ? 'selected' : '' }}>
                        {{ $year }}{{ $year == 1 ? 'st' : ($year == 2 ? 'nd' : ($year == 3 ? 'rd' : 'th') ) }} Year
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Students Table --}}
    @if($students->count())
        <div class="bg-white shadow-lg rounded-4 overflow-x-auto">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>Student Name</th>
                        <th class="text-center">Prelim</th>
                        <th class="text-center">Midterm</th>
                        <th class="text-center">Prefinal</th>
                        <th class="text-center">Final</th>
                        <th class="text-center text-success">Final Average</th>
                        <th class="text-center">Remarks</th>
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

                            $remarks = $average !== null ? ($average >= 75 ? 'Passed' : 'Failed') : null;
                        @endphp
                        <tr class="hover:bg-light">
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="text-center">{{ $prelim !== null ? number_format($prelim, 2) : '-' }}</td>
                            <td class="text-center">{{ $midterm !== null ? number_format($midterm, 2) : '-' }}</td>
                            <td class="text-center">{{ $prefinal !== null ? number_format($prefinal, 2) : '-' }}</td>
                            <td class="text-center">{{ $final !== null ? number_format($final, 2) : '-' }}</td>
                            <td class="text-center fw-semibold text-success">
                                {{ $average !== null ? $average : '-' }}
                            </td>
                            <td class="text-center">
                                @if($remarks === 'Passed')
                                    <span class="badge bg-success-subtle text-success fw-medium px-3 py-2 rounded-pill">Passed</span>
                                @elseif($remarks === 'Failed')
                                    <span class="badge bg-danger-subtle text-danger fw-medium px-3 py-2 rounded-pill">Failed</span>
                                @else
                                    <span class="text-muted">–</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center text-muted mt-8 bg-warning bg-opacity-25 border border-warning px-6 py-4 rounded-4">
            No students found under your department and course.
        </div>
    @endif
</div>
@endsection
