@php
    $hasData = count($students) > 0 && count($activities) > 0;
    $maxRows = 10; // Limit to 10 rows
@endphp

<div class="shadow-lg rounded-4 overflow-hidden border">
    @if ($hasData)
        <div class="table-responsive" style="overflow-x: auto;">
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-sm">
                        <tr>
                            <th style="min-width: 220px;" class="px-3 py-2">Student</th>
                            @foreach ($activities as $activity)
                                <th class="text-center px-3 py-2" style="min-width: 180px;">
                                    <div class="fw-semibold">{{ ucfirst($activity->type) }}</div>
                                    <div class="text-muted small">{{ $activity->title }} ({{ $activity->number_of_items }} pts)</div>
                                </th>
                            @endforeach
                            <th class="text-center px-3 py-2" style="min-width: 120px;">{{ ucfirst($term) }} Grade</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm" id="studentTableBody">
                        @foreach ($students->take($maxRows) as $student) <!-- Limit rows to 10 -->
                            <tr class="student-row">
                                <td class="px-3 py-2 fw-medium text-dark" style="min-width: 220px;">
                                    {{ $student->last_name }}, {{ $student->first_name }} 
                                    @if($student->middle_name)
                                        {{ strtoupper(substr($student->middle_name, 0, 1)) }}.
                                    @endif
                                </td>

                                @foreach ($activities as $activity)
                                    @php
                                        $score = $scores[$student->id][$activity->id] ?? null;
                                    @endphp
                                    <td class="px-2 py-2 text-center" style="min-width: 180px;">
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
                                    // Ensure the grade is an integer and rounded
                                    $grade = $grade !== null ? round($grade) : null;
                                    $gradeClass = is_numeric($grade)
                                        ? ($grade >= 75 ? 'text-success' : 'text-danger')
                                        : 'text-muted';
                                @endphp
                                
                                <td class="px-3 py-2 text-center fw-semibold {{ $gradeClass }}" style="min-width: 120px;">
                                    {{ $grade !== null ? $grade : '-' }}
                                </td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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

<!-- JavaScript for Client-Side Filtering -->
<script>
    document.getElementById('studentSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.student-row');
        
        rows.forEach(function(row) {
            const studentName = row.querySelector('td').textContent.toLowerCase();
            if (studentName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
