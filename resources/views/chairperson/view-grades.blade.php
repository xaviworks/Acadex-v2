@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">View Students' Final Grades</h1>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('chairperson.viewGrades') }}" class="flex gap-6 mb-6">
        {{-- Academic Period --}}
        <div>
            <label class="block text-sm font-medium mb-2">Academic Period:</label>
            <select name="academic_period_id" class="border rounded px-3 py-2 w-72" onchange="this.form.submit()">
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
            <select name="year_level" class="border rounded px-3 py-2 w-64" onchange="this.form.submit()">
                <option value="">-- All Year Levels --</option>
                @foreach([1,2,3,4,5] as $year)
                    <option value="{{ $year }}" {{ request('year_level') == $year ? 'selected' : '' }}>
                        {{ $year }}{{ $year == 1 ? 'st' : ($year == 2 ? 'nd' : ($year == 3 ? 'rd' : 'th')) }} Year
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Students Table --}}
    @if($students->count())
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left font-semibold">Student Name</th>
                        <th class="p-3 text-center font-semibold">Prelim</th>
                        <th class="p-3 text-center font-semibold">Midterm</th>
                        <th class="p-3 text-center font-semibold">Prefinal</th>
                        <th class="p-3 text-center font-semibold">Final</th>
                        <th class="p-3 text-center font-semibold">Final Average</th>
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
                            $average = $hasAll ? number_format(($prelim + $midterm + $prefinal + $final) / 4, 2) : '-';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="p-3 text-center border">{{ $prelim !== null ? number_format($prelim, 2) : '-' }}</td>
                            <td class="p-3 text-center border">{{ $midterm !== null ? number_format($midterm, 2) : '-' }}</td>
                            <td class="p-3 text-center border">{{ $prefinal !== null ? number_format($prefinal, 2) : '-' }}</td>
                            <td class="p-3 text-center border">{{ $final !== null ? number_format($final, 2) : '-' }}</td>
                            <td class="p-3 text-center border font-bold text-indigo-600">{{ $average }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center text-gray-500 mt-8">
            No students found under your department and course.
        </div>
    @endif
</div>
@endsection
