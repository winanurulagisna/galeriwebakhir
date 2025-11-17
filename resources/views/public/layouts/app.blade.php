<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMKN 4 KOTA BOGOR')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/css/glightbox.min.css">
    
    <!-- Moment.js for time formatting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    
         <!-- Custom styles -->
     <style>
         .hero-slider-container {
             height: 280px;
             position: relative;
             overflow: hidden;
         }
         .slider-image {
             transition: opacity 1s ease-in-out;
         }
         .slider-overlay {
             background: rgba(0, 0, 0, 0.4);
         }
         
         /* Dropdown styles */
         .group:hover .group-hover\:opacity-100 {
             opacity: 1;
         }
         .group:hover .group-hover\:visible {
             visibility: visible;
         }
         
         /* Mobile menu transitions */
         #mobile-menu {
             transition: all 0.3s ease;
         }
                 
         /* Ensure mobile menu is visible when not hidden */
         #mobile-menu:not(.hidden) {
             display: block;
         }
                 
         /* Fix for mobile menu button */
         #mobile-menu-button {
             touch-action: manipulation;
             -webkit-tap-highlight-color: transparent;
             cursor: pointer;
             min-width: 44px;
             min-height: 44px;
             display: flex;
             align-items: center;
             justify-content: center;
         }
                 
         /* Focus styles for mobile menu button */
         #mobile-menu-button:focus {
             outline: 2px solid #ffffff;
             outline-offset: 2px;
         }
                 
         /* Fix for mobile menu positioning */
         #mobile-menu {
             position: absolute;
             top: 100%;
             left: 0;
             right: 0;
             z-index: 1000;
             background: inherit;
             margin-top: 0;
         }
                 
         /* Ensure proper positioning relative to header */
         .site-header {
             position: relative;
         }
         
         /* Ensure dropdowns are above other content */
         .z-50 {
             z-index: 50;
         }
         
         /* Responsive slider */
         @media (max-width: 768px) {
             .hero-slider-container {
                 height: 220px;
             }
         }
         
         @media (max-width: 480px) {
             .hero-slider-container {
                 height: 180px;
             }
         }
         
         /* Scoped Navbar Styles (only affect header) */
        .site-header { background: transparent; z-index: 9999; }
        .site-header .topbar { background-color: #26658c; color: #e8f4ff; }
        .site-header .topbar a { color: #e8f4ff; }
        .site-header .topbar a:hover { color: #ffffff; }
        .site-header .main-nav { background: linear-gradient(180deg, #033e66 0%, #023859 100%); backdrop-filter: saturate(120%); }
        .site-header .nav-link { position: relative; color: #ffffff; padding: 0.55rem 0.45rem; display: inline-flex; align-items: center; font-size: 0.75rem; letter-spacing: .01em; }
        .site-header .nav-link::after { content: ""; position: absolute; left: 0.45rem; right: 0.45rem; bottom: .5rem; height: 2px; background: linear-gradient(90deg,#aee1ff,transparent); transform: scaleX(0); transform-origin: left; transition: transform .25s ease; border-radius: 2px; }
        .site-header .nav-link:hover::after, .site-header .nav-link.active::after { transform: scaleX(1); }
        .site-header .nav-link:hover, .site-header .nav-link.active { color: #d8f1ff; }
        .site-header .dropdown-panel { box-shadow: 0 18px 36px rgba(2,56,89,0.28); border: 1px solid rgba(2,56,89,0.15); }


        .site-header .icon-btn { color: #ffffff; }
        .site-header .icon-btn:hover { color: #aee1ff; }
        .site-header .login-btn { background: linear-gradient(180deg,#1fb6ff,#0091d5); color: #ffffff; padding: 0.4rem 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.8125rem; box-shadow: 0 4px 12px rgba(0,145,213,.2); }
        .site-header .login-btn:hover { filter: brightness(1.05); }
        .site-header .mobile-menu { background-color: #023859; }
        .site-header .mobile-link { color: #ffffff; }
        .site-header .mobile-link:hover { background-color: rgba(255,255,255,0.08); color: #aee1ff; }
     </style>
    
    @yield('styles')
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="site-header shadow-sm sticky top-0 z-50">
        <!-- Main Navigation -->
        <nav class="main-nav">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between">
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-6">
                        <!-- Inline Logo + Text (Desktop) -->
                        <a href="/" class="flex items-center mr-2">
                            <img src="{{ asset('images/LOGO.png') }}" 
                                 alt="SMKN 4 KOTA BOGOR Logo" 
                                 class="h-8 w-8 object-cover rounded-lg mr-2"
                                 onerror="this.src='https://via.placeholder.com/32x32/26658c/FFFFFF?text=SMK4&font=bold'">
                            <div class="leading-tight">
                                <div class="text-white font-bold text-[11px]">SMKN 4 KOTA BOGOR</div>
                                <div class="text-white/80 text-[9px]">Mencetak Generasi Cerdas dan Berkarakter</div>
                            </div>
                        </a>
                        <a href="/" class="nav-link transition duration-300 {{ request()->is('/') ? 'active' : '' }}">Beranda</a>

                        <!-- Galeri Link -->
                        <a href="/gallery" class="nav-link transition duration-300 {{ request()->is('gallery*') ? 'active' : '' }}">Galeri</a>
                        
                        <!-- Berita Link -->
                        <a href="{{ route('berita.index') }}" class="nav-link transition duration-300 {{ request()->is('berita*') ? 'active' : '' }}">Berita</a>

                        <!-- Agenda Link -->
                        <a href="{{ route('agenda.index') }}" class="nav-link transition duration-300 {{ request()->is('agenda*') ? 'active' : '' }}">Agenda</a>

                        <a href="/profil" class="nav-link transition duration-300 {{ request()->is('profil*') ? 'active' : '' }}">Tentang</a>
                    </div>

                    <!-- Mobile Logo + Menu Button -->
                    <a href="/" class="md:hidden py-2 mr-2 flex items-center">
                        <img src="{{ asset('images/LOGO.png') }}" 
                             alt="SMKN 4 KOTA BOGOR Logo" 
                             class="h-8 w-8 object-cover rounded-lg"
                             onerror="this.src='https://via.placeholder.com/32x32/26658c/FFFFFF?text=SMK4&font=bold'">
                    </a>
                    <div class="md:hidden">
                        <button id="mobile-menu-button" type="button" aria-controls="mobile-menu" aria-expanded="false" class="icon-btn focus:outline-none relative z-[10000] pointer-events-auto" style="touch-action: manipulation;">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>

                    <!-- Desktop Right Side -->
                    <div class="hidden md:flex items-center space-x-4 py-2">
                        <!-- User Dropdown Component -->
                        @include('components.user-dropdown')
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden w-full">
            <div class="mobile-menu px-4 pt-2 pb-3 space-y-1 shadow-lg max-h-[70vh] overflow-y-auto" style="background: linear-gradient(180deg, #033e66 0%, #023859 100%);">
                        <a href="/" class="mobile-link block px-3 py-2 rounded-md {{ request()->is('/') ? 'bg-white/10' : '' }}">
                            <i class="fas fa-home mr-2"></i>Beranda
                        </a>

                        <!-- Mobile Galeri -->
                        <a href="/gallery" class="mobile-link block px-3 py-2 rounded-md {{ request()->is('gallery*') ? 'bg-white/10' : '' }}">
                            <i class="fas fa-images mr-2"></i>Galeri
                        </a>
                        
                        <!-- Mobile Berita -->
                        <a href="{{ route('berita.index') }}" class="mobile-link block px-3 py-2 rounded-md {{ request()->is('berita*') ? 'bg-white/10' : '' }}">
                            <i class="fas fa-newspaper mr-2"></i>Berita
                        </a>

                        <!-- Mobile Agenda -->
                        <a href="{{ route('agenda.index') }}" class="mobile-link block px-3 py-2 rounded-md {{ request()->is('agenda*') ? 'bg-white/10' : '' }}">
                            <i class="fas fa-calendar-alt mr-2"></i>Agenda
                        </a>

                        <a href="/profil" class="mobile-link block px-3 py-2 rounded-md {{ request()->is('profil*') ? 'bg-white/10' : '' }}">
                            <i class="fas fa-info-circle mr-2"></i>Tentang
                        </a>

                        <!-- Mobile Auth -->
                        @auth
                            <div class="px-3 py-2 border-t border-white/10 mt-2 pt-2">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-white font-semibold text-sm">{{ auth()->user()->name }}</p>
                                        <p class="text-white/70 text-xs">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                @if(!auth()->user()->hasVerifiedEmail())
                                    <a href="{{ route('verification.notice') }}" class="mobile-link block px-3 py-2 rounded-md bg-yellow-500/20 text-yellow-300 text-sm mb-2">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>Verifikasi Email
                                    </a>
                                @endif
                            </div>
                            <a href="{{ route('user.profile.show') }}" class="mobile-link block px-3 py-2 rounded-md {{ request()->is('user/profile*') ? 'bg-white/10' : '' }}">
                                <i class="fas fa-user mr-2"></i>Profile Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="mobile-link w-full text-left px-3 py-2 rounded-md text-red-300">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                                </button>
                            </form>
                        @else
                            <div class="px-3 py-2 border-t border-white/10 mt-2 pt-2 space-y-2">
                                <a href="{{ route('login') }}" class="block w-full text-center px-3 py-1.5 text-xs text-white border border-white/30 rounded-lg hover:bg-white/10 transition-all">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" class="block w-full text-center px-3 py-1.5 text-xs text-white bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg shadow-md hover:shadow-lg transition-all">
                                    Daftar
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Notification Component -->
    @include('components.notification')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white" style="background-color:#023859;">
        <div class="container mx-auto px-3 py-4 sm:px-4 sm:py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-8">
                <!-- School Info -->
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-white">SMKN 4 KOTA BOGOR</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Jl. Raya Tajur, Kp. Buntar RT.02/RW.08, Kel. Muara sari, Kec. Bogor Selatan, RT.03/RW.08, Muarasari, Kec. Bogor Sel., Kota Bogor, Jawa Barat 16137
                    </p>
                    
                    <!-- Google Maps Embed -->
                    <div class="w-full h-24 sm:h-28 md:h-32 lg:h-36 rounded-lg overflow-hidden shadow">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.04983961244!2d106.82211897499403!3d-6.640733393353795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c8b16ee07ef5%3A0x14ab253dd267de49!2sSMK%20Negeri%204%20Bogor%20(Nebrazka)!5e0!3m2!1sid!2sid!4v1756719144541!5m2!1sid!2sid"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi SMKN 4 KOTA BOGOR"
                            class="w-full h-full">
                        </iframe>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="flex space-x-2 pt-1">
                        <a href="https://www.instagram.com/smkn4kotabogor" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors duration-300" aria-label="Instagram">
                            <i class="fab fa-instagram text-base"></i>
                        </a>
                        <a href="https://www.youtube.com/@smknegeri4bogor905" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors duration-300" aria-label="YouTube">
                            <i class="fab fa-youtube text-base"></i>
                        </a>
                        <a href="https://wa.me/6289516445505" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors duration-300" aria-label="WhatsApp">
                            <i class="fab fa-whatsapp text-base"></i>
                        </a>
                        <a href="mailto:ambertheory8@gmail.com" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors duration-300" aria-label="Email">
                            <i class="fas fa-envelope text-base"></i>
                        </a>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-white">Kirim Pesan</h3>
                    <p class="text-gray-300 text-xs">Sampaikan saran, kritik, atau pertanyaan Anda kepada kami.</p>
                    
                    <form action="{{ route('message.store') }}" method="POST" class="space-y-3" @guest onsubmit="event.preventDefault(); alert('Anda harus login terlebih dahulu untuk mengirim pesan.'); return false;" @endguest>
                        @csrf
                        <div>
                            <input type="text" 
                                   name="name" 
                                   placeholder="Nama Lengkap" 
                                   value="{{ old('name', auth()->user()->name ?? '') }}"
                                   @guest readonly @endguest
                                   class="w-full px-3 py-1.5 rounded-lg text-white placeholder-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-[#26658c] focus:border-transparent @error('name') border-red-500 focus:ring-red-500 @enderror transition-all duration-300"
                                   style="background-color:#26658c;border:1px solid #1f4f6a;">
                            @error('name')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input type="email" 
                                   name="email" 
                                   placeholder="Email" 
                                   value="{{ old('email', auth()->user()->email ?? '') }}"
                                   @guest readonly @endguest
                                   class="w-full px-3 py-1.5 rounded-lg text-white placeholder-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-[#26658c] focus:border-transparent @error('email') border-red-500 focus:ring-red-500 @enderror transition-all duration-300"
                                   style="background-color:#26658c;border:1px solid #1f4f6a;">
                            @error('email')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <textarea name="message" 
                                      placeholder="Tulis pesan Anda di sini..." 
                                      rows="3" 
                                      @guest readonly @endguest
                                      class="w-full px-3 py-1.5 rounded-lg text-white placeholder-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-[#26658c] focus:border-transparent @error('message') border-red-500 focus:ring-red-500 @enderror transition-all duration-300 resize-none" style="background-color:#26658c;border:1px solid #1f4f6a;">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Optional Rating -->
                        <div>
                            <label for="rating" class="block text-gray-200 text-xs mb-1">Penilaian</label>
                            <select id="rating" name="rating" @guest disabled @endguest class="w-full px-3 py-1.5 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#26658c] transition-all duration-300" style="background-color:#26658c;border:1px solid #1f4f6a;">
                                <option value="" {{ old('rating') === null ? 'selected' : '' }}>Tidak ada</option>
                                <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>Keren Sekali</option>
                                <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>Bagus</option>
                            </select>
                            @error('rating')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @auth
                        <button type="submit" class="w-full bg-[#26658c] hover:bg-[#205676] text-white font-semibold py-1.5 px-3 rounded-md text-sm transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#26658c] focus:ring-offset-2 focus:ring-offset-gray-800">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Pesan
                        </button>
                        @else
                        <div class="relative group">
                            <button type="button" class="w-full bg-gray-500 cursor-not-allowed text-white font-semibold py-1.5 px-3 rounded-md text-sm opacity-60">
                                <i class="fas fa-lock mr-2"></i>
                                Kirim Pesan
                            </button>
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                Anda harus login terlebih dahulu untuk mengirim pesan
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                    <div class="border-4 border-transparent border-t-gray-900"></div>
                                </div>
                            </div>
                        </div>
                        @endauth
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="py-3" style="background-color:#023859;">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
                    <div class="text-center md:text-left">
                        <p class="text-gray-400 text-xs">&copy; 2024 SMKN 4 KOTA BOGOR. All rights reserved.</p>
                    </div>
                    <div class="flex space-x-4 text-xs text-gray-400">
                        <a href="#" class="hover:text-white transition-colors duration-300 text-[10px] md:text-xs">Privacy Policy</a>
                        <a href="#" class="hover:text-white transition-colors duration-300 text-[10px] md:text-xs">Terms of Service</a>
                        <a href="#" class="hover:text-white transition-colors duration-300 text-[10px] md:text-xs">Contact</a>
                        <a href="{{ url('login.php') }}" class="hover:text-white transition-colors duration-300 text-[10px] md:text-xs">Login Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    @yield('scripts')
    
    <!-- Navbar JavaScript -->
    <script>
        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded and parsed');
            // Initialize GLightbox (guarded)
            try {
                if (typeof GLightbox !== 'undefined') {
                    const lightbox = GLightbox({
                        selector: '.glightbox',
                        touchNavigation: true,
                        loop: true,
                        autoplayVideos: true,
                        plyr: {
                            config: { ratio: '16:9' }
                        }
                    });
                }
            } catch (e) {
                console.warn('GLightbox initialization failed:', e);
            }

            // Initialize tooltips (only when Bootstrap JS is available)
            if (typeof bootstrap !== 'undefined' && bootstrap?.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Initialize popovers (only when Bootstrap JS is available)
            if (typeof bootstrap !== 'undefined' && bootstrap?.Popover) {
                const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
            }

            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    console.log('Mobile menu button clicked');
                    const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
                    mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                    mobileMenu.classList.toggle('hidden');
                    console.log('Mobile menu visibility toggled');
                });
            } else {
                console.log('Mobile menu elements not found');
            }

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                console.log('Document click event fired');
                if (mobileMenu && mobileMenuButton && !mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    console.log('Click outside mobile menu detected');
                    // Only hide the menu if it's currently visible
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });
    </script>
    
    <!-- Image URL Encoder for handling special characters in filenames -->
    <script src="{{ asset('js/image-url-encoder.js') }}"></script>
    
    <!-- Additional JavaScript for search functionality -->
    <script>
        function groupResultsByCategory(results) {
            const grouped = {};
            results.forEach(result => {
                if (!grouped[result.category]) {
                    grouped[result.category] = [];
                }
                grouped[result.category].push(result);
            });
            return grouped;
        }

        function getTotalDataCount() {
            return searchData.ekstrakurikuler.length + 
                   searchData.berita.length + 
                   searchData.galeri.length + 
                   searchData.profil.length;
        }

        function createResultElement(result, index) {
            const div = document.createElement('div');
            div.className = 'search-result-item search-result bg-white p-5 cursor-pointer mb-3';
            div.style.animationDelay = `${index * 0.05}s`;
            
            // Get current search query for highlighting
            const searchInput = document.getElementById('globalSearchInput');
            const query = searchInput ? searchInput.value.trim() : '';
            
            // Highlight function
            function highlightText(text, query) {
                if (!query) return text;
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<mark>$1</mark>');
            }
            
            // Relevance indicator - simplified
            function getRelevanceIndicator(score) {
                if (score >= 80) return '<span class="text-green-600 text-xs font-medium">Sangat Relevan</span>';
                if (score >= 50) return '<span class="text-blue-600 text-xs font-medium">Relevan</span>';
                if (score >= 20) return '<span class="text-yellow-600 text-xs font-medium">Cukup Relevan</span>';
                return '<span class="text-gray-500 text-xs font-medium">Kurang Relevan</span>';
            }
            
            div.innerHTML = `
                <div class="flex items-start space-x-4">
                    <div class="search-result-icon flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="${result.icon} text-gray-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-base font-semibold text-gray-900 leading-tight">${highlightText(result.title, query)}</h4>
                            <div class="flex items-center space-x-2 ml-3">
                                <span class="px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded">${result.category}</span>
                                ${getRelevanceIndicator(result.score)}
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed mb-3">${highlightText(result.description, query)}</p>
                        <div class="flex items-center text-xs text-gray-400">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            <span>Klik untuk membuka</span>
                        </div>
                    </div>
                </div>
            `;
            
            div.addEventListener('click', () => {
                window.location.href = result.url;
            });
            
            return div;
        }

        function hideAllStates() {
            document.getElementById('searchEmpty').classList.add('hidden');
            document.getElementById('searchLoading').classList.add('hidden');
            document.getElementById('searchNoResults').classList.add('hidden');
        }

        function showClearButton() {
            document.getElementById('searchClear').classList.remove('hidden');
        }

        function hideClearButton() {
            document.getElementById('searchClear').classList.add('hidden');
        }

        // Search suggestion function
        function searchSuggestion(query) {
            const searchInput = document.getElementById('globalSearchInput');
            searchInput.value = query;
            searchInput.focus();
            
            // Trigger search
            const event = new Event('input', { bubbles: true });
            searchInput.dispatchEvent(event);
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('searchModal');
            if (e.target === modal) {
                closeSearchModal();
            }
        });

        // Keyboard shortcut for search (Ctrl+K or Cmd+K)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openSearchModal();
            }
        });
    </script>
</body>
</html>
