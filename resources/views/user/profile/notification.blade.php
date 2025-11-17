@extends('public.layouts.app')

@section('title', 'Notification - SMKN 4 KOTA BOGOR')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Notification Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4 flex items-center gap-4">
                <a href="{{ route('user.profile.show') }}" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-white">Notification</h1>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Notifications</h3>
                    <p class="text-gray-500 text-sm">You don't have any notifications yet</p>
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
