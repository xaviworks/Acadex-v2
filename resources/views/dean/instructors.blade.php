@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Instructors in Department</h1>

        @if($instructors->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                No instructors found under your department.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($instructors as $instructor)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $instructor->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $instructor->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
