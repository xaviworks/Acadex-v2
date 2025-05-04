@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Final Grades</h1>

    {{-- Subject Selection --}}
    <form method="GET" action="{{ route('instructor.final-grades.index') }}">
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

    {{-- Generate Final Grades Button --}}
    @if(request('subject_id') && empty($finalData))
        <form method="POST" action="{{ route('instructor.final-grades.generate') }}" class="mb-6">
            @csrf
            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Generate Final Grades
            </button>
        </form>
    @endif

    {{-- Final Grades Table --}}
    @if(!empty($finalData) && count($finalData) > 0)
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
                        <th class="p-3 text-center border">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($finalData as $data)
                        <tr class="hover:bg-gray-100">
                            <td class="p-3 border">
                                {{ $data['student']->first_name }} {{ $data['student']->last_name }}
                            </td>
                            <td class="p-3 text-center border">
                                {{ isset($data['prelim']) ? number_format($data['prelim'], 2) : '-' }}
                            </td>
                            <td class="p-3 text-center border">
                                {{ isset($data['midterm']) ? number_format($data['midterm'], 2) : '-' }}
                            </td>
                            <td class="p-3 text-center border">
                                {{ isset($data['prefinal']) ? number_format($data['prefinal'], 2) : '-' }}
                            </td>
                            <td class="p-3 text-center border">
                                {{ isset($data['final']) ? number_format($data['final'], 2) : '-' }}
                            </td>
                            <td class="p-3 text-center border font-bold text-indigo-600">
                                {{ isset($data['final_average']) ? number_format($data['final_average'], 2) : '-' }}
                            </td>
                            <td class="p-3 text-center border">
                                {{ $data['remarks'] ?? '-' }}
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
