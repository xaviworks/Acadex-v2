@php
    $hasData = count($students) > 0 && count($activities) > 0;
@endphp

<div class="table-responsive shadow rounded-lg overflow-hidden">
    @if ($hasData)
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                    <th style="min-width: 220px;" class="px-3 py-2">Student</th>
                    @foreach ($activities as $activity)
                        <th class="text-center px-3 py-2">
                            <div class="fw-semibold">{{ ucfirst($activity->type) }}</div>
                            <div class="text-muted small">{{ $activity->title }} ({{ $activity->number_of_items }} pts)</div>
                        </th>
                    @endforeach
                    <th class="text-center px-3 py-2">Term Grade</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach ($students as $student)
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-900">
                            {{ $student->first_name }} {{ $student->last_name }}
                        </td>
                        @foreach ($activities as $activity)
                            @php
                                $score = $scores[$student->id][$activity->id] ?? null;
                            @endphp
                            <td class="px-2 py-2 text-center">
                                <input
                                    type="number"
                                    class="form-control text-center grade-input rounded"
                                    name="scores[{{ $student->id }}][{{ $activity->id }}]"
                                    value="{{ $score !== null ? (int) $score : '' }}"
                                    min="0"
                                    max="{{ $activity->number_of_items }}"
                                    step="1"
                                    placeholder="–"
                                    title="Max: {{ $activity->number_of_items }}"
                                    data-student="{{ $student->id }}"
                                    data-activity="{{ $activity->id }}"
                                    style="width: 80px;"
                                >
                            </td>
                        @endforeach
                        <td class="px-3 py-2 text-center fw-bold {{ isset($termGrades[$student->id]) ? 'text-success' : 'text-muted' }}">
                            {{ $termGrades[$student->id] ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning text-center rounded-3 m-0 py-4">
            No students or activities found for {{ ucfirst($term) }}.
        </div>
    @endif
</div>

@if ($hasData)
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success px-5 py-2 shadow-sm rounded">
            Save Grades
        </button>
    </div>
@endif
