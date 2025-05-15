<script>
    function bindGradeInputEvents() {
        const inputs = Array.from(document.querySelectorAll('.grade-input'));
        const saveButton = document.getElementById('saveGradesBtn');
        const form = document.querySelector('form');
    
        const inputGrid = {};
        const activityIds = new Set();
        const studentIds = new Set();

        // Validation function
        function validateInput(input) {
            const value = input.value.trim();
            const max = parseInt(input.getAttribute('max'));
            
            input.classList.remove('is-invalid', 'border-danger');
            
            if (value !== '') {
                const numValue = parseInt(value);
                if (isNaN(numValue) || numValue < 0 || numValue > max) {
                    input.classList.add('is-invalid', 'border-danger');
                    return false;
                }
            }
            return true;
        }

        // Validate all inputs
        function validateAllInputs() {
            let isValid = true;
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });
            return isValid;
        }
    
        function handleChange(e) {
            const input = e.target;
            const studentId = input.dataset.student;
            const activityId = input.dataset.activity;
            const subjectId = document.querySelector('input[name="subject_id"]').value;
            const term = document.querySelector('input[name="term"]').value;
            const score = input.value;

            // Validate input before saving
            if (!validateInput(input)) {
                input.focus();
                if (saveButton) {
                    saveButton.disabled = true;
                    saveButton.title = 'Please correct invalid grades before saving';
                }
                return;
            }

            // Enable/disable save button based on all inputs
            if (saveButton) {
                const isValid = validateAllInputs();
                saveButton.disabled = !isValid;
                saveButton.title = isValid ? '' : 'Please correct invalid grades before saving';
            }
    
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
                    input.classList.add('is-invalid', 'border-danger');
                } else {
                    input.classList.remove('is-invalid', 'border-danger');
                }
            })
            .catch(() => {
                alert('Error saving score.');
                input.classList.add('is-invalid', 'border-danger');
            });
        }

        // Add input event listeners
        inputs.forEach(input => {
            input.removeEventListener('change', handleChange);
            input.addEventListener('change', handleChange);
            
            // Add input validation on keyup
            input.addEventListener('input', function() {
                validateInput(this);
                if (saveButton) {
                    const isValid = validateAllInputs();
                    saveButton.disabled = !isValid;
                    saveButton.title = isValid ? '' : 'Please correct invalid grades before saving';
                }
            });
    
            const student = input.dataset.student;
            const activity = input.dataset.activity;
            activityIds.add(activity);
            studentIds.add(student);
    
            if (!inputGrid[activity]) inputGrid[activity] = {};
            inputGrid[activity][student] = input;
        });

        // Form submission validation
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateAllInputs()) {
                    e.preventDefault();
                    alert('Please correct all invalid grades before submitting.');
                    return false;
                }
            });
        }
    
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

        // Initial validation on page load
        if (saveButton) {
            const isValid = validateAllInputs();
            saveButton.disabled = !isValid;
            saveButton.title = isValid ? '' : 'Please correct invalid grades before saving';
        }
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
    