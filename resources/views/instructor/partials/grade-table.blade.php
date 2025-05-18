@php
    $hasData = count($students) > 0 && count($activities) > 0;
    $maxRows = 10; // Limit to 10 rows
@endphp

@if ($hasData)
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="input-group shadow-sm" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" 
                    id="studentSearch" 
                    class="form-control border-start-0 ps-0" 
                    placeholder="Search student name..."
                    aria-label="Search student">
            </div>
        </div>
        <div class="text-muted small">
            <i class="bi bi-info-circle me-1"></i>
            <span id="studentCount">{{ count($students) }}</span> students
        </div>
    </div>
@endif

<div class="shadow-lg rounded-4 overflow-hidden border">
    @if ($hasData)
        <div class="table-responsive">
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover align-middle mb-0 small">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="min-width: 180px; width: 180px;" class="px-2 py-1 bg-white">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-badge me-2 text-muted"></i>
                                    <span>Student</span>
                                </div>
                            </th>
                            @foreach ($activities as $activity)
                                <th class="text-center px-1 py-1 bg-white" style="min-width: 100px; width: 100px;">
                                    <div style="color: #198754;" class="fw-semibold">{{ ucfirst($activity->type) }}</div>
                                    <div class="text-muted small">{{ $activity->title }}</div>
                                    <div class="mt-1">
                                        <input type="number"
                                            class="form-control form-control-sm text-center items-input"
                                            value="{{ $activity->number_of_items }}"
                                            min="1"
                                            data-activity-id="{{ $activity->id }}"
                                            style="width: 65px; margin: 0 auto;"
                                            title="Number of Items"
                                            placeholder="Items">
                                    </div>
                                </th>
                            @endforeach
                            <th class="text-center px-1 py-1 bg-white" style="min-width: 90px; width: 90px;">
                                <div class="fw-semibold" style="color: #198754;">{{ ucfirst($term) }} Grade</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        @foreach ($students->take($maxRows) as $student)
                            <tr class="student-row">
                                <td class="px-2 py-1 fw-medium text-dark" style="width: 180px;">
                                    <div class="text-truncate" title="{{ $student->last_name }}, {{ $student->first_name }} @if($student->middle_name) {{ strtoupper(substr($student->middle_name, 0, 1)) }}. @endif">
                                        {{ $student->last_name }}, {{ $student->first_name }} 
                                        @if($student->middle_name)
                                            {{ strtoupper(substr($student->middle_name, 0, 1)) }}.
                                        @endif
                                    </div>
                                </td>

                                @foreach ($activities as $activity)
                                    @php
                                        $score = $scores[$student->id][$activity->id] ?? null;
                                    @endphp
                                    <td class="px-1 py-1 text-center" style="width: 100px;">
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
                                            style="width: 65px; margin: 0 auto;"
                                        >
                                    </td>
                                @endforeach
                                @php
                                    $grade = $termGrades[$student->id] ?? null;
                                    if ($grade !== null && is_numeric($grade)) {
                                        $grade = (int) round($grade);
                                    } else {
                                        $grade = null;
                                    }
                                    
                                    // Enhanced grade styling
                                    if ($grade !== null) {
                                        if ($grade >= 75) {
                                            $gradeClass = 'bg-success-subtle border-success';
                                            $textClass = 'text-success';
                                            $icon = 'bi-check-circle-fill';
                                        } else {
                                            $gradeClass = 'bg-danger-subtle border-danger';
                                            $textClass = 'text-danger';
                                            $icon = 'bi-x-circle-fill';
                                        }
                                    } else {
                                        $gradeClass = 'bg-secondary-subtle border-secondary';
                                        $textClass = 'text-secondary';
                                        $icon = 'bi-dash-circle';
                                    }
                                @endphp
                                
                                <td class="px-2 py-1 text-center align-middle" style="width: 90px;">
                                    <div class="d-inline-block border rounded-2 {{ $gradeClass }} position-relative" 
                                         style="min-width: 65px; padding: 6px 8px;">
                                        <div class="position-absolute top-50 start-0 translate-middle-y {{ $textClass }}" 
                                             style="margin-left: 6px;">
                                            <i class="bi {{ $icon }} small"></i>
                                        </div>
                                        <span class="fw-medium {{ $textClass }}" style="font-size: 0.95rem; margin-left: 8px;">
                                            {{ $grade !== null ? $grade : '–' }}
                                        </span>
                                    </div>
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
    <div class="text-end mt-4 d-flex justify-content-end align-items-center">
        <div id="unsavedNotificationContainer"></div>
        <button type="submit" id="saveGradesBtn" class="btn btn-success px-5 py-2 shadow-sm rounded-3">
            <i class="bi bi-save me-2"></i>Save Grades
        </button>
    </div>
@endif

<!-- JavaScript for Client-Side Filtering -->
<script>
    document.getElementById('studentSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.student-row');
        let visibleCount = 0;
        
        rows.forEach(function(row) {
            const studentName = row.querySelector('td').textContent.toLowerCase();
            const isVisible = studentName.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Update student count
        document.getElementById('studentCount').textContent = visibleCount;
    });
</script>
