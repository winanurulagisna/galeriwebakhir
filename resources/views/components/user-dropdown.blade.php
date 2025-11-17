<!-- User Profile Dropdown -->
@auth
<div class="relative group">
    <button type="button" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 transition-all duration-200" id="user-menu-button">
        <!-- User Avatar -->
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        
        <!-- User Name (Hidden on mobile) -->
        <span class="hidden md:block text-white font-medium text-sm">
            {{ Str::limit(auth()->user()->name, 15) }}
        </span>
        
        <!-- Dropdown Icon -->
        <svg class="w-4 h-4 text-white transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
        
        <!-- Verification Badge -->
        @if(auth()->user()->hasVerifiedEmail())
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full" title="Email terverifikasi"></span>
        @else
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-500 border-2 border-white rounded-full animate-pulse" title="Email belum terverifikasi"></span>
        @endif
    </button>
    
    <!-- Dropdown Menu -->
    <div class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform group-hover:translate-y-0 translate-y-2 z-50 border border-gray-100">
        <!-- User Info Header -->
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ auth()->user()->email }}
                    </p>
                    @if(auth()->user()->hasVerifiedEmail())
                        <span class="inline-flex items-center gap-1 text-xs text-green-600 mt-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Terverifikasi
                        </span>
                    @else
                        <a href="{{ route('verification.notice') }}" class="inline-flex items-center gap-1 text-xs text-yellow-600 hover:text-yellow-700 mt-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Verifikasi Email
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Menu Items -->
        <div class="py-2">
            <!-- Profile -->
            <a href="{{ route('user.profile.show') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="font-medium">Profile</span>
            </a>
        </div>
        
        <div class="border-t border-gray-100"></div>
        
        <!-- Logout -->
        <div class="py-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>
@else
<!-- Login/Register Buttons for Guest -->
<div class="flex items-center gap-2">
    <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-medium text-white hover:bg-white/10 rounded-lg transition-all duration-200">
        Masuk
    </a>
    <a href="{{ route('register') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
        Daftar
    </a>
</div>
@endauth
