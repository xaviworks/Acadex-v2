@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">View Grades</h1>

    {{-- Course Selection --}}
    <form method="GET" action="{{ route('dean.grades') }}">
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Select Course:</label>
            <select name="course_id" class="border rounded px-3 py-2 w-64" onchange="this.form.submit()">
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
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3 text-left border">Student Name</th>
                        <th class="p-3 text-center border">Prelim</th>
                        <th class="p-3 text-center border">Midterm</th>
                        <th class="p-3 text-center border">Prefinal</th>
                        <th class="p-3 text-center border">Final</th>
                        <th class="p-3 text-center border">Final Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                        // Get term grades already loaded from relationship
                        $termGrades = $student->termGrades->keyBy('term_id'); 
                
                        $prelim = $termGrades[1]->term_grade ?? null;
                        $midterm = $termGrades[2]->term_grade ?? null;
                        $prefinal = $termGrades[3]->term_grade ?? null;
                        $final = $termGrades[4]->term_grade ?? null;
                
                        $hasCompleteGrades = !is_null($prelim) && !is_null($midterm) && !is_null($prefinal) && !is_null($final);
                        $average = $hasCompleteGrades ? number_format(($prelim + $midterm + $prefinal + $final) / 4, 2) : '-';
                    @endphp
                    <tr class="hover:bg-gray-100">
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
            No students or grades found.
        </div>
    @endif
</div>
@endsection
