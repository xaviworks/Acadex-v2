@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">
        <i class="bi bi-person-lines-fill text-success me-2"></i>
        Manage Instructors
    </h1>

    {{-- Add Button --}}
    <div class="mb-6">
        <button type="button"
            class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#addInstructorModal">
            <i class="bi bi-person-plus-fill"></i> Add New Instructor
        </button>
    </div>

    {{-- Instructors Table --}}
    @if($instructors->isEmpty())
        <div class="alert alert-warning rounded shadow-sm">
            No instructors found.
        </div>
    @else
        <div class="table-responsive bg-white shadow rounded-4">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instructors as $instructor)
                        <tr>
                            <td>{{ $instructor->name }}</td>
                            <td>{{ $instructor->email }}</td>
                            <td class="text-center">
                                @if($instructor->is_active)
                                    <span class="badge bg-success-subtle text-success fw-semibold px-3 py-2 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-2 rounded-pill">Deactivated</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($instructor->is_active)
                                    <button type="button"
                                        class="btn btn-sm btn-danger shadow-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeactivateModal"
                                        data-instructor-id="{{ $instructor->id }}"
                                        data-instructor-name="{{ $instructor->name }}">
                                        <i class="bi bi-person-x-fill me-1"></i> Deactivate
                                    </button>
                                @else
                                    <span class="text-muted">No Actions</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Add Instructor Modal --}}
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-labelledby="addInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('chairperson.storeInstructor') }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #4da674, #3d865f);">
                    <h5 class="modal-title" id="addInstructorModalLabel">📋 Add New Instructor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Instructor</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Confirm Deactivation Modal --}}
<div class="modal fade" id="confirmDeactivateModal" tabindex="-1" aria-labelledby="confirmDeactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deactivateForm" method="POST">
            @csrf
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeactivateModalLabel">⚠ Confirm Deactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to deactivate <strong id="instructorName"></strong>?
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Deactivate</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Scripts --}}
@push('scripts')
<script>
    const confirmModal = document.getElementById('confirmDeactivateModal');
    confirmModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const instructorId = button.getAttribute('data-instructor-id');
        const instructorName = button.getAttribute('data-instructor-name');

        const form = document.getElementById('deactivateForm');
        const namePlaceholder = document.getElementById('instructorName');

        form.action = `/chairperson/instructors/${instructorId}/deactivate`;
        namePlaceholder.textContent = instructorName;
    });

    document.addEventListener('DOMContentLoaded', () => {
        @if ($errors->any())
            const addModal = new bootstrap.Modal(document.getElementById('addInstructorModal'));
            addModal.show();
        @endif
    });
</script>
@endpush
@endsection
