@extends('layouts.blank')

@section('content')
<div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-4 shadow-lg p-5 mx-auto mt-5">
    <h2 class="text-2xl fw-bold text-gray-800 dark:text-gray-100 mb-4">
        <i class="bi bi-calendar-week me-2 text-success"></i> Select Academic Period
    </h2>

    <form method="POST" action="{{ route('set.academicPeriod') }}">
        @csrf

        <div class="mb-4">
            <label for="academic_period_id" class="form-label fw-medium text-gray-700 dark:text-gray-300">
                Academic Period
            </label>
            <select name="academic_period_id" id="academic_period_id"
                    class="form-select rounded shadow-sm focus:ring-2 focus:ring-green-300" required>
                <option value="">-- Select Academic Period --</option>
                @foreach($periods as $period)
                    <option value="{{ $period->id }}">
                        {{ $period->academic_year }} - {{ $period->semester }} Semester
                    </option>
                @endforeach
            </select>
        </div>

        <div class="text-end">
            <button type="submit"
                    class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm px-4 py-2">
                <i class="bi bi-arrow-right-circle-fill"></i> Proceed
            </button>
        </div>
    </form>
</div>
@endsection
