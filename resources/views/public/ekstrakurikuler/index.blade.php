@extends('public.layouts.app')

@section('title', 'Ekstrakurikuler - SMKN 4 KOTA BOGOR')

@section('head')
    
    <style>
        .hero-slider-container {
            height: 500px;
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
        
        /* Ensure dropdowns are above other content */
        .z-50 {
            z-index: 50;
        }
        
        /* Responsive slider */
        @media (max-width: 768px) {
            .hero-slider-container {
                height: 400px;
            }
        }
        
        @media (max-width: 480px) {
            .hero-slider-container {
                height: 300px;
            }
        }
        
        /* Enhanced card styling */
        .ekstrakurikuler-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .ekstrakurikuler-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: translateY(-8px) scale(1.02);
        }
        
        .ekstrakurikuler-card .card-image {
            transition: transform 0.3s ease;
        }
        
        .ekstrakurikuler-card:hover .card-image {
            transform: scale(1.05);
        }
        
        /* Gradient overlay enhancement */
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.7) 100%);
        }
        
        /* Icon hover effect */
        .icon-container {
            transition: all 0.3s ease;
        }
        
        .ekstrakurikuler-card:hover .icon-container {
            transform: scale(1.1) rotate(5deg);
            background: rgba(255, 255, 255, 0.4);
        }
        
        /* Status badge enhancement */
        .status-badge {
            transition: all 0.3s ease;
        }
        
        .ekstrakurikuler-card:hover .status-badge {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Modern achievement cards */
        .achievement-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .achievement-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .achievement-card:hover::before {
            transform: scaleX(1);
        }
        
        .achievement-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .achievement-icon {
            transition: all 0.3s ease;
        }
        
        .achievement-card:hover .achievement-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .achievement-rank {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .achievement-year {
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            color: #6b7280;
            font-weight: 600;
        }
        
        /* Global Search Modal - Clean & Modern */
        .search-modal {
            backdrop-filter: blur(12px);
            background: rgba(0, 0, 0, 0.4);
        }
        
        .search-container {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 16px;
        }
        
        .search-input {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease;
            border-radius: 12px;
        }
        
        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: #ffffff;
        }
        
        .search-result {
            transition: all 0.2s ease;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            background: #ffffff;
        }
        
        .search-result:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #e5e7eb;
        }
        
        .search-result-icon {
            transition: all 0.2s ease;
        }
        
        .search-result:hover .search-result-icon {
            transform: scale(1.05);
        }
        
        .search-loading {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        .search-empty {
            animation: fadeInUp 0.4s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .search-result-item {
            animation: slideInUp 0.3s ease-out;
            animation-fill-mode: both;
        }
        
        .search-result-item:nth-child(1) { animation-delay: 0.05s; }
        .search-result-item:nth-child(2) { animation-delay: 0.1s; }
        .search-result-item:nth-child(3) { animation-delay: 0.15s; }
        .search-result-item:nth-child(4) { animation-delay: 0.2s; }
        .search-result-item:nth-child(5) { animation-delay: 0.25s; }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Search highlight - subtle */
        mark {
            background: #fef3c7;
            color: #92400e;
            font-weight: 500;
            padding: 0.125rem 0.25rem;
            border-radius: 4px;
        }
        
        /* Search suggestion buttons - clean */
        .search-suggestion-btn {
            transition: all 0.2s ease;
            border-radius: 8px;
        }
        
        .search-suggestion-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Clean search header */
        .search-header {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }
        
        /* Clean search results counter */
        .search-counter {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        
        /* Clean category headers */
        .search-category-header {
            color: #6b7280;
            font-weight: 500;
        }
        
        /* Clean top results section */
        .search-top-results {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
   
    

    <main class="container mx-auto px-6 py-12">
        <div class="text-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold mb-2 text-[#023859]">Ekstrakurikuler</h1>
            <p class="text-sm md:text-base max-w-3xl mx-auto text-[#023859]">Kembangkan bakat dan minat melalui berbagai kegiatan ekstrakurikuler yang menarik dan bermanfaat</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-12">
            @if(isset($ekstrakurikuler) && $ekstrakurikuler->count() > 0)
                @foreach($ekstrakurikuler as $eks)
                <a href="{{ route('ekstrakurikuler.show', $eks) }}" class="bg-white rounded-2xl shadow-md hover:shadow-lg transition group ring-1 ring-black/5">
                    <div class="relative aspect-square overflow-hidden rounded-t-2xl">
                        @php
                            $eksImage = $eks->photos->first() ? \App\Helpers\ImageUrlHelper::getSafeImageUrl($eks->photos->first()->file) : '/images/default-ekstrakurikuler.jpg';
                            $eksDesc = $eks->description ?? 'Ekstrakurikuler ' . ($eks->title ?? '');
                        @endphp
                        <img src="{{ $eksImage }}" alt="{{ $eks->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <!-- Arrow overlay button top-right -->
                        <button type="button"
                                class="absolute top-3 right-3 w-9 h-9 rounded-full bg-black/35 hover:bg-black/50 backdrop-blur-sm flex items-center justify-center text-white shadow focus:outline-none focus:ring-2 focus:ring-white/50"
                                aria-label="Lihat detail {{ $eks->title }}"
                                onclick="openEksModal({ title: @json($eks->title), image: @json($eksImage), description: @json($eksDesc) })">
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                        <!-- Title overlay bottom-left -->
                        <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/60 via-black/20 to-transparent">
                            <h3 class="text-white font-extrabold text-lg drop-shadow">{{ $eks->title }}</h3>
                            <p class="text-white/90 text-xs -mt-0.5">Ekstrakurikuler</p>
                        </div>
                    </div>
                    <!-- Footer pill -->
                    <div class="px-4 py-3 flex items-center justify-start">
                        <div class="inline-flex items-center gap-2 text-[#023859] text-sm">
                            <i class="fas fa-users"></i>
                            <span class="px-3 py-1 rounded-full text-xs font-medium" style="background-color: rgba(2,56,89,0.08);">Aktif</span>
                        </div>
                    </div>
                </a>
                @endforeach
            @else
                <div class="ekstrakurikuler-card bg-white rounded-xl shadow-lg overflow-hidden group">
                    <div class="h-56 bg-cover bg-center relative card-image" style="background-image: url('http://127.0.0.1:8000/images/paskibeks.JPG')">
                        <div class="absolute inset-0 gradient-overlay"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="text-white text-xl font-bold mb-2">Paskibra</h3>
                            <p class="text-white/90 text-sm">Pasukan Pengibar Bendera</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="icon-container bg-white/20 backdrop-blur-sm rounded-full p-2">
                                <i class="fas fa-flag text-white text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="mb-4 leading-relaxed text-[#023859]">Mengembangkan jiwa kepemimpinan dan nasionalisme melalui kegiatan pengibaran bendera dan latihan baris-berbaris.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-[#023859]">
                                <i class="fas fa-users mr-2 text-[#023859]"></i>
                                <span>30 Siswa</span>
                            </div>
                            <div class="status-badge px-3 py-1 rounded-full text-xs font-medium text-[#023859]" style="background-color: rgba(2,56,89,0.08);">Aktif</div>
                        </div>
                    </div>
                </div>

                <a href="#" class="bg-white rounded-xl shadow-md group">
                    <div class="relative aspect-square overflow-hidden rounded-t-xl" style="background-image: url('http://127.0.0.1:8000/images/pramukaeks.JPG'); background-size: cover; background-position: center;"></div>
                    <div class="p-3"><h3 class="font-bold text-[#023859] text-sm md:text-base">Album Pramuka</h3></div>
                </a>

                <a href="#" class="bg-white rounded-xl shadow-md group">
                    <div class="relative aspect-square overflow-hidden rounded-t-xl" style="background-image: url('http://127.0.0.1:8000/images/silateks.JPG'); background-size: cover; background-position: center;"></div>
                    <div class="p-3"><h3 class="font-bold text-[#023859] text-sm md:text-base">Album Silat</h3></div>
                </a>

                <a href="#" class="bg-white rounded-xl shadow-md group">
                    <div class="relative aspect-square overflow-hidden rounded-t-xl" style="background-image: url('http://127.0.0.1:8000/images/futsaleks.JPG'); background-size: cover; background-position: center;"></div>
                    <div class="p-3"><h3 class="font-bold text-[#023859] text-sm md:text-base">Album Futsal</h3></div>
                </a>

                <a href="#" class="bg-white rounded-xl shadow-md group">
                    <div class="relative aspect-square overflow-hidden rounded-t-xl" style="background-image: url('http://127.0.0.1:8000/images/paduansuaraeks.JPG'); background-size: cover; background-position: center;"></div>
                    <div class="p-3"><h3 class="font-bold text-[#023859] text-sm md:text-base">Album Paduan Suara</h3></div>
                </a>

                <a href="#" class="bg-white rounded-xl shadow-md group">
                    <div class="relative aspect-square overflow-hidden rounded-t-xl" style="background-image: url('http://127.0.0.1:8000/images/pmreks.JPG'); background-size: cover; background-position: center;"></div>
                    <div class="p-3"><h3 class="font-bold text-[#023859] text-sm md:text-base">Album PMR</h3></div>
                </a>
            @endif
        </div>
    </main>

    <!-- Ekstrakurikuler Detail Modal -->
    <div id="eksModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEksModal()"></div>
        <div class="relative z-10 max-w-2xl mx-auto my-8 px-4">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="relative h-56 md:h-80 bg-gray-100">
                    <img id="eksModalImage" src="" alt="Detail Ekstrakurikuler" class="w-full h-full object-cover" />
                    <button type="button" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center" onclick="closeEksModal()" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5 md:p-6">
                    <h3 id="eksModalTitle" class="text-xl md:text-2xl font-bold text-[#023859] mb-2">Judul</h3>
                    <p id="eksModalDesc" class="text-sm md:text-base text-gray-700">Deskripsi</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEksModal(data) {
            try {
                const modal = document.getElementById('eksModal');
                if (!modal) return;
                document.getElementById('eksModalTitle').textContent = data.title || 'Ekstrakurikuler';
                document.getElementById('eksModalDesc').textContent = data.description || '';
                const imgEl = document.getElementById('eksModalImage');
                imgEl.src = data.image || '/images/default-ekstrakurikuler.jpg';
                imgEl.alt = data.title || 'Ekstrakurikuler';
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } catch (e) { console.error('Failed to open modal', e); }
        }

        function closeEksModal() {
            const modal = document.getElementById('eksModal');
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', function(evt){
            if (evt.key === 'Escape') closeEksModal();
        });
    </script>

    <div id="searchModal" class="search-modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="search-container relative w-full max-w-xl mx-auto p-6 transition-all transform-gpu scale-95 opacity-0 duration-300 ease-out">
            <div class="search-header flex items-center justify-between py-2 px-4 mb-4 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-800">Cari di Website Kami</h3>
                <button onclick="closeSearchModal()" class="text-gray-500 hover:text-gray-800 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="relative mb-4">
                <input type="text"
                       id="searchQueryInput"
                       placeholder="Cari berita, profil, atau ekstrakurikuler..."
                       class="search-input w-full px-4 py-3 pr-16 text-gray-800 text-base focus:outline-none">
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            <div id="searchResults" class="max-h-80 overflow-y-auto space-y-2">
                </div>
            <div class="flex justify-end mt-4 text-sm text-gray-500">
                <p>Tekan <kbd class="px-1 py-0.5 bg-gray-100 rounded text-xs">Esc</kbd> untuk keluar</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });

            // Mobile Dropdown Toggles
            const mobileProfileButton = document.getElementById('mobile-profile-button');
            const mobileProfileDropdown = document.getElementById('mobile-profile-dropdown');
            mobileProfileButton.addEventListener('click', () => {
                mobileProfileDropdown.classList.toggle('hidden');
            });

            const mobileKategoriButton = document.getElementById('mobile-kategori-button');
            const mobileKategoriDropdown = document.getElementById('mobile-kategori-dropdown');
            mobileKategoriButton.addEventListener('click', () => {
                mobileKategoriDropdown.classList.toggle('hidden');
            });
            
            // Global Search Modal
            const searchModal = document.getElementById('searchModal');
            const searchContainer = searchModal.querySelector('.search-container');
            const searchQueryInput = document.getElementById('searchQueryInput');
            const searchResults = document.getElementById('searchResults');
            
            window.openSearchModal = function() {
                searchModal.classList.remove('hidden');
                setTimeout(() => {
                    searchContainer.classList.remove('scale-95', 'opacity-0');
                    searchContainer.classList.add('scale-100', 'opacity-100');
                    searchQueryInput.focus();
                }, 10);
            };
            
            window.closeSearchModal = function() {
                searchContainer.classList.remove('scale-100', 'opacity-100');
                searchContainer.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    searchModal.classList.add('hidden');
                }, 300);
            };
            
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !searchModal.classList.contains('hidden')) {
                    closeSearchModal();
                }
            });
            
            // Basic search functionality (for demonstration)
            searchQueryInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                searchResults.innerHTML = '';
                if (query.length > 2) {
                    // Simulate search results
                    const dummyResults = [
                        { title: 'Tentang SMKN 4 KOTA BOGOR', link: '/profil' },
                        { title: 'Berita Terbaru Seputar Sekolah', link: '/berita' },
                        { title: 'Jadwal Kegiatan Ekstrakurikuler', link: '/ekstrakurikuler' },
                    ];
                    
                    const filteredResults = dummyResults.filter(result => result.title.toLowerCase().includes(query));
                    
                    if (filteredResults.length > 0) {
                        filteredResults.forEach(result => {
                            const resultItem = document.createElement('a');
                            resultItem.href = result.link;
                            resultItem.className = 'search-result block p-3 hover:bg-gray-100';
                            resultItem.innerHTML = `<p class="font-semibold text-sm">${result.title}</p>`;
                            searchResults.appendChild(resultItem);
                        });
                    } else {
                        searchResults.innerHTML = `<p class="text-center text-gray-500 search-empty">Tidak ada hasil ditemukan.</p>`;
                    }
                }
            });
        });
    </script>
@endsection