@extends('public.layouts.app')

@section('title', 'Edit Profile - SMKN 4 KOTA BOGOR')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Edit Profile Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4 flex items-center gap-4">
                <a href="{{ route('user.profile.show') }}" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-white">Edit Profile</h1>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('user.profile.update') }}" class="px-6 py-8">
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

                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                        placeholder="Albert Florest"
                        required
                    >
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username Field -->
                <div class="mb-6">
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 @error('username') border-red-500 @enderror"
                        placeholder="username"
                    >
                    @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender Field -->
                <div class="mb-6">
                    <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
                    <div class="relative">
                        <select 
                            id="gender" 
                            name="gender"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 appearance-none @error('gender') border-red-500 @enderror"
                        >
                            <option value="" {{ old('gender', $user->gender) === null ? 'selected' : '' }}>Pilih gender (opsional)</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('gender')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number Field -->
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 @error('phone') border-red-500 @enderror"
                        placeholder="contoh: +62 81234567890"
                    >
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field (Read-only) -->
                <div class="mb-8">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        value="{{ $user->email }}"
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed"
                        readonly
                    >
                    <p class="mt-1 text-xs text-gray-500">Email tidak dapat diubah</p>
                </div>

                <!-- Save Button -->
                <button 
                    type="submit" 
                    class="w-full py-4 bg-teal-500 hover:bg-teal-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]"
                >
                    Save
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
