@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Courses</h1>
    <a href="{{ route('admin.createCourse') }}" class="bg-indigo-500 text-white px-4 py-2 rounded">Add Course</a>

    <div class="mt-6">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2">ID</th>
                    <th class="py-2">Course Description</th>
                    <th class="py-2">Department</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr>
                        <td class="py-2">{{ $course->id }}</td>
                        <td class="py-2">{{ $course->course_description }}</td> <!-- fixed -->
                        <td class="py-2">{{ $course->department->department_description ?? 'N/A' }}</td> <!-- fixed -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
