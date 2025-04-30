<div class="bg-white dark:bg-gray-800 w-64 min-h-screen p-4 shadow-sm border-end">
    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600 mb-6 block">
        📊 ACADEX
    </a>

    @php
        $role = Auth::user()->role;
    @endphp

    <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
        @if ($role == 1) {{-- Chairperson --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-400 uppercase mb-2">Chairperson Panel</h4>
                <x-nav-link :href="route('chairperson.instructors')" :active="request()->routeIs('chairperson.instructors')">
                    🧑‍🏫 Manage Instructors
                </x-nav-link>
                <x-nav-link :href="route('chairperson.assignSubjects')" :active="request()->routeIs('chairperson.assignSubjects')">
                    📚 Assign Subjects
                </x-nav-link>
                <x-nav-link :href="route('chairperson.viewGrades')" :active="request()->routeIs('chairperson.viewGrades')">
                    📈 View Grades
                </x-nav-link>
                <x-nav-link :href="route('chairperson.studentsByYear')" :active="request()->routeIs('chairperson.studentsByYear')">
                    👨‍🎓 View Students by Year
                </x-nav-link>
            </div>
        @elseif ($role == 0) {{-- Instructor --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-400 uppercase mb-2">Instructor Panel</h4>
                <x-nav-link :href="route('instructor.manageStudents')" :active="request()->routeIs('instructor.manageStudents')">
                    👨‍🎓 Manage Students
                </x-nav-link>
                <x-nav-link :href="route('instructor.manageGrades')" :active="request()->routeIs('instructor.manageGrades')">
                    📝 Manage Grades
                </x-nav-link>
                <x-nav-link :href="route('instructor.activities')" :active="request()->routeIs('instructor.activities')">
                    📌 Manage Activities
                </x-nav-link>
                <x-nav-link :href="route('instructor.finalGrades')" :active="request()->routeIs('instructor.finalGrades')">
                    📈 Final Grades
                </x-nav-link>
            </div>
        @elseif ($role == 2) {{-- Dean --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-400 uppercase mb-2">Dean Panel</h4>
                <x-nav-link :href="route('dean.instructors')" :active="request()->routeIs('dean.instructors')">
                    🧑‍🏫 View Instructors
                </x-nav-link>
                <x-nav-link :href="route('dean.students')" :active="request()->routeIs('dean.students')">
                    👨‍🎓 View Students
                </x-nav-link>
                <x-nav-link :href="route('dean.grades')" :active="request()->routeIs('dean.grades')">
                    📈 View Grades
                </x-nav-link>
            </div>
        @elseif ($role == 3) {{-- Admin --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-400 uppercase mb-2">Admin Panel</h4>
                <x-nav-link :href="route('admin.departments')" :active="request()->routeIs('admin.departments')">
                    🏢 Manage Departments
                </x-nav-link>
                <x-nav-link :href="route('admin.courses')" :active="request()->routeIs('admin.courses')">
                    📘 Manage Courses
                </x-nav-link>
                <x-nav-link :href="route('admin.subjects')" :active="request()->routeIs('admin.subjects')">
                    📖 Manage Subjects
                </x-nav-link>
                <x-nav-link :href="route('admin.academicPeriods')" :active="request()->routeIs('admin.academicPeriods')">
                    📅 Manage Academic Periods
                </x-nav-link>
            </div>
        @endif

        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                🏠 Dashboard
            </x-nav-link>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 block w-full text-left">
                    🚪 Logout
                </button>
            </form>
        </div>
    </div>
</div>


{{-- Add this style to your app.css or use Tailwind directives --}}
<style>
    .nav-link {
        @apply block py-2 px-2 rounded-md hover:bg-indigo-50 hover:text-indigo-600 transition;
    }
</style>
