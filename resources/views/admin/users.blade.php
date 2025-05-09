@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    <div class="bg-yellow-500 text-black p-4 rounded-lg mb-6 font-semibold">
        These users have higher access. Add one at your own discretion.
    </div>

    @if ($errors->any())
    <div class="mb-4 p-4 rounded bg-red-100 border border-red-400 text-red-700">
        <strong>Whoops! Something went wrong.</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Trigger Modal -->
    <button onclick="openModal()" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
        Add User
    </button>

    <!-- Modal -->
    <div id="courseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">Add New User</h2>
            <form id="user-form" action="{{ route('admin.storeVerifiedUser') }}" method="POST">
                @csrf

                {{-- Name Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="first_name" :value="__('First Name')" />
                        <x-text-input id="first_name" name="first_name" type="text" placeholder="Juan" class="w-full mt-1" :value="old('first_name')" required />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="middle_name" :value="__('Middle Name')" />
                        <x-text-input id="middle_name" name="middle_name" type="text" placeholder="(optional)" class="w-full mt-1" :value="old('middle_name')" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="last_name" :value="__('Last Name')" />
                        <x-text-input id="last_name" name="last_name" type="text" placeholder="Dela Cruz" class="w-full mt-1" :value="old('last_name')" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                {{-- Email Username --}}
                <div>
                    <x-input-label for="email" :value="__('Email Username')" />
                    <div class="flex rounded-md shadow-sm">
                        <x-text-input id="email" name="email" type="text"
                            placeholder="jdelacruz"
                            class="rounded-r-none w-full mt-1"
                            :value="old('email')"
                            required
                            pattern="^[^@]+$"
                            title="Do not include '@' or domain — just the username." />
                        <span class="inline-flex items-center px-3 rounded-r-md bg-gray-200 border border-l-0 border-gray-300 mt-1 text-sm text-gray-600">@brokenshire.edu.ph</span>
                    </div>

                    {{-- Live warning --}}
                    <p id="email-warning" class="text-sm text-red-600 mt-1 hidden">
                        Please enter only your username — do not include '@' or email domain.
                    </p>

                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- User Role --}}
                <div>
                    <x-input-label for="role" :value="__('User Role')" />
                    <select id="role" name="role" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- Choose Role --</option>
                        <option value="1">Chairperson</option>
                        <option value="2">Dean</option>
                        <option value="3">Admin</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                {{-- Department --}}
                <div>
                    <x-input-label id="department_label" for="department_id" :value="__('Select Department')" />
                    <select id="department_id" name="department_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- Choose Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_description }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                </div>

                {{-- Course --}}
                <div class="transition-opacity duration-300" id="course-wrapper">
                    <x-input-label id="course_label" for="course_id" :value="__('Select Course')" />
                    <select id="course_id" name="course_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- Choose Course --</option>
                        @foreach ($courses as $course)
                            <option value="{{$course->id}}">{{ $course->course_description }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                </div>


                {{-- Password --}}
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" name="password" type="password" class="w-full mt-1" required placeholder="Min. 8 characters" autocomplete="new-password" oninput="checkPassword(this.value)" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />

                    {{-- Password Rules in 2 Columns --}}
                    <div id="password-requirements" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm text-gray-700">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div id="circle-length" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                                <span>Minimum 8 characters</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div id="circle-case" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                                <span>Upper & lowercase</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div id="circle-number" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                                <span>At least 1 number</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div id="circle-special" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                                <span>Special character</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="w-full mt-1" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button id="addButton" type="button" onclick="openConfirmModal()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">Confirm Your Password</h2>
        <p>To make sure this is you, you will need to re-enter your password for safety purposes.</p>

        <form id="confirm-form" action="#" method="POST">
            @csrf
            <x-input-label for="confirm_password" :value="__('Password')" />
            <x-text-input id="confirm_password" name="confirm_password" type="password" class="w-full mt-1" required placeholder="Re-enter your password" />
            <x-input-error :messages="$errors->get('confirm_password')" class="mt-2" />

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Confirm
                </button>
            </div>
        </form>
        </div>
    </div>


    <!-- Table -->
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full bg-white border-collapse border border-gray-300 rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left py-2 px-4 border border-gray-300">Username</th>
                    <th class="text-left py-2 px-4 border border-gray-300">User Role</th>
                </tr>
            </thead>
            <tbody>
                @if($users->count())
                    @foreach ($users as $user)                    
                        <tr>
                            <td class="py-2 px-4 border border-gray-300">{{ $user->name }}</td>
                            <td class="py-2 px-4 border border-gray-300">
                                {{ $user->role == 1 ? 'Chairperson' : ($user->role == 2 ? 'Dean' : ($user->role == 3 ? 'Admin' : 'Unknown')) }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="py-4 px-4 text-center text-gray-500 border border-gray-300">No users found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

</div>

<!-- Modal JS -->
<script>
    function openModal() {
        const modal = document.getElementById('courseModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('courseModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function openConfirmModal() {
        const confirmModal = document.getElementById('confirmModal');
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');
    }

    function closeConfirmModal() {
        const confirmModal = document.getElementById('confirmModal');
        confirmModal.classList.remove('flex');
        confirmModal.classList.add('hidden');
    }

    document.getElementById('confirm-form').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(this); // Collect the form data
    
        fetch("{{ route('admin.confirmUserCreationWithPassword') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Password confirmed, close the modal and submit the user form
                closeConfirmModal();
                alert('Password confirmed successfully');

                // Submit the main form after password confirmation
                submitUserForm();
            } else {
                // Handle error (show an error message)
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error processing your request.');
        });
    });

    // Function to submit the main user form
    function submitUserForm() {
        const userForm = document.getElementById('user-form');
    
        // Check if the form is valid before submitting (optional)
        userForm.submit();
    }

    // Role Change Event
    document.addEventListener('DOMContentLoaded', function () {
        const roleInput = document.getElementById('role');
        const departmentInput = document.getElementById('department_id');
        const courseInput = document.getElementById('course_id');
        const departmentLabel = document.getElementById('department_label');
        const courseLabel = document.getElementById('course_label');

        roleInput.addEventListener('change', function () {
            if (roleInput.value == "3") {  // Admin role
                departmentInput.style.display = 'none';  // Hide department input
                courseInput.style.display = 'none';  // Hide course input
                departmentLabel.style.display = 'none';  // Hide department label
                courseLabel.style.display = 'none';  // Hide course label
                departmentInput.value = "1";  // Default to "1"
                courseInput.value = "2";  // Default to "2"
            } else {
                departmentInput.style.display = 'block';  // Show department input
                courseInput.style.display = 'block';  // Show course input
                departmentLabel.style.display = 'block';  // Show department label
                courseLabel.style.display = 'block';  // Show course label
                departmentInput.value = "";  // Reset department input
                courseInput.value = "";  // Reset course input
            }
        });

        // Check initial role selection on page load
        if (roleInput.value == "3") {  // Admin role
            departmentInput.style.display = 'none';  // Hide department input
            courseInput.style.display = 'none';  // Hide course input
            departmentLabel.style.display = 'none';  // Hide department label
            courseLabel.style.display = 'none';  // Hide course label
            departmentInput.value = "1";  // Default to "1"
            courseInput.value = "2";  // Default to "2"
        }
    });



    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('email');
        const emailWarning = document.getElementById('email-warning');
        const passwordInput = document.getElementById('password');
        const addButton = document.getElementById('addButton');
        
        // Email @ symbol warning
        emailInput.addEventListener('input', () => {
            const hasAtSymbol = emailInput.value.includes('@');
            if (hasAtSymbol) {
                emailWarning.classList.remove('hidden');
                emailInput.setCustomValidity("Please enter only your username, not the full email.");
            } else {
                emailWarning.classList.add('hidden');
                emailInput.setCustomValidity("");
            }
        });

        // Password validation check
        passwordInput.addEventListener('input', () => {
            checkPassword(passwordInput.value);
            toggleAddButton();
        });

        function checkPassword(password) {
            const checks = {
                length: password.length >= 8,
                number: /[0-9]/.test(password),
                case: /[a-z]/.test(password) && /[A-Z]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<script>]/.test(password)
            };

            const update = (id, valid) => {
                const el = document.getElementById(`circle-${id}`);
                el.classList.remove('bg-red-400', 'bg-green-500', 'bg-gray-300');
                el.classList.add(valid ? 'bg-green-500' : 'bg-red-400');
            };

            update('length', checks.length);
            update('number', checks.number);
            update('case', checks.case);
            update('special', checks.special);

            const requirementsBox = document.getElementById('password-requirements');
            const allValid = Object.values(checks).every(Boolean);
            requirementsBox.classList.toggle('hidden', allValid);
        }

        function toggleAddButton() {
            const passwordValid = Array.from(document.querySelectorAll('#password-requirements .bg-green-500')).length === 4;
            addButton.disabled = !passwordValid;
        }
    });
</script>

@endsection
