@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Add New Instructor</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('chairperson.storeInstructor') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block font-semibold mb-1">Name:</label>
            <input type="text" name="name" class="w-full border px-3 py-2 rounded" value="{{ old('name') }}" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Email:</label>
            <input type="email" name="email" class="w-full border px-3 py-2 rounded" value="{{ old('email') }}" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Password:</label>
            <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Confirm Password:</label>
            <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded" required>
        </div>

        {{-- Remove Department and Course fields since they are set automatically --}}

        <div class="text-right">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded">
                Create Instructor
            </button>
        </div>
    </form>
</div>
@endsection
