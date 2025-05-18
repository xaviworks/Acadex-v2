@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Department Chair Overview 👨‍💼</h2>
            <p class="text-muted mb-0">Monitor department performance and faculty management</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('chairperson.instructors') }}" class="btn btn-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-person-plus"></i> Manage Instructors
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4">
        @php
            $cards = [
                [
                    'label' => 'Total Instructors',
                    'icon' => 'bi bi-person-video3',
                    'value' => $countInstructors,
                    'color' => 'primary',
                    'trend' => 'Department faculty'
                ],
                [
                    'label' => 'Total Students',
                    'icon' => 'bi bi-mortarboard-fill',
                    'value' => $countStudents,
                    'color' => 'success',
                    'trend' => 'Enrolled this semester'
                ],
                [
                    'label' => 'Active Courses',
                    'icon' => 'bi bi-journal-text',
                    'value' => $countCourses,
                    'color' => 'info',
                    'trend' => 'Current offerings'
                ]
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-3 p-2 bg-{{ $card['color'] }}-subtle me-3">
                                <i class="{{ $card['icon'] }} text-{{ $card['color'] }} fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">{{ $card['label'] }}</h6>
                                <h3 class="fw-bold text-{{ $card['color'] }} mb-0">{{ $card['value'] }}</h3>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-arrow-right"></i> {{ $card['trend'] }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mt-4">
        {{-- Faculty Status Overview --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-semibold mb-1">
                                <i class="bi bi-people-fill me-2"></i>Faculty Status Overview
                            </h5>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Total Faculty Members: <span class="fw-bold">{{ $countInstructors }}</span>
                            </p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-pill" type="button" id="helpDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-question-circle"></i> Help
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-3 shadow-sm" aria-labelledby="helpDropdown" style="min-width: 280px;">
                                <li class="small">
                                    <div class="mb-2"><strong>Understanding Faculty Status:</strong></div>
                                    <div class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        <strong>Active:</strong> Currently teaching faculty
                                    </div>
                                    <div class="mb-2">
                                        <i class="bi bi-x-circle-fill text-danger me-1"></i>
                                        <strong>Inactive:</strong> On leave or deactivated
                                    </div>
                                    <div>
                                        <i class="bi bi-clock-fill text-warning me-1"></i>
                                        <strong>Pending:</strong> Awaiting verification
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- Overall Progress --}}
                        <div class="col-12 mb-2">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: {{ ($countActiveInstructors / $countInstructors) * 100 }}%" 
                                    title="Active: {{ $countActiveInstructors }}">
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" 
                                    style="width: {{ ($countInactiveInstructors / $countInstructors) * 100 }}%" 
                                    title="Inactive: {{ $countInactiveInstructors }}">
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" 
                                    style="width: {{ ($countUnverifiedInstructors / $countInstructors) * 100 }}%" 
                                    title="Pending: {{ $countUnverifiedInstructors }}">
                                </div>
                            </div>
                        </div>

                        {{-- Active Instructors --}}
                        <div class="col-md-4">
                            <div class="status-card active p-4 rounded-4 bg-success-subtle border border-success-subtle h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="badge bg-success-subtle text-success mb-2 px-3 py-2 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Active
                                        </span>
                                        <h3 class="fw-bold text-success mb-0">{{ $countActiveInstructors }}</h3>
                                    </div>
                                    <div class="status-percentage text-success">
                                        {{ number_format(($countActiveInstructors / $countInstructors) * 100, 1) }}%
                                    </div>
                                </div>
                                <p class="text-muted small mb-0">Currently teaching faculty members</p>
                            </div>
                        </div>

                        {{-- Inactive Instructors --}}
                        <div class="col-md-4">
                            <div class="status-card inactive p-4 rounded-4 bg-danger-subtle border border-danger-subtle h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="badge bg-danger-subtle text-danger mb-2 px-3 py-2 rounded-pill">
                                            <i class="bi bi-x-circle-fill me-1"></i> Inactive
                                        </span>
                                        <h3 class="fw-bold text-danger mb-0">{{ $countInactiveInstructors }}</h3>
                                    </div>
                                    <div class="status-percentage text-danger">
                                        {{ number_format(($countInactiveInstructors / $countInstructors) * 100, 1) }}%
                                    </div>
                                </div>
                                <p class="text-muted small mb-0">Faculty members on leave or deactivated</p>
                            </div>
                        </div>

                        {{-- Unverified Instructors --}}
                        <div class="col-md-4">
                            <div class="status-card pending p-4 rounded-4 bg-warning-subtle border border-warning-subtle h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="badge bg-warning-subtle text-warning mb-2 px-3 py-2 rounded-pill">
                                            <i class="bi bi-clock-fill me-1"></i> Pending
                                        </span>
                                        <h3 class="fw-bold text-warning mb-0">{{ $countUnverifiedInstructors }}</h3>
                                    </div>
                                    <div class="status-percentage text-warning">
                                        {{ number_format(($countUnverifiedInstructors / $countInstructors) * 100, 1) }}%
                                    </div>
                                </div>
                                <p class="text-muted small mb-0">New faculty members awaiting verification</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-semibold mb-4">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Quick Actions
                    </h5>
                    
                    <div class="d-flex flex-column gap-3 flex-grow-1 justify-content-between">
                        <a href="{{ route('chairperson.instructors') }}" class="btn btn-light text-start rounded-3 p-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 p-2 bg-success-subtle me-3">
                                    <i class="bi bi-person-plus-fill text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Manage Instructors</h6>
                                    <small class="text-muted">Add or modify faculty</small>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('chairperson.assignSubjects') }}" class="btn btn-light text-start rounded-3 p-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 p-2 bg-success-subtle me-3">
                                    <i class="bi bi-journal-plus text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Assign Subjects</h6>
                                    <small class="text-muted">Manage teaching loads</small>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('chairperson.viewGrades') }}" class="btn btn-light text-start rounded-3 p-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 p-2 bg-success-subtle me-3">
                                    <i class="bi bi-clipboard-data text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">View Grades</h6>
                                    <small class="text-muted">Monitor student performance</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --theme-primary: #4e73df;
        --theme-success: #1cc88a;
        --theme-info: #36b9cc;
        --theme-warning: #f6c23e;
        --theme-danger: #e74a3b;
        --theme-secondary: #858796;
        --theme-light: #f8f9fc;
        --theme-dark: #5a5c69;
    }

    .hover-lift {
        transition: transform 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
    }

    /* Custom Theme Colors */
    .bg-primary-subtle {
        background-color: rgba(78, 115, 223, 0.1) !important;
    }
    .bg-success-subtle {
        background-color: rgba(28, 200, 138, 0.1) !important;
    }
    .bg-info-subtle {
        background-color: rgba(54, 185, 204, 0.1) !important;
    }
    .bg-warning-subtle {
        background-color: rgba(246, 194, 62, 0.1) !important;
    }
    .bg-danger-subtle {
        background-color: rgba(231, 74, 59, 0.1) !important;
    }

    .text-primary {
        color: var(--theme-primary) !important;
    }
    .text-success {
        color: var(--theme-success) !important;
    }
    .text-info {
        color: var(--theme-info) !important;
    }
    .text-warning {
        color: var(--theme-warning) !important;
    }
    .text-danger {
        color: var(--theme-danger) !important;
    }

    .btn-primary {
        background-color: var(--theme-primary);
        border-color: var(--theme-primary);
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }

    .btn-light {
        background-color: var(--theme-light);
        border-color: #e3e6f0;
    }
    .btn-light:hover {
        background-color: #eaecf4;
        border-color: #d1d3e2;
    }

    /* Status Cards */
    .border-success-subtle {
        border-color: rgba(28, 200, 138, 0.2) !important;
    }
    .border-danger-subtle {
        border-color: rgba(231, 74, 59, 0.2) !important;
    }
    .border-warning-subtle {
        border-color: rgba(246, 194, 62, 0.2) !important;
    }

    /* Quick Actions */
    .btn-light:hover .bg-primary-subtle {
        background-color: rgba(78, 115, 223, 0.15) !important;
    }
    .btn-light:hover .bg-success-subtle {
        background-color: rgba(28, 200, 138, 0.15) !important;
    }
    .btn-light:hover .bg-info-subtle {
        background-color: rgba(54, 185, 204, 0.15) !important;
    }

    /* Status Cards Enhancements */
    .status-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .status-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
    }
    
    .status-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .status-card.active::before {
        background-color: var(--theme-success);
        opacity: 1;
    }

    .status-card.inactive::before {
        background-color: var(--theme-danger);
        opacity: 1;
    }

    .status-card.pending::before {
        background-color: var(--theme-warning);
        opacity: 1;
    }

    .status-percentage {
        font-size: 1.25rem;
        font-weight: 600;
    }

    /* Progress Bar Enhancements */
    .progress {
        border-radius: 10px;
        overflow: hidden;
        background-color: #f8f9fc;
    }

    .progress-bar {
        transition: width 0.8s ease;
    }

    /* Badge Enhancements */
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Dropdown Enhancements */
    .dropdown-menu {
        border: none;
        border-radius: 12px;
    }

    .btn-light {
        background-color: #f8f9fc;
        border-color: #e3e6f0;
    }

    .btn-light:hover {
        background-color: #eaecf4;
        border-color: #d1d3e2;
    }

    /* Tooltip customization */
    .tooltip {
        font-size: 0.875rem;
    }
    
    .tooltip .tooltip-inner {
        background-color: var(--theme-dark);
        padding: 0.5rem 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    // Initialize tooltips for progress bars
    document.addEventListener('DOMContentLoaded', function() {
        var progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(function(bar) {
            new bootstrap.Tooltip(bar);
        });
    });
</script>
@endpush
