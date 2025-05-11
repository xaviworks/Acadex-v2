@extends('layouts.app')

@section('content')
<div class="container">
    <div class="container py-5">
        <h2 class="mb-4 fw-bold text-dark">📊 Chairperson Dashboard Overview</h2>

    {{-- Summary Cards --}}
    <div class="row justify-content-center g-4">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-3 h-100 bg-white animate__animated animate__fadeInUp">
                <div class="d-flex align-items-center">
                    <span class="me-2 fs-4">👨‍🎓</span>
                    <div>
                        <h6 class="text-muted mb-0">Total Students</h6>
                        <h3 class="fw-bold text-primary mt-1">{{ $studentsPerDepartment->sum() }}</h3> <!-- Sum of all departments -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-3 h-100 bg-white animate__animated animate__fadeInUp">
                <div class="d-flex align-items-center">
                    <span class="me-2 fs-4">🧑‍🏫</span>
                    <div>
                        <h6 class="text-muted mb-0">Total Instructors</h6>
                        <h3 class="fw-bold text-success mt-1">{{ $totalInstructors }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-3 h-100 bg-white animate__animated animate__fadeInUp">
                <div class="d-flex align-items-center">
                    <span class="me-2 fs-4">📚</span>
                    <div>
                        <h6 class="text-muted mb-0">Number of Programs (Courses)</h6>
                        <h3 class="fw-bold text-warning mt-1">{{ $studentsPerCourse->count() }}</h3> <!-- Count of courses -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Students per Course --}}
    <div class="mt-5">
        <h3 class="mb-4">Total Students Per Course</h3>
        <div class="row">
            @foreach ($studentsPerCourse as $courseCode => $total)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 p-3 h-100 bg-light">
                        <div class="d-flex align-items-center">
                            <span class="me-2 fs-4">📚</span>
                            <div>
                                <h6 class="text-muted mb-0">{{ $courseCode }}</h6> <!-- You could also display the course description here -->
                                <h3 class="fw-bold text-warning mt-1">{{ $total }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
