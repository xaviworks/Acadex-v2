@php
    $hasData = count($students) > 0 && count($activities) > 0;
@endphp

<div class="table-responsive shadow-lg rounded-4 overflow-hidden">
    @if ($hasData)
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-sm">
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
                        <td class="px-3 py-2 fw-medium text-dark">
                            {{ $student->first_name }} {{ $student->last_name }}
                        </td>

                        @foreach ($activities as $activity)
                            @php
                                $score = $scores[$student->id][$activity->id] ?? null;
                            @endphp
                            <td class="px-2 py-2 text-center">
                                <input
                                    type="number"
                                    class="form-control form-control-sm text-center grade-input"
                                    name="scores[{{ $student->id }}][{{ $activity->id }}]"
                                    value="{{ $score !== null ? (int) $score : '' }}"
                                    min="0"
                                    max="{{ $activity->number_of_items }}"
                                    step="1"
                                    placeholder="–"
                                    title="Max: {{ $activity->number_of_items }}"
                                    data-student="{{ $student->id }}"
                                    data-activity="{{ $activity->id }}"
                                    style="width: 80px; margin: 0 auto;"
                                >
                            </td>
                        @endforeach

                        @php
                            $grade = $termGrades[$student->id] ?? null;
                            $gradeClass = is_numeric($grade)
                                ? ($grade >= 75 ? 'text-success' : 'text-danger')
                                : 'text-muted';
                        @endphp

                        <td class="px-3 py-2 text-center fw-semibold {{ $gradeClass }}">
                            {{ $grade ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning text-center rounded-4 m-0 py-4">
            No students or activities found for <strong>{{ ucfirst($term) }}</strong>.
        </div>
    @endif
</div>

@if ($hasData)
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success px-5 py-2 shadow-sm rounded-3">
            <i class="bi bi-save me-2"></i>Save Grades
        </button>
    </div>
@endif
