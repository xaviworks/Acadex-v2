<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-2">
    <h4 class="mb-0 fw-semibold text-dark">
        <i class="bi bi-journal-text me-2 text-primary"></i>
        Activities & Grades – {{ ucfirst($term) }}
    </h4>

    <div x-data="{ open: false }" x-init="$nextTick(() => open = false)" class="position-relative" x-cloak>
        <!-- Trigger Button -->
        <button @click="open = true" type="button"
                class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle-fill"></i> Add Activity
        </button>

        <!-- Modal Overlay -->
        <div x-show="open"
             x-transition.opacity
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @keydown.escape.window="open = false"
             @click.self="open = false">

            <!-- Modal Content -->
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative"
                 @click.stop>
                <!-- Close Button -->
                <button @click="open = false" type="button"
                        class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl font-bold focus:outline-none">
                    &times;
                </button>

                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Add New Activity</h2>

                <form method="POST" action="{{ route('instructor.activities.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <input type="hidden" name="term" value="{{ $term }}">

                    <!-- Activity Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
                        <select name="type" id="type"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                                required>
                            <option value="">-- Select Type --</option>
                            <option value="quiz">Quiz</option>
                            <option value="ocr">OCR</option>
                            <option value="exam">Exam</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" id="title"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                               required>
                    </div>

                    <!-- Number of Items -->
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Number of Items</label>
                        <input type="number" name="points" id="points" min="1"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                               required>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded shadow-sm">
                            Save Activity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
