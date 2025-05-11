@extends('layouts.app')

@section('content')
<div class="container">
    <div class="container py-5">
        <h2 class="mb-4 fw-bold text-dark">📊 Chairperson Dashboard Overview</h2>

        {{-- Summary Cards --}}
        <div class="row justify-content-center g-4">
            @php
                $cards = [
                    ['label' => 'Number of Instructors', 'icon' => '🧑‍🏫', 'value' => $countInstructors, 'color' => 'text-primary'], // teacher icon
                    ['label' => 'Number of Students', 'icon' => '🎓', 'value' => $countStudents, 'color' => 'text-success'], // graduation cap
                    ['label' => 'Number of Courses', 'icon' => '📚', 'value' => $countCourses, 'color' => 'text-success'], // books
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-12 col-md-6 col-lg-4">
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
    </div>
</div>
@endsection
