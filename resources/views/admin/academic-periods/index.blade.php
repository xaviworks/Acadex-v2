@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Manage Academic Periods</h1>

    {{-- Generate Button --}}
    <form method="POST" action="{{ route('admin.academicPeriods.generate') }}">
        @csrf
        <button type="submit" class="mb-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            Generate New Academic Period
        </button>
    </form>

    {{-- Periods Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left border">Academic Year</th>
                    <th class="p-3 text-left border">Semester</th>
                    <th class="p-3 text-center border">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periods as $period)
                    <tr class="hover:bg-gray-100">
                        <td class="p-3 border">{{ $period->academic_year }}</td>
                        <td class="p-3 border">{{ ucfirst($period->semester) }}</td>
                        <td class="p-3 border text-center">{{ $period->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
