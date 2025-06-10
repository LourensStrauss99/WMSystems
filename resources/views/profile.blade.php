{{-- filepath: resources/views/profile.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded shadow mt-10">
    <h2 class="text-2xl font-bold mb-6">My Profile</h2>

    {{-- Show profile photo if exists --}}
    @if(auth()->user()->profile_photo)
        <div class="mb-4 flex justify-center">
            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                 alt="Profile Photo"
                 class="rounded-full w-32 h-32 object-cover border-4 border-blue-300 shadow">
        </div>
    @else
        <div class="mb-4 flex justify-center">
            <img src="{{ asset('images/default-avatar.png') }}"
                 alt="Default Profile Photo"
                 class="rounded-full w-32 h-32 object-cover border-4 border-gray-300 shadow">
        </div>
    @endif

    {{-- Profile update form --}}
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block font-bold mb-2">Name</label>
            <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-2">Email</label>
            <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-2">Profile Photo</label>
            <input type="file" name="profile_photo" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Profile</button>
    </form>
</div>
@endsection