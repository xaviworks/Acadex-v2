@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Courses</h1>
    <!-- Button to open modal -->
    <button type="button" class="bg-indigo-500 text-white px-4 py-2 rounded" data-bs-toggle="modal" data-bs-target="#createCourseModal">
        Add Course
    </button>

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
                        <td class="py-2">{{ $course->course_description }}</td>
                        <td class="py-2">{{ $course->department->department_description ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCourseModalLabel">Create Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.storeCourse') }}" class="space-y-4">
                @csrf
                <div class="modal-body">
                    <div>
                        <label class="block font-semibold">Course Code:</label>
                        <input type="text" name="course_code" class="w-full border px-3 py-2 rounded" required>
                    </div>

                    <div>
                        <label class="block font-semibold">Course Description:</label>
                        <input type="text" name="course_description" class="w-full border px-3 py-2 rounded" required>
                    </div>

                    <div>
                        <label class="block font-semibold">Department:</label>
                        <select name="department_id" class="w-full border px-3 py-2 rounded" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->department_description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
