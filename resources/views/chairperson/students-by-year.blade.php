@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Students Grouped by Year Level</h1>

    @if($students->isEmpty())
        <div class="bg-blue-100 text-blue-800 p-4 rounded">
            No students found under your department and course.
        </div>
    @else
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Student Name</th>
                        <th class="px-4 py-2 text-left font-semibold">Course</th>
                        <th class="px-4 py-2 text-center font-semibold">Year Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="px-4 py-2">{{ $student->course->course_code ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center">{{ $student->formatted_year_level }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
