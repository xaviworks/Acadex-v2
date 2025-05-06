<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Middle Name (Optional) -->
        <div class="mt-4">
            <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
            <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" />
            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Institutional Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" pattern=".*@brokenshire\.edu\.ph" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <small class="text-gray-500">Must be an @brokenshire.edu.ph email</small>
        </div>

        <!-- Department -->
        <div class="mt-4">
            <x-input-label for="department_id" :value="__('Department')" />
            <select name="department_id" id="department_id" class="block mt-1 w-full rounded border-gray-300 shadow-sm" required>
                <option value="">-- Select Department --</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
        </div>

        <!-- Course -->
        <div class="mt-4">
            <x-input-label for="course_id" :value="__('Course')" />
            <select name="course_id" id="course_id" class="block mt-1 w-full rounded border-gray-300 shadow-sm" required>
                <option value="">-- Select Course --</option>
                {{-- This will be populated via JavaScript --}}
            </select>
            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Dependent Course Dropdown Script -->
    <script>
        document.getElementById('department_id').addEventListener('change', function () {
            const departmentId = this.value;
            const courseSelect = document.getElementById('course_id');
            courseSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/api/departments/${departmentId}/courses`)
                .then(res => res.json())
                .then(data => {
                    courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                    data.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.name;
                        courseSelect.appendChild(option);
                    });
                })
                .catch(() => {
                    courseSelect.innerHTML = '<option value="">Failed to load courses</option>';
                });
        });
    </script>
</x-guest-layout>
