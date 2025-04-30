<!-- Add Activity Modal -->
<div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('instructor.activities.store') }}" method="POST">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <input type="hidden" name="term" value="{{ $term }}">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addActivityModalLabel">Add Activity - {{ ucfirst($term) }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Activity Title</label>
                        <input type="text" name="title" class="form-control rounded-2" required placeholder="e.g. Quiz 1, Exam 1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Type</label>
                        <select name="type" class="form-select rounded-2" required>
                            <option value="quiz">Quiz</option>
                            <option value="ocr">OCR</option>
                            <option value="exam">Exam</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Points</label>
                        <input type="number" name="points" class="form-control rounded-2" required placeholder="e.g. 100">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
