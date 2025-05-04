<!-- resources/views/instructor/scores/partials/subject-selection.blade.php -->
<div class="row g-4">
    @foreach($subjects as $subjectItem)
        <div class="col-md-4">
            <a href="{{ route('grades.index', ['subject_id' => $subjectItem->id, 'term' => 'prelim']) }}"
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


<!-- resources/views/instructor/scores/partials/term-stepper.blade.php -->
@php
    $terms = ['prelim', 'midterm', 'prefinal', 'final'];
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
        <a href="{{ route('grades.index', ['subject_id' => $subject->id, 'term' => $termSlug]) }}" class="step {{ $class }}">
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