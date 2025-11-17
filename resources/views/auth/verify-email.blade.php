@extends('public.layouts.app')

@section('title', 'Verifikasi Email - SMKN 4 KOTA BOGOR')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #023859 0%, #26658C 50%, #54ACBF 100%);">
    <div class="max-w-md w-full space-y-8">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-lg">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto h-20 w-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mb-4 animate-pulse">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="login-card">
                    <div class="login-header">
                        <div class="login-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2 class="login-title">Verifikasi Email Anda</h2>
                        <p class="login-subtitle">
                            Masukkan kode OTP 6 digit yang telah dikirim ke email Anda
                        </p>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert-success">
                            <i class="fas fa-check-circle"></i>
                            Kode OTP baru telah dikirim ke email Anda!
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

            <!-- Resend Button -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:scale-[1.02]">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Atau</span>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <div class="mt-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>

            <!-- Help Text -->
            <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 text-center">
                    <strong>Tidak menerima email?</strong><br>
                    Periksa folder spam/junk Anda atau klik tombol "Kirim Ulang" di atas.
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="text-sm text-white hover:text-gray-200 transition-colors inline-flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
