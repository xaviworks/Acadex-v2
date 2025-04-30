@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Departments</h1>

    <a href="{{ route('admin.createDepartment') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded">
        Add Department
    </a>

    <div class="mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3 text-left border">ID</th>
                        <th class="p-3 text-left border">Name</th>
                        <th class="p-3 text-left border">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $department)
                        <tr class="hover:bg-gray-100">
                            <td class="p-3 border">{{ $department->id }}</td>
                            <td class="p-3 border">{{ $department->department_description }}</td>
                            <td class="p-3 border">{{ $department->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
