@extends('public.layouts.app')

@section('title', 'Change Password - SMKN 4 KOTA BOGOR')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Change Password Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4 flex items-center gap-4">
                <a href="{{ route('user.profile.show') }}" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-white">Change Password</h1>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('user.profile.update-password') }}" class="px-6 py-8">
                @csrf
                @method('PUT')

                <!-- Error Messages -->
                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Current Password -->
                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 @error('current_password') border-red-500 @enderror"
                        required
                    >
                    @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                        required
                    >
                    @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200"
                        required
                    >
                </div>

                <!-- Save Button -->
                <button 
                    type="submit" 
                    class="w-full py-4 bg-teal-500 hover:bg-teal-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]"
                >
                    Update Password
                </button>
            </form>
        </div>

        <!-- Back to Profile -->
        <div class="mt-6 text-center">
            <a href="{{ route('user.profile.show') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-teal-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="font-medium">Kembali ke Profile</span>
            </a>
        </div>
    </div>
</div>
@endsection
