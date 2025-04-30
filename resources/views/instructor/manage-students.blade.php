@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Manage Students</h1>

    {{-- Subject Selection --}}
    <form method="GET" action="{{ route('instructor.manageStudents') }}" class="mb-6">
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
        <a href="{{ route('instructor.addStudentForm') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
            Add Student
        </a>
    </div>

    {{-- Students Table --}}
    @if($students)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Student Name
                        </th>
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
                                <form method="POST" action="{{ route('instructor.dropStudent', $student->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Drop
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                                No students found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
