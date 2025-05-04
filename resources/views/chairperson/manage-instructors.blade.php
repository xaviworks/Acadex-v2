@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Manage Instructors</h1>

    {{-- Add Button --}}
    <div class="mb-6">
        <button onclick="openModal('addInstructorModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded">
            + Add New Instructor
        </button>
    </div>

    {{-- Instructors Table --}}
    @if($instructors->isEmpty())
        <div class="bg-blue-100 text-blue-800 p-4 rounded">
            No instructors found.
        </div>
    @else
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Name</th>
                        <th class="px-4 py-2 text-left font-semibold">Email</th>
                        <th class="px-4 py-2 text-center font-semibold">Status</th>
                        <th class="px-4 py-2 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instructors as $instructor)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $instructor->name }}</td>
                            <td class="px-4 py-2">{{ $instructor->email }}</td>
                            <td class="px-4 py-2 text-center">
                                @if($instructor->is_active)
                                    <span class="inline-block bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded">Active</span>
                                @else
                                    <span class="inline-block bg-red-200 text-red-800 text-xs font-semibold px-2 py-1 rounded">Deactivated</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($instructor->is_active)
                                    <form action="{{ route('chairperson.deactivateInstructor', $instructor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate this instructor?');">
                                        @csrf
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded text-sm">
                                            Deactivate
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm">No Actions</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Modal --}}
<div id="addInstructorModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-xl mx-auto rounded-lg shadow-lg p-6 relative">
        {{-- Close Button --}}
        <button onclick="closeModal('addInstructorModal')" class="absolute top-2 right-3 text-gray-600 text-xl font-bold">&times;</button>

        <h2 class="text-xl font-bold mb-4">Add New Instructor</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('chairperson.storeInstructor') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block font-semibold mb-1">Name</label>
                <input type="text" name="name" class="w-full border px-3 py-2 rounded" value="{{ old('name') }}" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" class="w-full border px-3 py-2 rounded" value="{{ old('email') }}" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Password</label>
                <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded">
                    Create Instructor
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Scripts --}}
@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }
</script>
@endpush

@endsection
