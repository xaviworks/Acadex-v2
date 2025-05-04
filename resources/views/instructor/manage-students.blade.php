@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="text-2xl font-bold mb-4">🎓 Manage Students</h1>

    {{-- Subject Selection --}}
    <form method="GET" action="{{ route('instructor.students.index') }}" class="mb-4">
        <label class="form-label fw-medium mb-1">Select Subject</label>
        <select name="subject_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- Select Subject --</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->subject_code }} - {{ $subject->subject_description }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Add Student Button --}}
    <div class="mb-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
            + Enroll Student
        </button>
    </div>

    {{-- Students Table --}}
    @if($students)
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </td>
                                <td class="text-end">
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmDropModal"
                                            data-student-id="{{ $student->id }}"
                                            data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                        Drop
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">No students found for this subject.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Enroll Student Modal --}}
<div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('instructor.students.store') }}">
            @csrf
            <div class="modal-content shadow-sm border-0 rounded-3">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="enrollStudentModalLabel">📥 Enroll New Student</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year Level <span class="text-danger">*</span></label>
                            <select name="year_level" class="form-select" required>
                                <option value="">-- Select Year Level --</option>
                                @foreach([1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'] as $level => $label)
                                    <option value="{{ $level }}">{{ $label }} Year</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assign Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->subject_code }} - {{ $subject->subject_description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Course <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select" required>
                                <option value="">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->course_code }} - {{ $course->course_description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enroll Student</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Drop Confirmation Modal --}}
<div class="modal fade" id="confirmDropModal" tabindex="-1" aria-labelledby="confirmDropModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="dropStudentForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDropModalLabel">⚠ Confirm Drop</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to drop <strong id="studentNamePlaceholder">this student</strong> from the subject?
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Drop</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Toast Message --}}
@if(session('success') || session('dropped'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div class="toast show align-items-center text-bg-{{ session('success') ? 'success' : 'danger' }} border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') ?? session('dropped') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropModal = document.getElementById('confirmDropModal');
    dropModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const studentId = button.getAttribute('data-student-id');
        const studentName = button.getAttribute('data-student-name');
        const form = dropModal.querySelector('#dropStudentForm');
        const placeholder = dropModal.querySelector('#studentNamePlaceholder');

        form.action = `/instructor/students/${studentId}/drop`;
        placeholder.textContent = studentName;
    });
});
</script>
@endpush
