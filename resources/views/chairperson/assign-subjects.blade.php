@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Assign Subjects to Instructors</h1>

    @if ($subjects->count() > 0)
        <form method="POST" action="{{ route('chairperson.storeAssignedSubject') }}" class="space-y-6">
            @csrf

            {{-- Subject Selection --}}
            <div>
                <label class="block font-semibold mb-1">Select Subject:</label>
                <select name="subject_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Choose Subject --</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">
                            {{ $subject->subject_code }} - {{ $subject->subject_description }}
                            @if ($subject->instructor_id)
                                (Assigned)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Instructor Selection --}}
            <div>
                <label class="block font-semibold mb-1">Select Instructor:</label>
                <select name="instructor_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Choose Instructor --</option>
                    @foreach ($instructors as $instructor)
                        <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Submit Button --}}
            <div class="text-right">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded">
                    Assign Subject
                </button>
            </div>
        </form>
    @else
        <div class="text-center text-gray-500">
            No subjects available for assignment.
        </div>
    @endif
</div>
@endsection
