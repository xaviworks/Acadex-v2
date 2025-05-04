@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">
        <i class="bi bi-people-fill text-success me-2"></i>
        Students Grouped by Year Level
    </h1>

    @if($students->isEmpty())
        <div class="bg-warning bg-opacity-25 text-warning border border-warning px-4 py-3 rounded-4 shadow-sm">
            No students found under your department and course.
        </div>
    @else
        <div class="bg-white shadow-lg rounded-4 overflow-x-auto">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th class="text-center">Year Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr class="hover:bg-light">
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td>{{ $student->course->course_code ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success fw-semibold px-3 py-2 rounded-pill">
                                    {{ $student->formatted_year_level }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
