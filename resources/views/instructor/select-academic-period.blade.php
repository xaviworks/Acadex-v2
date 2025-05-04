@extends('layouts.blank')

@section('content')
<div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-2xl font-semibold mb-4">Select Academic Period</h2>

    <form method="POST" action="{{ route('set.academicPeriod') }}">
        @csrf

        <div class="mb-4">
            <label for="academic_period_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Academic Period
            </label>
            <select name="academic_period_id" id="academic_period_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200"
                required>
                <option value="">-- Select Academic Period --</option>
                @foreach($periods as $period)
                    <option value="{{ $period->id }}">
                        {{ $period->academic_year }} - {{ $period->semester }} Semester
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
                Proceed
            </button>
        </div>
    </form>
</div>
@endsection
