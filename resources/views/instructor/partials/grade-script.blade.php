<script>
    function bindGradeInputEvents() {
        console.log("Binding grade input events...");
        
        // Safely query elements with null checks
        const inputs = Array.from(document.querySelectorAll('.grade-input') || []);
        const itemsInputs = Array.from(document.querySelectorAll('.items-input') || []);
        const saveButton = document.getElementById('saveGradesBtn');
        const form = document.querySelector('form');
        const studentSearch = document.getElementById('studentSearch');

        // Track unsaved changes
        let hasUnsavedChanges = false;
        const originalValues = new Map();

        // Store original values
        inputs.forEach(input => {
            originalValues.set(input, input.value);
        });

        // Function to check for unsaved changes
        function checkUnsavedChanges() {
            let changed = false;
            inputs.forEach(input => {
                if (input.value !== originalValues.get(input)) {
                    changed = true;
                }
            });
            return changed;
        }

        // Function to update save button state
        function updateSaveButtonState() {
            if (saveButton) {
                const hasChanges = checkUnsavedChanges();
                hasUnsavedChanges = hasChanges;
                
                // Update button appearance
                saveButton.classList.toggle('btn-pulse', hasChanges);
                saveButton.disabled = !hasChanges || !validateAllInputs();
                
                // Update notification message
                const container = document.getElementById('unsavedNotificationContainer');
                if (container) {
                    if (hasChanges) {
                        container.innerHTML = `
                            <div class="alert alert-warning py-2 px-3 mb-0 me-3 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <span>You have unsaved changes</span>
                            </div>
                        `;
                    } else {
                        container.innerHTML = '';
                    }
                }

                // Update notification badge
                let badge = document.getElementById('unsavedChangesBadge');
                if (hasChanges) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.id = 'unsavedChangesBadge';
                        badge.className = 'position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle';
                        badge.innerHTML = '<span class="visually-hidden">Unsaved changes</span>';
                        saveButton.style.position = 'relative';
                        saveButton.appendChild(badge);
                    }
                } else if (badge) {
                    badge.remove();
                }
            }
        }

        console.log("Found items inputs:", itemsInputs.length);

        // Initialize data structures
        const inputGrid = {};
        const activityIds = new Set();
        const studentIds = new Set();

        // Create and append modal to body
        const modalHtml = `
            <div class="modal fade" id="gradeWarningModal" tabindex="-1" aria-labelledby="gradeWarningModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="gradeWarningModalLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i>Invalid Grades Detected
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">The following grades exceed the new maximum score:</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="invalidGradesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Current Grade</th>
                                            <th>New Maximum</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <p class="text-danger mt-3 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Please adjust these grades before changing the number of items.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const warningModal = new bootstrap.Modal(document.getElementById('gradeWarningModal'));

        // Function to show warning modal
        function showWarningModal(invalidGrades, newMax) {
            const tbody = document.querySelector('#invalidGradesTable tbody');
            tbody.innerHTML = invalidGrades.map(grade => `
                <tr>
                    <td>${grade.student}</td>
                    <td class="text-danger">${grade.grade}</td>
                    <td>${newMax}</td>
                </tr>
            `).join('');
            warningModal.show();
        }

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

        // New function to check if any grades exceed the new maximum
        function checkGradesAgainstNewMax(activityId, newMax) {
            const invalidGrades = [];
            document.querySelectorAll(`.grade-input[data-activity="${activityId}"]`).forEach(gradeInput => {
                const value = parseInt(gradeInput.value);
                if (!isNaN(value) && value > newMax) {
                    const studentName = gradeInput.closest('tr')?.querySelector('td')?.textContent.trim() || 'Unknown Student';
                    invalidGrades.push({
                        student: studentName,
                        grade: value
                    });
                }
            });
            return invalidGrades;
        }

        // Handle student search if element exists
        if (studentSearch) {
            studentSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.student-row');
                
                rows.forEach(function(row) {
                    const studentName = row.querySelector('td')?.textContent.toLowerCase() || '';
                    row.style.display = studentName.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Handle items inputs
        if (itemsInputs.length > 0) {
            itemsInputs.forEach(input => {
                if (!input) return; // Skip if input is null
                
                // Prevent form submission on enter
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopPropagation();
                        this.blur(); // Remove focus to trigger change event
                    }
                });
                
                input.addEventListener('change', function(e) {
                    // Prevent any form submission
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const activityId = this.dataset.activityId;
                    const newValue = parseInt(this.value);
                    const oldValue = parseInt(this.defaultValue);

                    if (isNaN(newValue) || newValue < 1) {
                        showWarningModal([{ student: 'Error', grade: 'Invalid input' }], 1);
                        this.value = this.defaultValue;
                        return;
                    }

                    // Check if new maximum would invalidate existing grades
                    const invalidGrades = checkGradesAgainstNewMax(activityId, newValue);
                    if (newValue < oldValue && invalidGrades.length > 0) {
                        showWarningModal(invalidGrades, newValue);
                        this.value = oldValue;
                        return;
                    }

                    // Disable save button during the update
                    if (saveButton) {
                        saveButton.disabled = true;
                    }

                    fetch('/instructor/activities/' + activityId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            number_of_items: newValue,
                            type: this.closest('th')?.querySelector('.fw-semibold')?.textContent.toLowerCase() || '',
                            title: this.closest('th')?.querySelector('.text-muted')?.textContent.trim() || ''
                        })
                    })
                    .then(async response => {
                        if (!response.ok) {
                            const errorData = await response.json().catch(() => null);
                            console.error('Server response:', {
                                status: response.status,
                                statusText: response.statusText,
                                data: errorData
                            });
                            throw new Error(
                                errorData?.message || 
                                `Server returned ${response.status}: ${response.statusText}`
                            );
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            this.defaultValue = newValue;
                            let hasInvalidGrades = false;
                            
                            document.querySelectorAll(`.grade-input[data-activity="${activityId}"]`).forEach(gradeInput => {
                                if (gradeInput) {
                                    const currentValue = parseInt(gradeInput.value);
                                    gradeInput.max = newValue;
                                    gradeInput.title = `Max: ${newValue}`;
                                    
                                    // Check if current value exceeds new max
                                    if (!isNaN(currentValue) && currentValue > newValue) {
                                        hasInvalidGrades = true;
                                        gradeInput.classList.add('is-invalid', 'border-danger');
                                    } else {
                                        gradeInput.classList.remove('is-invalid', 'border-danger');
                                    }
                                }
                            });

                            // Update save button state
                            if (saveButton) {
                                const isValid = validateAllInputs();
                                saveButton.disabled = !isValid;
                                saveButton.title = isValid ? '' : 'Please correct invalid grades before saving';
                            }

                            // Show warning if any grades became invalid
                            if (hasInvalidGrades) {
                                showWarningModal(checkGradesAgainstNewMax(activityId, newValue), newValue);
                            }
                        } else {
                            throw new Error(data.message || 'Failed to update number of items');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to update number of items: ' + error.message);
                        this.value = oldValue;
                        
                        // Re-enable save button on error
                        if (saveButton) {
                            const isValid = validateAllInputs();
                            saveButton.disabled = !isValid;
                        }
                    });
                });
            });
        }

        // Handle grade inputs
        if (inputs.length > 0) {
            inputs.forEach(input => {
                if (!input) return; // Skip if input is null
                
                const student = input.dataset.student;
                const activity = input.dataset.activity;
                
                if (student && activity) {
                    activityIds.add(activity);
                    studentIds.add(student);
                    
                    if (!inputGrid[activity]) inputGrid[activity] = {};
                    inputGrid[activity][student] = input;
                }
            });
        }

        // Setup form validation if form exists
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateAllInputs()) {
                    e.preventDefault();
                    alert('Please correct all invalid grades before submitting.');
                    return false;
                }
                // Reset change tracking after successful submission
                hasUnsavedChanges = false;
                updateSaveButtonState();
            });
        }

        // Setup navigation sequence
        const sequence = [];
        [...activityIds].sort().forEach(activityId => {
            [...studentIds].sort().forEach(studentId => {
                const el = inputGrid[activityId]?.[studentId];
                if (el) sequence.push(el);
            });
        });

        sequence.forEach((input, idx) => {
            if (!input) return; // Skip if input is null
            
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

        // Add input event listeners for change tracking
        inputs.forEach(input => {
            if (!input) return;

            // Update on typing
            input.addEventListener('input', () => {
                updateSaveButtonState();
                validateInput(input);
            });

            // Update on change (blur/enter)
            input.addEventListener('change', () => {
                updateSaveButtonState();
                validateInput(input);
            });

            // Select all text when focusing
            input.addEventListener('focus', () => {
                input.select();
            });
        });

        // Initial state check
        updateSaveButtonState();
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Grade script loaded");
        bindGradeInputEvents();
    });

    // Add navigation prevention with modal
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });

    // Prevent navigation when clicking links if there are unsaved changes
    document.addEventListener('click', function(e) {
        if (hasUnsavedChanges) {
            const link = e.target.closest('a');
            if (link && !link.hasAttribute('data-bs-toggle')) {
                e.preventDefault();
                if (confirm('You have unsaved changes. Are you sure you want to leave this page?')) {
                    window.location.href = link.href;
                }
            }
        }
    });

    // Add styles for notifications
    const style = document.createElement('style');
    style.textContent = `
        .btn-pulse {
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 rgba(40, 167, 69, 0.4);
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }

        #unsavedNotification {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);

    // Export for external use
    window.bindGradeInputEvents = bindGradeInputEvents;
</script>
    