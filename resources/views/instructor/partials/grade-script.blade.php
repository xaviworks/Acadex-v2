<script>
    function bindGradeInputEvents() {
        const inputs = Array.from(document.querySelectorAll('.grade-input'));
    
        const inputGrid = {};
        const activityIds = new Set();
        const studentIds = new Set();
    
        function handleChange(e) {
            const input = e.target;
            const studentId = input.dataset.student;
            const activityId = input.dataset.activity;
            const subjectId = document.querySelector('input[name="subject_id"]').value;
            const term = document.querySelector('input[name="term"]').value;
            const score = input.value;
    
            fetch("{{ route('instructor.grades.ajaxSaveScore') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ student_id: studentId, activity_id: activityId, subject_id: subjectId, term, score })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('Failed to save score.');
                }
            })
            .catch(() => alert('Error saving score.'));
        }
    
        inputs.forEach(input => {
            input.removeEventListener('change', handleChange);
            input.addEventListener('change', handleChange);
    
            const student = input.dataset.student;
            const activity = input.dataset.activity;
            activityIds.add(activity);
            studentIds.add(student);
    
            if (!inputGrid[activity]) inputGrid[activity] = {};
            inputGrid[activity][student] = input;
        });
    
        const sequence = [];
        [...activityIds].sort().forEach(activityId => {
            [...studentIds].sort().forEach(studentId => {
                const el = inputGrid[activityId]?.[studentId];
                if (el) sequence.push(el);
            });
        });
    
        sequence.forEach((input, idx) => {
            input.addEventListener('keydown', e => {
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
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        console.log("Grade script loaded");
        bindGradeInputEvents();
    
        const overlay = document.getElementById('fadeOverlay');
    
        document.querySelectorAll('.subject-card[data-url]').forEach(button => {
            button.addEventListener('click', function () {
                const url = this.dataset.url;
                console.log("Subject card clicked:", url);
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
    
                    bindGradeInputEvents();
                    overlay.classList.add('d-none');
                })
                .catch(() => {
                    overlay.classList.add('d-none');
                    alert('Failed to load subject grades.');
                });
            });
        });
    
        document.addEventListener('click', function (e) {
            if (e.target.closest('.term-step')) {
                const button = e.target.closest('.term-step');
                const term = button.dataset.term;
                const subjectId = document.querySelector('input[name="subject_id"]')?.value;
                if (!subjectId) return;
    
                console.log("Term step clicked:", term);
    
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
    
                    bindGradeInputEvents();
                    overlay.classList.add('d-none');
                })
                .catch(() => {
                    overlay.classList.add('d-none');
                    alert('Failed to load term data.');
                });
            }
        });
    });
    
    window.bindGradeInputEvents = bindGradeInputEvents;
    </script>
    