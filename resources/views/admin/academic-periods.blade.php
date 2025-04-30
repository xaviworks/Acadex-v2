@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Academic Periods</h1>
    <a href="{{ route('admin.createAcademicPeriod') }}" class="bg-indigo-500 text-white px-4 py-2 rounded">Add Academic Period</a>

    <div class="mt-6">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2">ID</th>
                    <th class="py-2">Academic Year</th>
                    <th class="py-2">Semester</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periods as $period)
                    <tr>
                        <td class="py-2">{{ $period->id }}</td>
                        <td class="py-2">{{ $period->academic_year }}</td> <!-- fixed -->
                        <td class="py-2">{{ ucfirst($period->semester) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
