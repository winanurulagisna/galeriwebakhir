<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - SMKN 4 KOTA BOGOR')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Base font size reduction - v2 */
        body {
            font-size: 14px !important;
        }
        
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        
        .content-transition {
            transition: margin-left 0.3s ease-in-out;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar-mobile {
                transform: translateX(-100%);
            }
            
            .sidebar-mobile.open {
                transform: translateX(0);
            }
            
            body {
                font-size: 13px;
            }
        }
        
        /* Compact mode for all elements */
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .text-compact {
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-56 bg-white shadow-lg sidebar-transition md:translate-x-0 sidebar-mobile" 
         :class="{ 'open': sidebarOpen }">
        
        <!-- Logo & Header -->
        <div class="flex items-center justify-center h-14 bg-blue-600 text-white">
            <a href="{{ url('/admin') }}" class="flex items-center space-x-2 hover:text-white/90">
                <i class="fas fa-school text-lg"></i>
                <span class="text-base font-bold">SMKN 4 BOGOR</span>
            </a>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="mt-4 px-3">
            <div class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ url('/admin') }}" 
                   class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->is('admin') || request()->is('admin/index.php') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-tachometer-alt w-4 h-4 mr-2"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Section: Konten -->
                <div class="px-3 pt-3">
                    <div class="text-[10px] font-semibold tracking-widest text-gray-400 uppercase">KONTEN</div>
                </div>

                <!-- Halaman Berita Terkini -->
                <a href="{{ url('/admin/pages/berita.php') }}" 
                   class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->is('admin/pages/berita.php') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-newspaper w-4 h-4 mr-2"></i>
                    <span>Halaman Berita Terkini</span>
                </a>

                <!-- Agenda -->
                <a href="{{ url('/admin/pages/agenda.php') }}" 
                   class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->is('admin/pages/agenda.php') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-alt w-4 h-4 mr-2"></i>
                    <span>Agenda</span>
                </a>

                <!-- Section: Media -->
                <div class="px-3 pt-3">
                    <div class="text-[10px] font-semibold tracking-widest text-gray-400 uppercase">MEDIA</div>
                </div>

                <!-- Halaman Galeri -->
                <a href="{{ url('/admin/pages/galeri.php') }}" 
                   class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->is('admin/pages/galeri.php') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-images w-4 h-4 mr-2"></i>
                    <span>Halaman Galeri</span>
                </a>

                <!-- Section: Manajemen -->
                <div class="px-3 pt-3">
                    <div class="text-[10px] font-semibold tracking-widest text-gray-400 uppercase">MANAJEMEN</div>
                </div>

                <!-- Pesan -->
                <a href="{{ url('/admin/pages/pesan.php') }}" 
                   class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->is('admin/pages/pesan.php') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-envelope w-4 h-4 mr-2"></i>
                    <span>Pesan</span>
                </a>

                <!-- Komentar Foto -->
                <a href="{{ url('/admin/pages/komentar_foto.php') }}" 
                   class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->is('admin/pages/komentar_foto.php') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-comments w-4 h-4 mr-2"></i>
                    <span>Komentar Foto</span>
                </a>

                <!-- Pengaturan Akun -->
                <a href="{{ url('/admin/pages/profile.php') }}" 
                   class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->is('admin/pages/profile.php') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-user-cog w-4 h-4 mr-2"></i>
                    <span>Pengaturan Akun</span>
                </a>

                <!-- Logout -->
                <a href="{{ url('/logout.php') }}" 
                   class="flex items-center px-3 py-2 text-sm text-red-600 rounded-lg hover:bg-red-50 transition-colors mt-3">
                    <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="md:ml-56 content-transition">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-4 py-3">
                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="md:hidden p-1.5 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                
                <!-- Page Title (clickable to Dashboard) -->
                <div class="flex items-center">
                    <a href="{{ url('/admin') }}" class="text-lg font-bold text-gray-900 hover:text-blue-600 transition-colors">
                        @yield('page-title', 'Dashboard')
                    </a>
                </div>
                
                <!-- User Info -->
                <div class="flex items-center space-x-3">
                    <div class="hidden md:block text-right">
                        <p class="text-xs font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-500">Administrator</p>
                    </div>
                    <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-xs"></i>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="p-4">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded-lg flex items-center text-sm">
                    <i class="fas fa-check-circle mr-2 text-xs"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-lg flex items-center text-sm">
                    <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle mr-2 text-xs"></i>
                        <span class="font-medium text-sm">Terjadi kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Page Content -->
            @yield('content')
        </main>
    </div>
    
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>
    
    @yield('scripts')
</body>
</html>
