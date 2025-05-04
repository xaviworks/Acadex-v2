@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div id="grade-section">
        @if (!$subject)
            {{-- SUBJECT SELECTION --}}
            <div class="row g-4 px-4" id="subject-selection">
                @foreach($subjects as $subjectItem)
                    <div class="col-md-4">
                        <div
                            class="subject-card card h-100 bg-white border-0 shadow-lg rounded-4 transform transition hover:scale-105 hover:shadow-xl"
                            data-url="{{ route('instructor.grades.index') }}?subject_id={{ $subjectItem->id }}&term=prelim"
                            style="cursor: pointer;"
                        >
                            <div class="card-body d-flex flex-column justify-content-between p-4 rounded-4">
                                <div class="text-center mb-3">
                                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center text-white shadow"
                                        style="width: 80px; height: 80px; background: linear-gradient(135deg, #4da674, #023336); transition: background 0.3s ease;">
                                        <h5 class="mb-0 fw-bold">{{ $subjectItem->subject_code }}</h5>
                                    </div>
                                    <h6 class="fw-semibold mt-3 text-truncate text-dark" title="{{ $subjectItem->subject_description }}">
                                        {{ $subjectItem->subject_description }}
                                    </h6>
                                </div>
                                <div class="text-muted text-center small">
                                    Instructor: <strong>{{ $subjectItem->instructor->name ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- TERM NAVIGATION STEPPER --}}
            @include('instructor.partials.term-stepper')

            {{-- ADD ACTIVITY HEADER & MODAL --}}
            @include('instructor.partials.activity-header', ['subject' => $subject, 'term' => $term])

            {{-- GRADES TABLE --}}
            <form method="POST" action="{{ route('instructor.grades.store') }}">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <input type="hidden" name="term" value="{{ $term }}">
                @include('instructor.partials.grade-table')
            </form>

            {{-- SUCCESS TOAST --}}
            @if(session('success'))
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
                    <div class="toast show align-items-center text-bg-success border-0 shadow" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">{{ session('success') }}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Fade Overlay Spinner -->
    <div id="fadeOverlay" class="fade-overlay d-none">
        <div class="spinner"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
.fade-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255, 255, 255, 0.75);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
    transition: opacity 0.3s ease-in-out;
}
.fade-overlay.d-none {
    display: none !important;
}
.spinner {
    border: 4px solid #e5e7eb;
    border-top: 4px solid #4da674;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 0.6s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
.subject-card {
    transition: all 0.3s ease;
}
.subject-card:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
@include('instructor.partials.grade-script')

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('✅ manage-grades script loaded and DOM ready');
    const overlay = document.getElementById('fadeOverlay');

    // SUBJECT CARD CLICK → full section replacement
    document.querySelectorAll('.subject-card[data-url]').forEach(card => {
        card.addEventListener('click', function () {
            const url = this.dataset.url;
            overlay.classList.remove('d-none');

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGradeSection = doc.querySelector('#grade-section');
                document.getElementById('grade-section')?.replaceWith(newGradeSection);

                if (typeof bindGradeInputEvents === 'function') {
                    bindGradeInputEvents();
                }

                overlay.classList.add('d-none');
            })
            .catch(() => {
                overlay.classList.add('d-none');
                alert('Failed to load subject grades.');
            });
        });
    });

    // TERM STEPPER CLICK → partial section update
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.term-step');
        if (button) {
            const term = button.dataset.term;
            const subjectId = document.querySelector('input[name="subject_id"]')?.value;
            if (!subjectId) return;

            overlay.classList.remove('d-none');

            fetch(`/instructor/grades/partial?subject_id=${subjectId}&term=${term}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGradeSection = doc.querySelector('#grade-section');
                document.getElementById('grade-section')?.replaceWith(newGradeSection);

                if (typeof bindGradeInputEvents === 'function') {
                    bindGradeInputEvents();
                }

                overlay.classList.add('d-none');
            })
            .catch(() => {
                overlay.classList.add('d-none');
                alert('Failed to load term data.');
            });
        }
    });
});
</script>
@endpush
