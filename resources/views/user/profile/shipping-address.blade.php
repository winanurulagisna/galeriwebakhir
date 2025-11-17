@extends('public.layouts.app')

@section('title', 'Shipping Address - SMKN 4 KOTA BOGOR')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Shipping Address Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4 flex items-center gap-4">
                <a href="{{ route('user.profile.show') }}" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-white">Shipping Address</h1>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Address Added</h3>
                    <p class="text-gray-500 text-sm mb-6">You haven't added any shipping address yet</p>
                    
                    <button class="px-6 py-3 bg-teal-500 hover:bg-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                        Add New Address
                    </button>
                </div>
            </div>
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
