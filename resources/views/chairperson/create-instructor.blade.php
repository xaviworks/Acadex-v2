@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 bg-white rounded-4 shadow-lg">
    <h1 class="text-2xl font-bold mb-6">
        <i class="bi bi-person-plus-fill text-success me-2"></i>
        Add New Instructor
    </h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
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
            <label class="block font-medium text-sm mb-1">Name</label>
            <input type="text" name="name"
                   class="w-full border border-gray-300 shadow-sm px-3 py-2 rounded focus:ring-2 focus:ring-green-300 focus:outline-none"
                   value="{{ old('name') }}" required>
        </div>

        <div>
            <label class="block font-medium text-sm mb-1">Email</label>
            <input type="email" name="email"
                   class="w-full border border-gray-300 shadow-sm px-3 py-2 rounded focus:ring-2 focus:ring-green-300 focus:outline-none"
                   value="{{ old('email') }}" required>
        </div>

        <div>
            <label class="block font-medium text-sm mb-1">Password</label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 shadow-sm px-3 py-2 rounded focus:ring-2 focus:ring-green-300 focus:outline-none"
                   required>
        </div>

        <div>
            <label class="block font-medium text-sm mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-gray-300 shadow-sm px-3 py-2 rounded focus:ring-2 focus:ring-green-300 focus:outline-none"
                   required>
        </div>

        <div class="text-end">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded shadow-sm transition">
                Create Instructor
            </button>
        </div>
    </form>
</div>
@endsection
