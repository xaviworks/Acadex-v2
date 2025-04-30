@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Final Grades</h1>

    {{-- Subject Selection --}}
    <form method="GET" action="{{ route('instructor.finalGrades') }}">
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Select Subject:</label>
            <select name="subject_id" class="border rounded px-3 py-2 w-64" onchange="this.form.submit()">
                <option value="">-- Choose Subject --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->subject_code }} - {{ $subject->subject_description }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Final Grades Table --}}
    @if(!empty($finalData))
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
                    @foreach($finalData as $data)
                        @php
                            $prelim = $data['prelim'] ?? null;
                            $midterm = $data['midterm'] ?? null;
                            $prefinal = $data['prefinal'] ?? null;
                            $final = $data['final'] ?? null;

                            // Only calculate average if ALL are present
                            $hasAllTerms = !is_null($prelim) && !is_null($midterm) && !is_null($prefinal) && !is_null($final);
                        @endphp
                        <tr class="hover:bg-gray-100">
                            <td class="p-3 border">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</td>
                            <td class="p-3 text-center border">{{ $prelim !== null ? number_format($prelim, 2) : '-' }}</td>
                            <td class="p-3 text-center border">{{ $midterm !== null ? number_format($midterm, 2) : '-' }}</td>
                            <td class="p-3 text-center border">{{ $prefinal !== null ? number_format($prefinal, 2) : '-' }}</td>
                            <td class="p-3 text-center border">{{ $final !== null ? number_format($final, 2) : '-' }}</td>
                            <td class="p-3 text-center border font-bold text-indigo-600">
                                {{ $hasAllTerms ? number_format(($prelim + $midterm + $prefinal + $final) / 4, 2) : '-' }}
                            </td>
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
