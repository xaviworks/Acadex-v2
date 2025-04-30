@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Subjects</h1>

    <a href="{{ route('admin.createSubject') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded">
        Add Subject
    </a>

    <div class="mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3 text-left border">ID</th>
                        <th class="p-3 text-left border">Code</th>
                        <th class="p-3 text-left border">Description</th> <!-- now Description instead of Name -->
                        <th class="p-3 text-left border">Units</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                        <tr class="hover:bg-gray-100">
                            <td class="p-3 border">{{ $subject->id }}</td>
                            <td class="p-3 border">{{ $subject->subject_code }}</td>
                            <td class="p-3 border">{{ $subject->subject_description ?? '-' }}</td> <!-- use subject_description -->
                            <td class="p-3 border">{{ $subject->units }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
