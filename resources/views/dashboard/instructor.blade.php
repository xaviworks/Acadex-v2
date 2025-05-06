@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold text-dark">📊 Instructor Dashboard Overview</h2>

    {{-- Summary Cards --}}
    <div class="row g-4">
        @php
            $cards = [
                ['label' => 'Total Students', 'icon' => '👥', 'value' => $instructorStudents, 'color' => 'text-primary'],
                ['label' => 'Enrolled Subjects', 'icon' => '📚', 'value' => $enrolledSubjectsCount, 'color' => 'text-dark'],
                ['label' => 'Students Passed', 'icon' => '✅', 'value' => $totalPassedStudents, 'color' => 'text-success'],
                ['label' => 'Students Failed', 'icon' => '❌', 'value' => $totalFailedStudents, 'color' => 'text-danger'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-3 h-100 bg-white animate__animated animate__fadeInUp">
                    <div class="d-flex align-items-center">
                        <span class="me-2 fs-4">{{ $card['icon'] }}</span>
                        <div>
                            <h6 class="text-muted mb-0">{{ $card['label'] }}</h6>
                            <h3 class="fw-bold {{ $card['color'] }} mt-1">{{ $card['value'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Term Completion --}}
    <div class="mt-5">
        <h5 class="fw-semibold mb-3">📈 Grading Completion by Term</h5>
        <div class="row g-4">
            @foreach(['prelim', 'midterm', 'prefinal', 'final'] as $term)
                @php
                    $progress = $termCompletions[$term]['total'] > 0
                        ? round(($termCompletions[$term]['graded'] / $termCompletions[$term]['total']) * 100)
                        : 0;

                    $color = match(true) {
                        $progress === 100 => 'bg-success',
                        $progress > 0 => 'bg-warning text-dark',
                        default => 'bg-secondary'
                    };
                @endphp
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 p-3 h-100 animate__animated animate__zoomIn">
                        <h6 class="text-muted text-capitalize">{{ ucfirst($term) }}</h6>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <span class="fw-semibold">{{ $termCompletions[$term]['graded'] }} / {{ $termCompletions[$term]['total'] }}</span>
                            <span class="badge {{ $color }} rounded-pill px-3">{{ $progress }}%</span>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $progress }}%;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Per Subject Charts --}}
    <div class="mt-5">
        <h5 class="fw-semibold mb-3">📘 Subject-wise Grading Progress</h5>
        <div class="row g-4">
            @foreach($subjectCharts as $chart)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100 animate__animated animate__fadeIn">
                        <h6 class="mb-3 fw-semibold text-dark">{{ $chart['code'] }} – {{ $chart['description'] }}</h6>
                        <canvas id="chart-{{ $loop->index }}" style="height: 200px;"></canvas>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const termLabels = ['Prelim', 'Midterm', 'Prefinal', 'Final'];

    @foreach($subjectCharts as $index => $chart)
        const ctx{{ $index }} = document.getElementById('chart-{{ $index }}').getContext('2d');
        new Chart(ctx{{ $index }}, {
            type: 'bar',
            data: {
                labels: termLabels,
                datasets: [{
                    label: 'Completion %',
                    data: @json($chart['termPercentages']),
                    backgroundColor: '#4da674',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.y}% completed`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: value => value + '%' }
                    }
                }
            }
        });
    @endforeach
});
</script>
@endpush
