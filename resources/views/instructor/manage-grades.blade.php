@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    @if (!$subject)
        {{-- SUBJECT SELECTION --}}
        <div class="row g-4">
            @foreach($subjects as $subjectItem)
                <div class="col-md-4">
                    <a href="{{ route('instructor.manageGrades', ['subject_id' => $subjectItem->id, 'term' => 'prelim']) }}"
                       class="text-decoration-none text-dark">
                        <div class="card h-100 border-0 shadow rounded-4 hover-shadow-lg transition-all">
                            <div class="card-body d-flex flex-column justify-content-between bg-white p-4 rounded-4">
                                <div class="text-center mb-3">
                                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center text-white shadow-lg"
                                         style="width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                        <h5 class="mb-0 fw-bold">{{ $subjectItem->subject_code }}</h5>
                                    </div>
                                    <h6 class="fw-semibold mt-3 text-truncate" title="{{ $subjectItem->subject_description }}">
                                        {{ $subjectItem->subject_description }}
                                    </h6>
                                </div>
                                <div class="text-muted text-center small">
                                    Instructor: <strong>{{ $subjectItem->instructor->name ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
    <style>
        .stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 2rem 0;
        }
    
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            flex: 1;
            text-decoration: none;
        }
    
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 38px;
            left: calc(50% + 40px);
            width: calc(100% - 80px);
            height: 2px;
            background-color: #e5e7eb;
            z-index: 0;
        }
    
        .circle-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
        }
    
        .circle {
            position: absolute;
            top: 8px;
            left: 8px;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            z-index: 2;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
    
        .step:hover .circle {
            transform: scale(1.05);
        }
    
        .completed .circle {
            background-color: #2563eb;
        }
    
        .active .circle {
            background-color: #ef4444;
            animation: pulse 1.5s infinite;
        }
    
        .upcoming .circle {
            background-color: #cbd5e1;
            color: #374151;
        }
    
        .progress-ring {
            position: absolute;
            top: 0;
            left: 0;
            transform: rotate(-90deg);
            z-index: 1;
        }
    
        .progress-ring circle {
            fill: none;
            stroke-width: 6;
            stroke-linecap: round;
        }
    
        .progress-ring-bg {
            stroke: #e5e7eb;
        }
    
        .progress-ring-bar {
            stroke: #2563eb;
            transition: stroke-dashoffset 0.4s ease-in-out;
        }
    
        .step-label {
            font-size: 15px;
            margin-top: 0.75rem;
        }
    
        .completed .step-label {
            color: #1e3a8a;
            font-weight: 500;
        }
    
        .active .step-label {
            color: #dc2626;
            font-weight: 600;
        }
    
        .upcoming .step-label {
            color: #6b7280;
        }
    
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(239, 68, 68, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }
    </style>
    
    @php
        $terms = ['prelim', 'midterm', 'prefinal', 'final'];
        $termLabels = ['Prelim', 'Midterm', 'Prefinal', 'Final'];
        $radius = 36;
        $circumference = 2 * pi() * $radius;
        $totalCells = count($students) * count($activities);
        $filledCells = 0;
    
        foreach ($students as $student) {
            foreach ($activities as $activity) {
                if (isset($scores[$student->id][$activity->id]) && $scores[$student->id][$activity->id] !== null) {
                    $filledCells++;
                }
            }
        }
    
        $completion = $totalCells > 0 ? round(($filledCells / $totalCells) * 100) : 0;
        $offset = $circumference - ($completion / 100) * $circumference;
    @endphp
    
    <div class="stepper">
        @foreach ($terms as $index => $termSlug)
            @php
                $step = $index + 1;
                $label = ucfirst($termSlug);
                $isActive = $term === $termSlug;
                $isCompleted = array_search($term, $terms) > $index;
                $class = $isActive ? 'active' : ($isCompleted ? 'completed' : 'upcoming');
            @endphp
    
            <a href="{{ route('instructor.manageGrades', ['subject_id' => $subject->id, 'term' => $termSlug]) }}"
               class="step {{ $class }}">
                <div class="circle-wrapper">
                    <svg class="progress-ring" width="80" height="80">
                        <circle class="progress-ring-bg" cx="40" cy="40" r="{{ $radius }}" />
                        @if ($isActive)
                            <circle class="progress-ring-bar" cx="40" cy="40" r="{{ $radius }}"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $offset }}" />
                        @endif
                    </svg>
                    <div class="circle">{{ $step }}</div>
                </div>
                <div class="step-label">{{ $label }}</div>
            </a>
        @endforeach
    </div>
    
    

        {{-- SECTION TITLE + ADD ACTIVITY BUTTON --}}
        <div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-2">
            <h4 class="mb-0 fw-semibold text-dark">
                <i class="bi bi-journal-text me-2 text-primary"></i>
                Activities & Grades – {{ ucfirst($term) }}
            </h4>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#addActivityModal">
                <i class="bi bi-plus-circle-fill"></i> Add Activity
            </button>
        </div>

        {{-- GRADES TABLE --}}
        <form method="POST" action="{{ route('instructor.saveGrades') }}">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <input type="hidden" name="term" value="{{ $term }}">

            <div class="table-responsive shadow-sm rounded overflow-hidden">
                @if(count($students) > 0 && count($activities) > 0)
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Student</th>
                                @foreach($activities as $activity)
                                    <th class="text-center">
                                        <div class="fw-semibold">{{ ucfirst($activity->type) }}</div>
                                        <div class="text-muted small">{{ $activity->title }} ({{ $activity->number_of_items }} pts)</div>
                                    </th>
                                @endforeach
                                <th class="text-center">Term Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    @foreach($activities as $activity)
                                        @php
                                            $score = $scores[$student->id][$activity->id] ?? null;
                                        @endphp
                                        <td>
                                            <input type="number"
                                                   class="form-control text-center rounded-3 grade-input"
                                                   name="scores[{{ $student->id }}][{{ $activity->id }}]"
                                                   value="{{ $score !== null ? (int) $score : '' }}"
                                                   placeholder="0"
                                                   min="0"
                                                   max="{{ $activity->number_of_items }}"
                                                   step="1"
                                                   title="Max: {{ $activity->number_of_items }}"
                                                   data-student="{{ $student->id }}"
                                                   data-activity="{{ $activity->id }}">
                                        </td>
                                    @endforeach
                                    <td class="text-center fw-bold {{ isset($termGrades[$student->id]) ? 'text-primary' : 'text-muted' }}">
                                        {{ $termGrades[$student->id] ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning text-center m-0">
                        No students or activities available for {{ ucfirst($term) }}.
                    </div>
                @endif
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success px-5 shadow-sm">Save Grades</button>
            </div>
        </form>

        {{-- ADD ACTIVITY MODAL --}}
        @include('instructor.partials.add-activity-modal', ['subject' => $subject, 'term' => $term])

        {{-- SUCCESS TOAST --}}
        @if(session('success'))
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
                <div class="toast show align-items-center text-bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = Array.from(document.querySelectorAll('.grade-input'));

    // Build a vertical tab sequence: activity-first (vertical input)
    const inputGrid = {};
    inputs.forEach(input => {
        const student = input.dataset.student;
        const activity = input.dataset.activity;
        if (!inputGrid[activity]) inputGrid[activity] = {};
        inputGrid[activity][student] = input;
    });

    const activityIds = Object.keys(inputGrid);
    const studentIds = [...new Set(inputs.map(i => i.dataset.student))];

    const sequence = [];
    activityIds.forEach(activityId => {
        studentIds.forEach(studentId => {
            const el = inputGrid[activityId][studentId];
            if (el) sequence.push(el);
        });
    });

    sequence.forEach((input, idx) => {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Tab' || e.key === 'Enter') {
                e.preventDefault();
                const next = sequence[idx + 1];
                if (next) {
                    next.focus();
                    next.select();
                }
            }
        });
    });
});
</script>
@endpush
