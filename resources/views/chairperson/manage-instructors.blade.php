@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Manage Instructors</h1>

    <div class="mb-6">
        <a href="{{ route('chairperson.createInstructor') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded">
            + Add New Instructor
        </a>
    </div>

    @if($instructors->isEmpty())
        <div class="bg-blue-100 text-blue-800 p-4 rounded">
            No instructors found.
        </div>
    @else
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Name</th>
                        <th class="px-4 py-2 text-left font-semibold">Email</th>
                        <th class="px-4 py-2 text-center font-semibold">Status</th>
                        <th class="px-4 py-2 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instructors as $instructor)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $instructor->name }}</td>
                            <td class="px-4 py-2">{{ $instructor->email }}</td>
                            <td class="px-4 py-2 text-center">
                                @if($instructor->is_active)
                                    <span class="inline-block bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded">Active</span>
                                @else
                                    <span class="inline-block bg-red-200 text-red-800 text-xs font-semibold px-2 py-1 rounded">Deactivated</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($instructor->is_active)
                                    <form action="{{ route('chairperson.deactivateInstructor', $instructor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate this instructor?');">
                                        @csrf
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded text-sm">
                                            Deactivate
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm">No Actions</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
