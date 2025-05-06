@extends('layouts.app')

@section('content')
<div class="container py-4" style="background-color: #EAF8E7;">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Upload Form --}}
    <form method="POST" action="{{ route('instructor.students.import.upload') }}" enctype="multipart/form-data" id="uploadForm" class="mb-3">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="file" class="form-label fw-semibold">Upload Excel File (.xlsx)</label>
                <input type="file" name="file" id="file" class="form-control form-control-sm border-success shadow-sm rounded-3" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                    <i class="bi bi-upload me-1"></i> Upload
                </button>
            </div>
        </div>
    </form>

    @php
        $listName = request('list_name');
        $compareSubjectId = request('compare_subject_id');
        $filteredReviewStudents = $listName ? $reviewStudents->where('list_name', $listName) : collect();
        $existingStudents = $compareSubjectId ? \App\Models\Subject::find($compareSubjectId)?->students()->where('students.is_deleted', 0)->get() : collect();
    @endphp

    <div class="card shadow-sm rounded-4 border-0 p-3">
        <div class="row g-0">
            {{-- Uploaded Students --}}
            <div class="col-md-6 pe-md-2 border-end">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-success">📥 Uploaded Students</span>
                    <select id="listFilter" class="form-select form-select-sm w-auto" name="list_name" onchange="filterList(this.value)">
                        <option value="">-- Select Uploaded List --</option>
                        @foreach ($reviewStudents->unique('list_name')->pluck('list_name') as $name)
                            <option value="{{ $name }}" {{ request('list_name') === $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 review-table">
                        <thead class="text-center table-success border-bottom border-2">
                            <tr class="align-middle text-uppercase small text-dark">
                                <th class="text-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th class="text-start ps-3">Full Name</th>
                                <th class="text-center">Course</th>
                                <th class="text-end pe-3">Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($filteredReviewStudents as $student)
                                <tr class="uploaded-row"
                                    data-full-name="{{ strtolower(trim($student->full_name)) }}"
                                    data-course="{{ trim($student->course->course_code ?? '') }}"
                                    data-year="{{ trim($student->formatted_year_level) }}">
                                    <td class="text-center">
                                        <input type="checkbox" name="selected_students[]" value="{{ $student->id }}" class="form-check-input student-checkbox">
                                    </td>
                                    <td class="text-start ps-3 py-1 student-name">{{ $student->full_name }}</td>
                                    <td class="text-center py-1 student-course">{{ $student->course->course_code ?? 'N/A' }}</td>
                                    <td class="text-end pe-3 py-1 student-year">{{ $student->formatted_year_level }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-2">No uploaded list selected.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Existing Enrolled Students --}}
            <div class="col-md-6 ps-md-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-secondary">👥 Existing Enrolled Students</span>
                    <select id="compareSubjectSelect" class="form-select form-select-sm w-auto">
                        <option value="">-- Select Subject to Compare --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('compare_subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_code }} - {{ $subject->subject_description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 review-table">
                        <thead class="text-center table-secondary border-bottom border-2">
                            <tr class="align-middle text-uppercase small text-dark">
                                <th class="text-start ps-3">Full Name</th>
                                <th class="text-center">Course</th>
                                <th class="text-end pe-3">Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($existingStudents as $student)
                                <tr class="enrolled-row"
                                    data-full-name="{{ strtolower(trim($student->full_name)) }}"
                                    data-course="{{ trim($student->course->course_code ?? '') }}"
                                    data-year="{{ trim($student->formatted_year_level) }}">
                                    <td class="text-start ps-3 py-1 student-name">{{ $student->full_name }}</td>
                                    <td class="text-center py-1 student-course">{{ $student->course->course_code ?? 'N/A' }}</td>
                                    <td class="text-end pe-3 py-1 student-year">{{ $student->formatted_year_level }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-2">No subject selected.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons Row --}}
    <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
        <button type="button" class="btn btn-outline-primary btn-sm px-4 py-2 me-auto" onclick="runCrossCheck()">
            <i class="bi bi-search me-1"></i> Cross Check Data
        </button>
        <a href="{{ route('instructor.students.import') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
        </a>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
            <i class="bi bi-check-circle me-1"></i> Confirm & Import to Selected Subject
        </button>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('instructor.students.import.confirm') }}" class="modal-content">
                @csrf
                <input type="hidden" name="list_name" value="{{ $listName }}">
                <input type="hidden" name="selected_student_ids" id="selectedStudentIds">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label for="subject_id" class="form-label">Select subject to import students:</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- Select Subject --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->subject_code }} - {{ $subject->subject_description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterList(selected) {
    const url = new URL(window.location.href);
    url.searchParams.set('list_name', selected);
    window.location.href = url.toString();
}

document.getElementById('compareSubjectSelect')?.addEventListener('change', function () {
    const url = new URL(window.location.href);
    url.searchParams.set('compare_subject_id', this.value);
    window.location.href = url.toString();
});

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                if (!cb.disabled) cb.checked = selectAll.checked;
            });
        });
    }
});

function extractNameParts(fullName) {
    const parts = fullName.split(' ').filter(Boolean);
    const first = parts[0] ?? '';
    const last = parts[parts.length - 1] ?? '';
    return (first + last).toLowerCase();
}

function runCrossCheck() {
    const uploadedRows = document.querySelectorAll('.uploaded-row');
    const enrolledRows = document.querySelectorAll('.enrolled-row');

    const enrolledData = [...enrolledRows].map(row => ({
        row,
        nameKey: extractNameParts(row.dataset.fullName || ''),
        course: row.dataset.course?.trim(),
        year: row.dataset.year?.trim(),
        nameCell: row.querySelector('.student-name'),
        courseCell: row.querySelector('.student-course'),
        yearCell: row.querySelector('.student-year')
    }));

    [...uploadedRows, ...enrolledRows].forEach(row => {
        row.classList.remove('bg-danger', 'bg-success', 'bg-opacity-10');
        row.querySelectorAll('td').forEach(cell => cell.classList.remove('text-danger', 'text-success'));
        const checkbox = row.querySelector('.student-checkbox');
        if (checkbox) checkbox.disabled = false;
    });

    uploadedRows.forEach(row => {
        const nameKey = extractNameParts(row.dataset.fullName || '');
        const course = row.dataset.course?.trim();
        const year = row.dataset.year?.trim();

        const nameCell = row.querySelector('.student-name');
        const courseCell = row.querySelector('.student-course');
        const yearCell = row.querySelector('.student-year');
        const checkbox = row.querySelector('.student-checkbox');

        let matched = false;

        enrolledData.forEach(e => {
            if (e.nameKey === nameKey && e.course === course && e.year === year) {
                row.classList.add('bg-danger', 'bg-opacity-10');
                [nameCell, courseCell, yearCell].forEach(el => el.classList.add('text-danger'));
                if (checkbox) checkbox.disabled = true;

                e.row.classList.add('bg-danger', 'bg-opacity-10');
                [e.nameCell, e.courseCell, e.yearCell].forEach(el => el.classList.add('text-danger'));
                matched = true;
            }
        });

        if (!matched) {
            row.classList.add('bg-success', 'bg-opacity-10');
            [nameCell, courseCell, yearCell].forEach(el => el.classList.add('text-success'));
        }
    });
}

// Collect selected checkboxes for modal form submission
const confirmForm = document.querySelector('#confirmModal form');
confirmForm?.addEventListener('submit', function () {
    const selected = [...document.querySelectorAll('.student-checkbox:checked')].map(cb => cb.value);
    document.getElementById('selectedStudentIds').value = selected.join(',');
});
</script>
@endpush