<!-- resources/views/instructor/scores/partials/auto-save-script.blade.php -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = Array.from(document.querySelectorAll('.grade-input'));

    // Auto-save on change
    inputs.forEach(input => {
        input.addEventListener('change', function () {
            const studentId = this.dataset.student;
            const activityId = this.dataset.activity;
            const subjectId = document.querySelector('input[name="subject_id"]').value;
            const term = document.querySelector('input[name="term"]').value;
            const score = this.value;

            fetch("{{ route('grades.ajaxSaveScore') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    student_id: studentId,
                    activity_id: activityId,
                    subject_id: subjectId,
                    term: term,
                    score: score
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('Failed to save score.');
                }
            })
            .catch(() => alert('Error saving score.'));
        });
    });

    // Optional: Keyboard navigation (Tab/Enter)
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

    // Future: Add debounce or toast notification for smoother UX
});
</script>
@endpush
