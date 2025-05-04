@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Assign Subjects to Instructors</h1>

    @if ($subjects->count())
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Subject Code</th>
                        <th class="px-4 py-3 text-left font-semibold">Description</th>
                        <th class="px-4 py-3 text-left font-semibold">Assigned Instructor</th>
                        <th class="px-4 py-3 text-center font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $subject->subject_code }}</td>
                            <td class="px-4 py-2">{{ $subject->subject_description }}</td>
                            <td class="px-4 py-2">
                                {{ $subject->instructor ? $subject->instructor->name : '—' }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if ($subject->instructor)
                                    <span class="text-gray-500 text-sm">Already Assigned</span>
                                @else
                                    <button
                                        onclick="openAssignModal({{ $subject->id }}, '{{ addslashes($subject->subject_code . ' - ' . $subject->subject_description) }}')"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow transition duration-150 ease-in-out">
                                        Assign
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center text-gray-500 mt-8">
            No subjects available for this academic period.
        </div>
    @endif
</div>

{{-- Assign Modal --}}
<div id="assignModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden justify-center items-center">
    <div class="bg-white w-full max-w-lg rounded shadow-lg p-6 relative">
        <button onclick="closeAssignModal()" class="absolute top-2 right-3 text-gray-600 text-xl font-bold">&times;</button>

        <h2 class="text-xl font-bold mb-4">Assign Instructor</h2>

        <form method="POST" action="{{ route('chairperson.storeAssignedSubject') }}">
            @csrf
            <input type="hidden" name="subject_id" id="modal_subject_id">

            <div class="mb-4">
                <label class="block font-medium mb-1">Subject</label>
                <input type="text" id="modal_subject_name" class="w-full border px-3 py-2 rounded bg-gray-100" disabled>
            </div>

            <div class="mb-6">
                <label class="block font-medium mb-1">Select Instructor</label>
                <select name="instructor_id" class="w-full border px-3 py-2 rounded" required>
                    <option value="">-- Choose Instructor --</option>
                    @foreach ($instructors as $instructor)
                        <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">
                    Assign
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAssignModal(subjectId, subjectName) {
        document.getElementById('modal_subject_id').value = subjectId;
        document.getElementById('modal_subject_name').value = subjectName;
        document.getElementById('assignModal').classList.remove('hidden');
        document.getElementById('assignModal').classList.add('flex');
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
        document.getElementById('assignModal').classList.remove('flex');
    }
</script>
@endpush
@endsection
