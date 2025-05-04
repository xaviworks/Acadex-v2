@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Manage Students</h1>

    {{-- Subject Selection --}}
    <form method="GET" action="{{ route('instructor.students.index') }}" class="mb-6">
        <label class="block mb-2 font-medium">Select Subject:</label>
        <select name="subject_id" class="border rounded px-3 py-2 w-full" onchange="this.form.submit()">
            <option value="">-- Select Subject --</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->subject_code }} - {{ $subject->subject_description }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Add Student Button --}}
    <div class="mb-4">
        <button onclick="openModal('add-student-modal')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
            Add Student
        </button>
    </div>

    {{-- Students Table --}}
    @if($students)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                {{ $student->first_name }} {{ $student->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('instructor.students.drop', $student->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Drop</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-500">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Modal --}}
<div id="add-student-modal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 hidden" style="display: none;">
    <div class="bg-white w-full max-w-2xl mx-auto rounded-lg shadow-lg p-6 relative">
        {{-- Close Button --}}
        <button onclick="closeModal('add-student-modal')" class="absolute top-2 right-3 text-gray-600 text-xl font-bold">&times;</button>

        {{-- Form --}}
        <h2 class="text-xl font-semibold mb-4">Enroll New Student</h2>

        <form action="{{ route('instructor.students.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- First Name --}}
            <div>
                <label class="block font-medium mb-1">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" class="w-full border px-3 py-2 rounded" required>
            </div>

            {{-- Last Name --}}
            <div>
                <label class="block font-medium mb-1">Last Name <span class="text-red-500">*</span></label>
                <input type="text" name="last_name" class="w-full border px-3 py-2 rounded" required>
            </div>

            {{-- Year Level --}}
            <div>
                <label class="block font-medium mb-1">Year Level <span class="text-red-500">*</span></label>
                <select name="year_level" class="w-full border px-3 py-2 rounded" required>
                    <option value="">-- Select Year Level --</option>
                    @foreach([1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'] as $level => $label)
                        <option value="{{ $level }}">{{ $label }} Year</option>
                    @endforeach
                </select>
            </div>

            {{-- Assign Subject --}}
            <div>
                <label class="block font-medium mb-1">Assign Subject <span class="text-red-500">*</span></label>
                <select name="subject_id" class="w-full border px-3 py-2 rounded" required>
                    <option value="">-- Select Subject --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">
                            {{ $subject->subject_code }} - {{ $subject->subject_description }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Assign Course --}}
            <div>
                <label class="block font-medium mb-1">Assign Course <span class="text-red-500">*</span></label>
                <select name="course_id" class="w-full border px-3 py-2 rounded" required>
                    <option value="">-- Select Course --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->course_code }} - {{ $course->course_description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                    Enroll Student
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Toggle Script --}}
@push('scripts')
<script>
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'flex';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
}
</script>
@endpush

@endsection
