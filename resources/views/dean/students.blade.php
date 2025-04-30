@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Students in Department</h1>

        @if($students->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                No students found under your department.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $student->course->course_code ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $student->year_level ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
