@extends('public.layouts.app')

@section('title', 'Beranda - SMKN 4 KOTA BOGOR')

@section('content')
<!-- Hero Slider -->
<div class="relative overflow-hidden" style="position: relative; z-index: 1;">
    <div class="hero-slider-container" id="heroSlider">
        <!-- Slider images will be populated by JavaScript -->
    </div>
    
    <!-- Slider Controls -->
    <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition duration-300" style="z-index: 100;" onclick="changeSlide(-1)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition duration-300" style="z-index: 100;" onclick="changeSlide(1)">
        <i class="fas fa-chevron-right"></i>
    </button>
    
    <!-- Slider Indicators -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2" style="z-index: 100;" id="sliderIndicators">
        <!-- Indicators will be populated by JavaScript -->
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Profile Hero Section -->
    <section class="mb-12">
        <div class="profile-hero grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <!-- Left: Text -->
            <div>
                <h2 class="profile-title">Selamat Datang Di SMKN 4 Kota Bogor</h2>
                <p class="profile-paragraph mt-4">
                   SMK Negeri 4 Kota Bogor merupakan salah satu sekolah menengah kejuruan yang berkomitmen untuk melahirkan lulusan yang kompeten, berkarakter, dan siap bersaing di dunia kerja.
                   Dengan dukungan fasilitas pembelajaran yang memadai, kolaborasi dengan industri, serta visi dan misi yang berfokus pada pembentukan profil pelajar Pancasila
                   , SMKN 4 Kota Bogor turut berperan aktif dalam memajukan pendidikan di Indonesia.
                </p>
                <div class="mt-6">
                    <a href="/profil" class="profile-btn inline-flex items-center">
                        Selengkapnya
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
            <!-- Right: Video -->
            <div class="profile-video-wrapper">
                <div class="relative rounded-xl overflow-hidden shadow-lg" style="max-width: 450px; margin: 0 auto;">
                    <div class="relative w-full" style="padding-top: 56.25%;">
                        <iframe
                            class="absolute inset-0 w-full h-full"
                            src="https://www.youtube.com/embed/N6cmqCbQllo"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            title="YouTube video player for SMKN 4 Kota Bogor">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Fasilitas Sekolah Section -->
    <section class="mb-12">
        <div class="facilities-grid flex flex-wrap items-center justify-center gap-8">
            <!-- Laboratorium -->
            <div class="facility-item facility-vertical">
                <div class="facility-icon facility-circle">
                    <i class="fas fa-desktop"></i>
                </div>
                <div class="facility-content">
                    <h3 class="facility-title">Laboratorium</h3>
                </div>
            </div>
            <!-- Bengkel -->
            <div class="facility-item facility-vertical">
                <div class="facility-icon facility-circle">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="facility-content">
                    <h3 class="facility-title">Bengkel</h3>
                </div>
            </div>
            <!-- Perpustakaan -->
            <div class="facility-item facility-vertical">
                <div class="facility-icon facility-circle">
                    <i class="fas fa-book"></i>
                </div>
                <div class="facility-content">
                    <h3 class="facility-title">Perpustakaan</h3>
                </div>
            </div>
            <!-- Lapangan -->
            <div class="facility-item facility-vertical">
                <div class="facility-icon facility-circle">
                    <i class="fas fa-futbol"></i>
                </div>
                <div class="facility-content">
                    <h3 class="facility-title">Lapangan</h3>
                </div>
            </div>
            <!-- Wifi -->
            <div class="facility-item facility-vertical">
                <div class="facility-icon facility-circle">
                    <i class="fas fa-wifi"></i>
                </div>
                <div class="facility-content">
                    <h3 class="facility-title">Wifi</h3>
                </div>
            </div>
        </div>
    </section>
    <!-- Berita Sekolah Section -->
    <section class="mb-12">
        <div class="mb-6 text-center">
            <h2 class="section-title">Berita Sekolah</h2>
        </div>
        
        <!-- News Grid (Redesigned) -->
        <div id="newsGridWrapper">
            <div id="newsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5"></div>
        </div>
    </section>

    <!-- Galeri Sekolah Section -->
    <section class="mb-12">
        <div class="mb-6 text-center">
            <h2 class="section-title">Galeri Sekolah</h2>
        </div>
        <div id="homeGalleryWrapper">
            <div id="galleryGrid" class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4"></div>
            <div class="mt-6 text-left mb-2">
                <a href="{{ route('gallery.index') }}" class="load-more-btn">Lihat Selengkapnya</a>
            </div>
        </div>
    </section>

    <!-- Pesan/Testimoni Section -->
    <section class="mb-12">
        <!-- Testimonial Slider Only -->
        <div class="testimonial-slider-container w-full">
            <div class="swiper testimonialSwiper">
                <div class="swiper-wrapper" id="testimonialWrapper"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        
        <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ===== Testimonial Section Redesign ===== */
        :root { --t-primary: #26658c; }
        .testimonial-slider-container{position:relative; z-index:1; padding:8px 0 24px; max-width:1200px; margin:0 auto; padding-left:16px; padding-right:16px}
        .testimonialSwiper.swiper{overflow:visible !important}
        .testimonialSwiper .swiper-wrapper{align-items:stretch; height:auto !important}
        .testimonialSwiper{height:auto !important}
        .testimonialSwiper .swiper-slide{height:auto}
        .t-card{position:relative; display:flex; flex-direction:column; height:auto; background:#ffffff; border-radius:16px; border:1px solid #e6eef4; box-shadow:0 10px 24px rgba(2,56,89,.10); padding:20px; transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease}
        .t-card:hover{transform:translateY(-4px); box-shadow:0 18px 40px rgba(2,56,89,.16); border-color:#d7e6f0}
        .t-header{display:flex; align-items:center; gap:12px; margin-bottom:10px}
        .t-avatar{flex:0 0 auto; width:44px; height:44px; border-radius:9999px; background:#eaf2f7; color:var(--t-primary); display:flex; align-items:center; justify-content:center; font-weight:800}
        .t-avatar i{font-size:18px}
        .t-name{font-weight:700; color:#023859; font-size:0.9375rem; line-height:1.2}
        .t-email{font-size:.75rem; color:#7a8a9a}
        .t-quote{color:#334155; font-size:.875rem; line-height:1.6; margin-top:6px}
        .t-quote i{color:var(--t-primary); opacity:.8; margin-right:.4rem}
        .t-footer{margin-top:auto; display:flex; align-items:center; justify-content:flex-start; gap:8px; color:#93a4b4; font-size:.75rem}
        .t-stars{color:#f6b100}
        .testimonialSwiper .swiper-pagination{position:static; margin-top:16px}
        .testimonialSwiper .swiper-pagination-bullet{background:#c2d3df; opacity:.9}
        .testimonialSwiper .swiper-pagination-bullet-active{background:var(--t-primary)}
        
        /* News Slider Styles */
        .news-slider-container {
            position: relative;
            padding: 0 20px;
        }
        
        .newsSwiper {
            padding: 20px 0;
        }
        
        .news-slide {
            height: auto;
            display: flex;
            flex-direction: column;
        }
        
        .news-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .news-image-container {
            position: relative;
            height: 180px;
            overflow: hidden;
        }
        
        .news-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .news-card:hover .news-image {
            transform: scale(1.05);
        }
        
        .news-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .news-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .news-excerpt {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 15px;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        
        .news-date {
            color: #9ca3af;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }
        
        .news-category {
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Swiper Navigation Customization */
        .news-swiper-button-next,
        .news-swiper-button-prev {
            color: #10b981;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .news-swiper-button-next:hover,
        .news-swiper-button-prev:hover {
            background: #10b981;
            color: white;
        }
        
        .news-swiper-pagination .swiper-pagination-bullet {
            background: #d1d5db;
            opacity: 0.5;
        }
        
        .news-swiper-pagination .swiper-pagination-bullet-active {
            background: #10b981;
            opacity: 1;
        }
        
        /* Soft dark overlay on each slide (no caption) */
        .slider-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(2, 56, 89, 0.25); /* ~25% dark blue overlay */
            pointer-events: none;
        }
        @media (max-width: 640px) {
            .slider-image::after { background: rgba(2, 56, 89, 0.30); }
        }

        /* ===== Hero Slider Typography (clean, no box/blur) ===== */
        .slider-overlay { background: transparent; }
        .slider-caption { max-width: 860px; margin: 0 16px; }
        .slider-title {
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0,0,0,.45);
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            line-height: 1.3;
            margin-bottom: .3rem;
        }
        .slider-subtitle {
            color: #f0f5f9;
            text-shadow: 0 1px 6px rgba(0,0,0,.35);
            font-size: clamp(.85rem, 1.5vw, 1rem);
            line-height: 1.5;
            font-weight: 500;
        }
        @media (max-width: 640px) {
            .slider-caption { margin: 0 12px; }
        }
        /* Caption fade animation */
        .caption-fade { animation: captionFade .6s ease; }
        @keyframes captionFade {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Section Title */
        .section-title { color:#023859; font-weight:700; font-size:1.5rem; margin-bottom: .25rem; }
        
        /* ===== Profile Hero Section Styles ===== */
        .profile-hero { background: transparent; padding: 0; }
        .profile-title {
            color: #023859;
            font-weight: 700;
            font-size: 1.5rem; /* 24px */
            line-height: 1.3;
        }
        .profile-paragraph {
            color: #334155;
            font-size: 0.9375rem; /* 15px */
            line-height: 1.7;
        }
        .profile-btn {
            border: 1.5px solid #023859;
            color: #023859;
            padding: .4rem .75rem; /* match .load-more-btn */
            border-radius: 9999px;
            font-weight: 600;
            font-size: .8125rem; /* match .load-more-btn */
            transition: all .2s ease;
        }
        .profile-btn:hover { background: #023859; color: #ffffff; }
        .profile-image-wrapper {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 16 / 9;
        }
        .profile-image { width: 100%; height: 100%; object-fit: cover; display: block; }
        
        /* ===== News Card (Grid) — Themed to #023859 ===== */
        .news-card-v2 {
            --primary: #023859;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(2, 56, 89, 0.06);
            padding: 12px;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .news-card-v2:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(2, 56, 89, 0.1);
            border-color: rgba(2, 56, 89, 0.15);
        }
        .news-card-v2 .nc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .news-card-v2 .nc-title {
            font-size: 1rem; /* standard size */
            font-weight: 600;
            color: var(--primary);
            line-height: 1.4;
        }
        .news-card-v2 .nc-arrow {
            display: inline-flex;
            width: 36px;
            height: 36px;
            border: 1px solid #cbd5e1;
            border-radius: 9999px;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            background: #f3f7fa;
            transition: all .2s ease;
        }
        .news-card-v2 .nc-arrow:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
        .news-card-v2 .nc-image {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .news-card-v2 .nc-image img { 
            width: 100%; height: 100%; object-fit: cover; display: block; 
            transition: transform .25s ease;
        }
        .news-card-v2:hover .nc-image img { transform: scale(1.03); }
        .news-card-v2 .nc-list { border-top: 1px solid #eef2f7; margin-top: 8px; }
        .news-card-v2 .nc-list li { border-bottom: 1px dashed #eef2f7; }
        .news-card-v2 .nc-list a {
            display: block;
            padding: 12px 2px;
            color: #334155;
            font-size: 0.9rem; /* smaller description */
            text-decoration: none;
            transition: color .15s ease;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .news-card-v2 .nc-list a:hover { color: var(--primary); }
        .news-card-v2 .news-date { color: #64748b; font-size: .85rem; display: inline-flex; align-items: center; }
        .news-card-v2 .news-date i { margin-right: .35rem; color: #94a3b8; }
        
        /* ===== Facilities Section (Theme-aligned) ===== */
        .facilities-grid { align-items: center; }
        .facility-item { display: flex; flex-direction: column; align-items: center; gap: 10px; background: transparent; border: none; padding: 6px; box-shadow: none; transition: transform .2s ease; }
        .facility-item:hover { transform: translateY(-4px); }
        .facility-icon.facility-circle { width: 70px; height: 70px; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; background: #eaf2f7; }
        .facility-icon.facility-circle i { font-size: 24px; color: #023859; }
        .facility-title { color: #023859; font-weight: 600; font-size: .875rem; text-align: center; }
        @media (max-width: 640px) {
            .facility-icon.facility-circle { width: 60px; height: 60px; }
            .facility-icon.facility-circle i { font-size: 20px; }
        }
        
        /* ===== Home Gallery (Grid) ===== */
        .home-gallery-card { transition: transform .2s ease; }
        .home-gallery-card:hover { transform: translateY(-4px); }
        .home-gallery-thumb { aspect-ratio: 4 / 3; border-radius: 8px; overflow: hidden; background:#e5e7eb; }
        .home-gallery-title { color:#023859; font-weight:600; font-size:0.9375rem; line-height:1.4; margin-top:.6rem; }
        .home-gallery-title a:hover { text-decoration: underline; }
        .home-gallery-year { color:#64748b; font-size:.9rem; margin-top:.15rem; }
        .home-gallery-year i { margin-right:.35rem; color:#94a3b8; }
        .load-more-btn{ display:inline-block; padding:.4rem .75rem; border:1.5px solid #023859; color:#023859; border-radius:9999px; font-weight:600; font-size:.8125rem; transition:all .2s ease; }
        .load-more-btn:hover{ background:#023859; color:#fff; }
        
        /* Global Search Modal Styles - Clean & Modern */
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
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
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
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-6" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
</div>

<!-- Global Search Modal -->
<div id="searchModal" class="fixed inset-0 search-modal z-50 hidden">
    <div class="flex items-start justify-center min-h-screen pt-16 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" onclick="closeSearchModal()">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="search-container inline-block align-bottom text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Search Header -->
            <div class="search-header px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Pencarian Global</h3>
                    <button onclick="closeSearchModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Search Input -->
            <div class="px-6 py-5">
                <div class="relative">
                    <input type="text" 
                           id="globalSearchInput" 
                           placeholder="Ketik kata kunci untuk mencari..." 
                           class="search-input w-full px-4 py-3 pl-12 text-gray-900 focus:outline-none"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <div id="searchClear" class="absolute inset-y-0 right-0 pr-4 flex items-center hidden">
                        <button onclick="clearSearch()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- Search Tips -->
                <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-500">
                    <span class="flex items-center">
                        <kbd class="px-2 py-1 bg-gray-100 rounded mr-2">⌘K</kbd>
                        untuk membuka pencarian
                    </span>
                    <span class="flex items-center">
                        <kbd class="px-2 py-1 bg-gray-100 rounded mr-2">ESC</kbd>
                        untuk menutup
                    </span>
                </div>
            </div>

            <!-- Search Results -->
            <div id="searchResults" class="px-6 pb-6 max-h-96 overflow-y-auto">
                <!-- Loading State -->
                <div id="searchLoading" class="hidden text-center py-8">
                    <div class="search-loading inline-block w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div>
                    <p class="mt-2 text-gray-500">Mencari...</p>
                </div>

                <!-- Empty State -->
                <div id="searchEmpty" class="hidden search-empty text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-6 bg-gray-50 rounded-full flex items-center justify-center">
                        <i class="fas fa-search text-gray-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Mulai Pencarian</h4>
                    <p class="text-gray-500 mb-8">Ketik kata kunci untuk mencari di seluruh website</p>
                    
                    <!-- Search Suggestions -->
                    <div class="text-left max-w-lg mx-auto">
                        <p class="text-sm text-gray-600 mb-4">Pencarian Populer:</p>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="searchSuggestion('paskibra')" class="search-suggestion-btn px-3 py-2 bg-gray-50 text-gray-700 text-sm hover:bg-gray-100 transition-colors duration-200">
                                Paskibra
                            </button>
                            <button onclick="searchSuggestion('pramuka')" class="search-suggestion-btn px-3 py-2 bg-gray-50 text-gray-700 text-sm hover:bg-gray-100 transition-colors duration-200">
                                Pramuka
                            </button>
                            <button onclick="searchSuggestion('berita')" class="search-suggestion-btn px-3 py-2 bg-gray-50 text-gray-700 text-sm hover:bg-gray-100 transition-colors duration-200">
                                Berita
                            </button>
                            <button onclick="searchSuggestion('galeri')" class="search-suggestion-btn px-3 py-2 bg-gray-50 text-gray-700 text-sm hover:bg-gray-100 transition-colors duration-200">
                                Galeri
                            </button>
                            <button onclick="searchSuggestion('futsal')" class="search-suggestion-btn px-3 py-2 bg-gray-50 text-gray-700 text-sm hover:bg-gray-100 transition-colors duration-200">
                                Futsal
                            </button>
                            <button onclick="searchSuggestion('silat')" class="search-suggestion-btn px-3 py-2 bg-gray-50 text-gray-700 text-sm hover:bg-gray-100 transition-colors duration-200">
                                Silat
                            </button>
                        </div>
                        
                        <!-- Last Search -->
                        <div id="lastSearchSection" class="mt-6 hidden">
                            <p class="text-sm text-gray-600 mb-3">Pencarian Terakhir:</p>
                            <div class="flex flex-wrap gap-2">
                                <button id="lastSearchBtn" onclick="searchSuggestion('')" class="search-suggestion-btn px-3 py-2 bg-gray-50 text-gray-700 text-sm hover:bg-gray-100 transition-colors duration-200">
                                    <i class="fas fa-history mr-2"></i>
                                    <span id="lastSearchText"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Results State -->
                <div id="searchNoResults" class="hidden search-empty text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-6 bg-gray-50 rounded-full flex items-center justify-center">
                        <i class="fas fa-search-minus text-gray-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Tidak Ditemukan</h4>
                    <p class="text-gray-500 mb-2">Tidak ada hasil untuk "<span id="searchQuery"></span>"</p>
                    <p class="text-sm text-gray-400">Coba gunakan kata kunci yang berbeda</p>
                </div>

                <!-- Results Container -->
                <div id="searchResultsList" class="space-y-3">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
// Get latest photos from database for slider
@if(isset($latestPhotos) && $latestPhotos->count() > 0)
    // Use latest photos data from controller
    const latestPhotosData = @json($latestPhotos);
    // Transform to slider format
    const sliderImages = latestPhotosData.map(photo => {
        const imageUrl = photo.file_path || photo.file || '/images/default-gallery.jpg';
        const title = photo.caption || photo.judul || 'Foto Galeri SMKN 4 KOTA BOGOR';
        const subtitle = (photo.gallery && photo.gallery.title) ? photo.gallery.title : 'Galeri Sekolah';
        
        return {
            url: imageUrl,
            title: title,
            subtitle: subtitle
        };
    });
@else
    // Fallback to default images if no photos in database
    const sliderImages = [
        {
            url: '/images/halaman1.JPG',
            title: 'Selamat Datang di SMKN 4 KOTA BOGOR',
            subtitle: 'Mencetak Generasi Cerdas dan Berkarakter'
        },
        {
            url: '/images/halaman2.JPG',
            title: 'Kegiatan Belajar Mengajar',
            subtitle: 'Suasana Pembelajaran yang Kondusif'
        },
        {
            url: '/images/halaman3.jpg',
            title: 'Ekstrakurikuler Unggulan',
            subtitle: 'Mengembangkan Bakat dan Minat Siswa'
        },
        {
            url: '/images/halaman4.JPG',
            title: 'Prestasi Akademik dan Non-Akademik',
            subtitle: 'Membanggakan Sekolah dan Bangsa'
        },
        {
            url: '/images/halaman5.JPG',
            title: 'Fasilitas Sekolah Terlengkap',
            subtitle: 'Mendukung Kegiatan Pembelajaran'
        }
    ];
@endif

let currentSlide = 0;
let slideInterval;

// Initialize slider
function initSlider() {
    const slider = document.getElementById('heroSlider');
    const indicators = document.getElementById('sliderIndicators');
    
    if (slider && sliderImages.length > 0) {
        // Create slider images
        slider.innerHTML = sliderImages.map((image, index) => `
            <div class="slider-image absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out ${index === 0 ? 'opacity-100' : 'opacity-0'}" 
                 style="background-image: url('${image.url}'); background-size: cover; background-position: center;">
                <a href="${image.url}" class="glightbox" data-gallery="slider-gallery" data-title="${image.title}" data-description="${image.subtitle}">
                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                        <div class="opacity-0 hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-expand text-white text-3xl"></i>
                        </div>
                    </div>
                </a>
            </div>
        `).join('');
        
        // Create indicators
        if (indicators) {
            indicators.innerHTML = sliderImages.map((_, index) => `
                <button class="w-3 h-3 rounded-full transition-all duration-300 ${index === 0 ? 'bg-white' : 'bg-white bg-opacity-50'}" 
                        onclick="goToSlide(${index})">
                </button>
            `).join('');
        }
        
        // Update overlay text
        updateSliderText();
    }
}

// Change slide
function changeSlide(direction) {
    const images = document.querySelectorAll('.slider-image');
    const indicators = document.querySelectorAll('#sliderIndicators button');
    
    // Hide current slide
    images[currentSlide].classList.remove('opacity-100');
    images[currentSlide].classList.add('opacity-0');
    indicators[currentSlide].classList.remove('bg-white');
    indicators[currentSlide].classList.add('bg-white', 'bg-opacity-50');
    
    // Calculate new slide index
    currentSlide += direction;
    if (currentSlide >= sliderImages.length) {
        currentSlide = 0;
    } else if (currentSlide < 0) {
        currentSlide = sliderImages.length - 1;
    }
    
    // Show new slide
    images[currentSlide].classList.remove('opacity-0');
    images[currentSlide].classList.add('opacity-100');
    indicators[currentSlide].classList.remove('bg-white', 'bg-opacity-50');
    indicators[currentSlide].classList.add('bg-white');
    
    // Update overlay text
    updateSliderText();
}

// Go to specific slide
function goToSlide(index) {
    const images = document.querySelectorAll('.slider-image');
    const indicators = document.querySelectorAll('#sliderIndicators button');
    
    // Hide current slide
    images[currentSlide].classList.remove('opacity-100');
    images[currentSlide].classList.add('opacity-0');
    indicators[currentSlide].classList.remove('bg-white');
    indicators[currentSlide].classList.add('bg-white', 'bg-opacity-50');
    
    // Update current slide
    currentSlide = index;
    
    // Show new slide
    images[currentSlide].classList.remove('opacity-0');
    images[currentSlide].classList.add('opacity-100');
    indicators[currentSlide].classList.remove('bg-white', 'bg-opacity-50');
    indicators[currentSlide].classList.add('bg-white');
    
    // Update overlay text
    updateSliderText();
    
    // Reset auto slide timer
    resetAutoSlide();
}

// Update slider text
function updateSliderText() {
    const title = document.getElementById('sliderTitle');
    const subtitle = document.getElementById('sliderSubtitle');
    const caption = document.querySelector('.slider-caption');
    
    if (title && subtitle && sliderImages[currentSlide]) {
        title.textContent = sliderImages[currentSlide].title;
        subtitle.textContent = sliderImages[currentSlide].subtitle;
        if (caption) {
            // retrigger fade animation
            caption.classList.remove('caption-fade');
            // force reflow
            void caption.offsetWidth;
            caption.classList.add('caption-fade');
        }
    }
}

// Start auto slide
function startAutoSlide() {
    slideInterval = setInterval(() => {
        changeSlide(1);
    }, 3000); // Change every 3 seconds
}

// Reset auto slide timer
function resetAutoSlide() {
    if (slideInterval) {
        clearInterval(slideInterval);
        startAutoSlide();
    }
}

// Pause auto slide on hover
function pauseAutoSlide() {
    if (slideInterval) {
        clearInterval(slideInterval);
    }
}

// Resume auto slide when mouse leaves
function resumeAutoSlide() {
    startAutoSlide();
}

// Load news from database via controller
function loadNews() {
    @if(isset($latestPosts) && $latestPosts->count() > 0)
        // Use data from controller
        const posts = @json($latestPosts);
        const beritaBaseUrl = "{{ url('/berita') }}";
        console.log('Posts data:', posts);
        renderNewsGrid(posts.map(post => {
            console.log('Post:', post);
            console.log('Post photos:', post.photos);
            
            // Try multiple ways to get the image
            let imageUrl = '/images/default-news.jpg';
            
            // Method 1: Check if photos array exists and has items
            if (post.photos && post.photos.length > 0) {
                const firstPhoto = post.photos[0];
                console.log('First photo:', firstPhoto);
                // Try multiple properties
                if (firstPhoto) {
                    imageUrl = firstPhoto.file_path || firstPhoto.file || firstPhoto.optimal_url || imageUrl;
                }
            }
            
            // Method 2: Check if image attribute exists
            if (post.image && post.image !== '/images/default-news.jpg') {
                imageUrl = post.image;
            }
            
            // Method 3: Check if post has file_path directly
            if (post.file_path) {
                imageUrl = post.file_path;
            }
            
            console.log('Final image URL:', imageUrl);
            
            // Detect external source URL: prioritize dedicated column 'url', fallback to first http/https in isi
            let externalUrl = null;
            try {
                if (post.url && String(post.url).trim() !== '') {
                    externalUrl = String(post.url).trim();
                } else if (post.isi) {
                    const isiText = String(post.isi);
                    const urlRegex = /(https?:\/\/[^\"]+|https?:\/\/[^'\s]+)/i;
                    const match = isiText.match(urlRegex);
                    if (match && match[0]) externalUrl = match[0];
                }
            } catch (e) {}

            return {
                title: post.judul,
                // content tidak dipakai di beranda sesuai permintaan
                content: '',
                date: new Date(post.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }),
                image: imageUrl,
                detailUrl: `${beritaBaseUrl}/${post.id}`,
                sourceUrl: externalUrl
            };
        }));
    @else
        // DB kosong: tampilkan placeholder agar tidak terlihat kosong
        showPlaceholder('newsGrid', 'Belum ada data berita');
    @endif
}

// Load sample news data
function loadSampleNews() {
    console.log('loadSampleNews called in home.blade.php');
    const sampleNews = [
        {
            title: 'Upacara Peringatan Hari Kemerdekaan Indonesia',
            content: 'Kegiatan upacara bendera dalam rangka memperingati Hari Kemerdekaan Republik Indonesia yang dilaksanakan di lapangan sekolah dengan penuh khidmat dan semangat nasionalisme.',
            date: '17 Agustus 2024',
            category: 'Kegiatan Sekolah',
            image: '/images/upacara17.JPG'
        },
        {
            title: 'Kegiatan TRANSFORKR4B',
            content: 'Program TRANSFOKR4B untuk meningkatkan kompetensi siswa dalam bidang teknologi informasi dan komunikasi di era modern.',
            date: '30 Oktober 2024',
            category: 'Teknologi',
            image: '/images/transforkrab.JPG'
        },
        {
            title: 'Masa Pengenalan Lingkungan Sekolah',
            content: 'MPLS tahun ajaran 2024/2025 berlangsung dengan lancar dan penuh semangat dari seluruh peserta didik baru untuk mengenal lingkungan sekolah.',
            date: '19 Juli 2024',
            category: 'Akademik',
            image: '/images/mpls.JPG'
        },
        {
            title: 'Peringatan Maulid Nabi Muhammad SAW',
            content: 'Kegiatan peringatan Maulid Nabi Muhammad SAW yang diisi dengan berbagai kegiatan keagamaan dan pembacaan sholawat.',
            date: '15 September 2024',
            category: 'Keagamaan',
            image: '/images/maulidnabi.JPG'
        },
        {
            title: 'Festival Adat (FEDAT)',
            content: 'Festival budaya yang menampilkan berbagai kesenian dan adat istiadat dari berbagai daerah di Indonesia untuk melestarikan budaya bangsa.',
            date: '17 Desember 2024',
            category: 'Budaya',
            image: '/images/fedat.JPG'
        },
        {
            title: 'Kunjungan Industri',
            content: 'Siswa kelas XII melakukan kunjungan industri untuk mengenal dunia kerja yang sesungguhnya dan mempersiapkan diri memasuki dunia profesional.',
            date: '25 September 2024',
            category: 'Industri',
            image: '/images/kunjungan.JPG'
        }
    ];
    
    console.log('Sample news data loaded in home.blade.php, calling initNewsSlider...');
    // Render news in redesigned grid
    renderNewsGrid(sampleNews);
}

// Render Berita (grid) ke dalam #newsGrid
function renderNewsGrid(newsData) {
    const grid = document.getElementById('newsGrid');
    if (!grid) return;

    if (!newsData || newsData.length === 0) {
        showPlaceholder('newsGrid', 'Belum ada data berita');
        return;
    }

    const items = newsData.map(item => {
        const hasSource = !!item.sourceUrl;
        const btnLabel = 'Buka Sumber';
        const btnHref = hasSource ? item.sourceUrl : (item.detailUrl || item.url || '#');
        const btnTarget = hasSource ? " target=\"_blank\" rel=\"noopener noreferrer\"" : '';
        return `
        <article class="news-card-v2">
            <div class="nc-header">
                <h3 class="nc-title">${item.title}</h3>
            </div>
            <div class="nc-image">
                <a href="${item.image}" class="glightbox" data-gallery="news-grid" data-title="${item.title}">
                    <img src="${item.image}" alt="${item.title}">
                </a>
            </div>
            <div class="mt-3 text-sm text-gray-500">
                <span class="news-date"><i class="fas fa-calendar-alt"></i>${item.date}</span>
            </div>
            <div class="mt-3">
                <a href="${btnHref}" class="load-more-btn"${btnTarget}>${btnLabel}</a>
            </div>
        </article>
    `;}).join('');

    grid.innerHTML = items;
    // Tidak perlu bind modal - tombol mengarah ke halaman detail
}

// Initialize news slider with Swiper
function initNewsSlider(newsData) {
    console.log('initNewsSlider called in home.blade.php with data:', newsData);
    const newsSlider = document.getElementById('newsSlider');
    if (newsSlider) {
        console.log('News slider element found in home.blade.php, creating slides...');
        // Create news slides
        const newsSlides = newsData.map(news => `
            <div class="swiper-slide news-slide">
                <div class="news-card">
                    <div class="news-image-container">
                    <a href="${news.image}" 
                       class="glightbox" 
                       data-gallery="news-gallery" 
                       data-title="${news.title}" 
                           data-description="${news.content}">
                        <img src="${news.image}" 
                             alt="${news.title}" 
                                 class="news-image">
                        </a>
                            </div>
                    <div class="news-content">
                        <h3 class="news-title">${news.title}</h3>
                        <p class="news-excerpt">${news.content}</p>
                        <div class="news-meta">
                            <span class="news-date">
                                <i class="fas fa-calendar mr-1"></i>
                                ${news.date}
                            </span>
                            <span class="news-category">${news.category}</span>
                        </div>
                </div>
                </div>
            </div>
        `).join('');
        
        newsSlider.innerHTML = newsSlides;
        
        // Initialize Swiper
        const newsSwiper = new Swiper('.newsSwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.news-swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.news-swiper-button-next',
                prevEl: '.news-swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                1280: {
                    slidesPerView: 4,
                    spaceBetween: 30,
                },
            },
            on: {
                init: function() {
                    // Initialize GLightbox for new slides
                    if (typeof GLightbox !== 'undefined') {
                        GLightbox({
                            selector: '.glightbox',
                            touchNavigation: true,
                            loop: true,
                        });
                    }
                },
            },
        });
    }
}

// Load gallery from database via controller
function loadGallery() {
    @if(isset($randomPhotos) && $randomPhotos->count() > 0)
        // Use random photos data from controller
        const randomPhotos = @json($randomPhotos);
        const galleryGrid = document.getElementById('galleryGrid');
        if (galleryGrid) {
            const perRow = 4;
            // Ambil maksimal 8 foto, lalu pangkas agar kelipatan perRow untuk tampilan rapi
            let selectedPhotos = randomPhotos.slice(0, 8);
            if (selectedPhotos.length > perRow) {
                const remainder = selectedPhotos.length % perRow;
                if (remainder !== 0) {
                    selectedPhotos = selectedPhotos.slice(0, selectedPhotos.length - remainder);
                }
            }

            const galleryItems = selectedPhotos.map(photo => {
                // Try multiple properties for image URL
                const imageUrl = photo.file_path || photo.file || photo.optimal_url || '/images/default-gallery.jpg';
                const link = photo.gallery ? `/gallery/${photo.gallery.id}` : '#';
                const title = photo.caption || photo.judul || 'Foto Galeri';

                return `
                    <div class="home-gallery-card">
                        <a href="${imageUrl}" class="glightbox block" data-gallery="home-gallery" data-title="${title}">
                            <div class="home-gallery-thumb">
                                <img src="${imageUrl}" alt="${title}" class="w-full h-full object-cover" 
                                     onerror="this.src='/images/placeholder.svg'" />
                            </div>
                        </a>
                    </div>
                `;
            }).join('');

            galleryGrid.innerHTML = galleryItems;
        }
    @else
        // DB kosong: tampilkan placeholder
        showPlaceholder('galleryGrid', 'Belum ada data galeri');
    @endif
}

// Load sample gallery data
function loadSampleGallery() {
    const sampleGallery = [
        { image: '/images/belanegara.JPG', title: 'Kegiatan OSIS', year: '2024' },
        { image: '/images/komprapramuka.JPG', title: 'Praktik Bengkel', year: '2024' },
        { image: '/images/mubes.JPG', title: 'Lomba Futsal', year: '2024' },
        { image: '/images/p5solatduha.JPG', title: 'Pentas Seni', year: '2023' },
        { image: '/images/praktekmapil.JPG', title: 'Praktik Mapel', year: '2023' },
        { image: '/images/upacararutin.JPG', title: 'Upacara Rutin', year: '2022' },
        { image: '/images/ekskulbasket.JPG', title: 'Ekskul Basket', year: '2022' },
        { image: '/images/upacara17.JPG', title: 'Upacara 17 Agustus', year: '2021' }
    ];
    
    const galleryGrid = document.getElementById('galleryGrid');
    if (galleryGrid) {
        const galleryItems = sampleGallery.map(item => `
            <div class="home-gallery-card">
                <div class="home-gallery-thumb">
                    <img src="${item.image}" alt="${item.title}" class="w-full h-full object-cover" />
                </div>
                <div>
                    <h3 class="home-gallery-title">${item.title}</h3>
                </div>
            </div>
        `).join('');
        
        galleryGrid.innerHTML = galleryItems;
    }
}

// Message slider variables
let messageSlider = {
    currentIndex: 0,
    messages: [],
    interval: null,
    isPaused: false
};

// Load messages from database via controller
function loadMessages() {
    @if(isset($messages) && $messages->count() > 0)
        // Use data from controller
        const messages = @json($messages);
        messageSlider.messages = messages;
        initMessageSlider();
        displayMessages(messages);
    @else
        // Fallback to sample data
        loadSampleMessages();
    @endif
}

// Initialize message slider
function initMessageSlider() {
    if (messageSlider.messages.length === 0) return;
    
    const slider = document.getElementById('messageSlider');
    const indicators = document.getElementById('messageIndicators');
    
    if (slider && indicators) {
        // Create message slides
        slider.innerHTML = messageSlider.messages.map((message, index) => `
            <div class="message-slide ${index === 0 ? 'active' : ''}" data-index="${index}">
                <div class="message-content">
                    <div class="message-text">"${message.message}"</div>
                    <div class="message-author">- ${message.name}</div>
                </div>
            </div>
        `).join('');
        
        // Create indicators
        indicators.innerHTML = messageSlider.messages.map((_, index) => `
            <button class="w-2 h-2 rounded-full transition-all duration-300 ${index === 0 ? 'bg-blue-600' : 'bg-gray-300'}" 
                    data-index="${index}" onclick="goToMessage(${index})">
            </button>
        `).join('');
        
        // Add event listeners
        document.getElementById('prevMessage').addEventListener('click', () => changeMessage(-1));
        document.getElementById('nextMessage').addEventListener('click', () => changeMessage(1));
        
        // Start auto slide
        startMessageAutoSlide();
        
        // Pause on hover
        slider.addEventListener('mouseenter', pauseMessageAutoSlide);
        slider.addEventListener('mouseleave', resumeMessageAutoSlide);
    }
}

// Change message slide
function changeMessage(direction) {
    if (messageSlider.messages.length === 0) return;
    
    const slides = document.querySelectorAll('.message-slide');
    const indicators = document.querySelectorAll('#messageIndicators button');
    
    // Hide current slide
    slides[messageSlider.currentIndex].classList.remove('active');
    indicators[messageSlider.currentIndex].classList.remove('bg-blue-600');
    indicators[messageSlider.currentIndex].classList.add('bg-gray-300');
    
    // Calculate new index
    messageSlider.currentIndex += direction;
    if (messageSlider.currentIndex >= messageSlider.messages.length) {
        messageSlider.currentIndex = 0;
    } else if (messageSlider.currentIndex < 0) {
        messageSlider.currentIndex = messageSlider.messages.length - 1;
    }
    
    // Show new slide
    slides[messageSlider.currentIndex].classList.add('active');
    indicators[messageSlider.currentIndex].classList.remove('bg-gray-300');
    indicators[messageSlider.currentIndex].classList.add('bg-blue-600');
}

// Go to specific message
function goToMessage(index) {
    if (messageSlider.messages.length === 0) return;
    
    const slides = document.querySelectorAll('.message-slide');
    const indicators = document.querySelectorAll('#messageIndicators button');
    
    // Hide current slide
    slides[messageSlider.currentIndex].classList.remove('active');
    indicators[messageSlider.currentIndex].classList.remove('bg-blue-600');
    indicators[messageSlider.currentIndex].classList.add('bg-gray-300');
    
    // Update current index
    messageSlider.currentIndex = index;
    
    // Show new slide
    slides[messageSlider.currentIndex].classList.add('active');
    indicators[messageSlider.currentIndex].classList.remove('bg-gray-300');
    indicators[messageSlider.currentIndex].classList.add('bg-blue-600');
    
    // Reset auto slide timer
    resetMessageAutoSlide();
}

// Start auto slide for messages
function startMessageAutoSlide() {
    if (messageSlider.messages.length <= 1) return;
    
    messageSlider.interval = setInterval(() => {
        if (!messageSlider.isPaused) {
            changeMessage(1);
        }
    }, 3000); // Change every 3 seconds
}

// Reset auto slide timer
function resetMessageAutoSlide() {
    if (messageSlider.interval) {
        clearInterval(messageSlider.interval);
        startMessageAutoSlide();
    }
}

// Pause auto slide
function pauseMessageAutoSlide() {
    messageSlider.isPaused = true;
}

// Resume auto slide
function resumeMessageAutoSlide() {
    messageSlider.isPaused = false;
}

// Display messages as slider
function displayMessages(messages) {
    const wrapper = document.getElementById('testimonialWrapper');
    if (wrapper && messages && messages.length > 0) {
        // helper render stars from rating (0-5, supports .5)
        const renderStars = (rating) => {
            const r = Math.max(0, Math.min(5, Number(rating || 0)));
            const full = Math.floor(r);
            const half = r - full >= 0.5 ? 1 : 0;
            const empty = 5 - full - half;
            return `${'<i class=\"fas fa-star\"></i>'.repeat(full)}${half ? '<i class=\"fas fa-star-half-alt\"></i>' : ''}${'<i class=\"far fa-star\"></i>'.repeat(empty)}`;
        };
        const slides = messages.map(m => `
            <div class="swiper-slide">
                <article class="t-card">
                    <header class="t-header">
                        <div class="t-avatar"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="t-name">${m.name || 'Pengguna'}</div>
                        </div>
                    </header>
                    <div class="t-quote line-clamp-3"><i class="fas fa-quote-left"></i>${m.message || ''}</div>
                    <footer class="t-footer">
                        <span class="t-stars">${renderStars(m.rating)}</span>
                        <span>Testimoni</span>
                    </footer>
                </article>
            </div>
        `).join('');
        wrapper.innerHTML = slides;

        // Init Swiper for testimonials (responsive cards)
        new Swiper('.testimonialSwiper', {
            loop: true,
            autoplay: { delay: 3500, disableOnInteraction: false },
            pagination: { el: '.testimonialSwiper .swiper-pagination', clickable: true },
            slidesPerView: 1,
            spaceBetween: 16,
            autoHeight: false,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 16 },
                1024: { slidesPerView: 3, spaceBetween: 20 }
            },
            grabCursor: true,
        });
    } else if (wrapper) {
        wrapper.innerHTML = `<div class=\"text-gray-400 p-4\">Belum ada testimoni</div>`;
    }
}

// Load sample messages
function loadSampleMessages() {
    const sampleMessages = [
        { name: 'Ahmad', email: 'ahmad@email.com', message: 'Sangat senang dengan program ekstrakurikuler yang ditawarkan sekolah ini.', rating: 4.5 },
        { name: 'Siti', email: 'siti@email.com', message: 'Fasilitas sekolah sangat memadai dan guru-gurunya sangat ramah.', rating: 5 },
        { name: 'Budi', email: 'budi@email.com', message: 'Anak saya sangat senang belajar di SMKN 4 KOTA BOGOR.', rating: 4 },
        { name: 'Rina', email: 'rina@email.com', message: 'Program pembelajaran yang sangat menarik dan mudah dipahami.', rating: 4.5 },
        { name: 'Dedi', email: 'dedi@email.com', message: 'Sekolah ini memberikan pendidikan yang berkualitas tinggi.', rating: 5 }
    ];
    
    // Set sample messages to slider
    messageSlider.messages = sampleMessages;
    initMessageSlider();
    
    // Render to testimonial stack
    displayMessages(sampleMessages);
}

// Show placeholder when no data is available
function showPlaceholder(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 text-lg mb-2">
                    <i class="fas fa-image"></i>
                </div>
                <p class="text-gray-500">${message}</p>
            </div>
        `;
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initSlider();
    loadNews();
    loadGallery();
    loadMessages();
    
    // Start auto slide
    startAutoSlide();
    
    // Add hover events to pause/resume auto slide
    const slider = document.getElementById('heroSlider');
    if (slider) {
        slider.addEventListener('mouseenter', pauseAutoSlide);
        slider.addEventListener('mouseleave', resumeAutoSlide);
    }
    
    // Initialize GLightbox
    initLightbox();
    
    // Initialize Global Search
    initGlobalSearch();
});

// Initialize GLightbox
function initLightbox() {
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true,
        onOpen: () => {
            // Pause auto slide when lightbox is open
            pauseAutoSlide();
        },
        onClose: () => {
            // Resume auto slide when lightbox is closed
            resumeAutoSlide();
        }
    });
}

// Global Search Functionality
let searchTimeout;
const searchData = {
    ekstrakurikuler: [
        { title: 'Paskibra', description: 'Pasukan Pengibar Bendera - Mengembangkan jiwa kepemimpinan dan nasionalisme', url: '/ekstrakurikuler', icon: 'fas fa-flag', category: 'Ekstrakurikuler' },
        { title: 'Pramuka', description: 'Gerakan Pramuka - Membangun karakter dan kepribadian yang baik', url: '/ekstrakurikuler', icon: 'fas fa-campfire', category: 'Ekstrakurikuler' },
        { title: 'Silat', description: 'Pencak Silat - Seni bela diri tradisional Indonesia', url: '/ekstrakurikuler', icon: 'fas fa-fist-raised', category: 'Ekstrakurikuler' },
        { title: 'Futsal', description: 'Futsal - Olahraga sepak bola dalam ruangan', url: '/ekstrakurikuler', icon: 'fas fa-futbol', category: 'Ekstrakurikuler' },
        { title: 'Basket', description: 'Basketball - Olahraga bola basket', url: '/ekstrakurikuler', icon: 'fas fa-basketball-ball', category: 'Ekstrakurikuler' },
        { title: 'Tari', description: 'Tari Tradisional - Seni tari tradisional Indonesia', url: '/ekstrakurikuler', icon: 'fas fa-music', category: 'Ekstrakurikuler' },
        { title: 'PMR', description: 'Palang Merah Remaja - Organisasi kemanusiaan', url: '/ekstrakurikuler', icon: 'fas fa-heart', category: 'Ekstrakurikuler' },
        { title: 'Paduan Suara', description: 'Paduan Suara - Seni vokal dan musik', url: '/ekstrakurikuler', icon: 'fas fa-microphone', category: 'Ekstrakurikuler' },
        { title: 'Rohis', description: 'Rohani Islam - Organisasi keagamaan Islam', url: '/ekstrakurikuler', icon: 'fas fa-mosque', category: 'Ekstrakurikuler' }
    ],
    berita: [
        { title: 'Upacara Peringatan Hari Kemerdekaan', description: 'Kegiatan upacara bendera dalam rangka memperingati Hari Kemerdekaan RI', url: '/berita', icon: 'fas fa-flag', category: 'Berita' },
        { title: 'Kegiatan TRANSFORMAKSI', description: 'Program transformasi digital untuk meningkatkan kompetensi siswa', url: '/berita', icon: 'fas fa-laptop', category: 'Berita' },
        { title: 'Masa Pengenalan Lingkungan Sekolah', description: 'MPLS tahun ajaran 2024/2025 berlangsung dengan lancar', url: '/berita', icon: 'fas fa-graduation-cap', category: 'Berita' },
        { title: 'Peringatan Maulid Nabi Muhammad SAW', description: 'Kegiatan peringatan Maulid Nabi dengan berbagai kegiatan keagamaan', url: '/berita', icon: 'fas fa-mosque', category: 'Berita' },
        { title: 'Festival Adat (FESDA)', description: 'Festival budaya menampilkan kesenian dan adat istiadat Indonesia', url: '/berita', icon: 'fas fa-theater-masks', category: 'Berita' },
        { title: 'Kunjungan Industri', description: 'Siswa melakukan kunjungan industri untuk mengenal dunia kerja', url: '/berita', icon: 'fas fa-industry', category: 'Berita' }
    ],
    galeri: [
        { title: 'Bela Negara', description: 'Kegiatan bela negara untuk membangun semangat patriotisme', url: '/gallery', icon: 'fas fa-shield-alt', category: 'Galeri' },
        { title: 'Kompetisi Pramuka', description: 'Kompetisi kepramukaan tingkat kabupaten', url: '/gallery', icon: 'fas fa-trophy', category: 'Galeri' },
        { title: 'Musyawarah Besar', description: 'Kegiatan musyawarah besar organisasi siswa', url: '/gallery', icon: 'fas fa-users', category: 'Galeri' },
        { title: 'P5 Solat Duha', description: 'Program Penguatan Profil Pelajar Pancasila', url: '/gallery', icon: 'fas fa-pray', category: 'Galeri' },
        { title: 'Praktik Mapel', description: 'Kegiatan praktik mata pelajaran', url: '/gallery', icon: 'fas fa-flask', category: 'Galeri' },
        { title: 'Upacara Rutin', description: 'Kegiatan upacara bendera rutin', url: '/gallery', icon: 'fas fa-flag', category: 'Galeri' }
    ],
    profil: [
        { title: 'Visi Misi Sekolah', description: 'Visi dan misi SMKN 4 KOTA BOGOR', url: '/profil', icon: 'fas fa-eye', category: 'Profil' },
        { title: 'Sejarah Sekolah', description: 'Sejarah berdirinya SMKN 4 KOTA BOGOR', url: '/profil', icon: 'fas fa-history', category: 'Profil' },
        { title: 'Struktur Organisasi', description: 'Struktur organisasi dan kepemimpinan sekolah', url: '/profil', icon: 'fas fa-sitemap', category: 'Profil' },
        { title: 'Fasilitas Sekolah', description: 'Fasilitas dan sarana prasarana yang tersedia', url: '/profil', icon: 'fas fa-building', category: 'Profil' },
        { title: 'Prestasi Sekolah', description: 'Prestasi dan pencapaian yang diraih sekolah', url: '/profil', icon: 'fas fa-trophy', category: 'Profil' }
    ]
};

function initGlobalSearch() {
    const searchInput = document.getElementById('globalSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
        searchInput.addEventListener('keydown', handleKeyDown);
    }
}

function openSearchModal() {
    const modal = document.getElementById('searchModal');
    const searchInput = document.getElementById('globalSearchInput');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Focus on search input after modal animation
    setTimeout(() => {
        searchInput.focus();
    }, 100);
    
    // Show empty state initially
    showEmptyState();
    
    // Load last search if exists
    const lastSearch = localStorage.getItem('lastSearch');
    if (lastSearch) {
        searchInput.value = lastSearch;
        searchInput.focus();
        // Trigger search
        const event = new Event('input', { bubbles: true });
        searchInput.dispatchEvent(event);
    }
}

function closeSearchModal() {
    const modal = document.getElementById('searchModal');
    const searchInput = document.getElementById('globalSearchInput');
    
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Clear search input
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Clear timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
}

function clearSearch() {
    const searchInput = document.getElementById('globalSearchInput');
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        showEmptyState();
        hideClearButton();
    }
}

function handleKeyDown(e) {
    if (e.key === 'Escape') {
        closeSearchModal();
    }
}

function handleSearch(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        showEmptyState();
        hideClearButton();
        return;
    }
    
    showClearButton();
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Show loading state
    showLoadingState();
    
    // Debounce search
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
}

function performSearch(query) {
    const results = searchInData(query);
    
    // Save search query to localStorage
    if (query.trim()) {
        localStorage.setItem('lastSearch', query.trim());
    }
    
    if (results.length === 0) {
        showNoResultsState(query);
    } else {
        showResults(results);
    }
}

function searchInData(query) {
    const allData = [
        ...searchData.ekstrakurikuler,
        ...searchData.berita,
        ...searchData.galeri,
        ...searchData.profil
    ];
    
    const lowerQuery = query.toLowerCase();
    
    // Filter and score results
    const results = allData.map(item => {
        let score = 0;
        const title = item.title.toLowerCase();
        const description = item.description.toLowerCase();
        const category = item.category.toLowerCase();
        
        // Exact match in title gets highest score
        if (title === lowerQuery) score += 100;
        else if (title.startsWith(lowerQuery)) score += 50;
        else if (title.includes(lowerQuery)) score += 30;
        
        // Exact match in description gets medium score
        if (description.includes(lowerQuery)) score += 20;
        
        // Category match gets low score
        if (category.includes(lowerQuery)) score += 10;
        
        return { ...item, score };
    }).filter(item => item.score > 0);
    
    // Sort by score (highest first)
    return results.sort((a, b) => b.score - a.score);
}

function showEmptyState() {
    hideAllStates();
    document.getElementById('searchEmpty').classList.remove('hidden');
    
    // Clear any existing popular searches
    const existingPopular = document.querySelector('.popular-searches');
    if (existingPopular) {
        existingPopular.remove();
    }
    
    // Show last search if exists
    const lastSearch = localStorage.getItem('lastSearch');
    if (lastSearch) {
        const lastSearchSection = document.getElementById('lastSearchSection');
        const lastSearchText = document.getElementById('lastSearchText');
        const lastSearchBtn = document.getElementById('lastSearchBtn');
        
        lastSearchText.textContent = lastSearch;
        lastSearchBtn.onclick = () => searchSuggestion(lastSearch);
        lastSearchSection.classList.remove('hidden');
    }
    
    // Show popular searches
    showPopularSearches();
}

function showPopularSearches() {
    const popularSearches = [
        { term: 'paskibra', count: 45 },
        { term: 'pramuka', count: 38 },
        { term: 'berita', count: 32 },
        { term: 'galeri', count: 28 },
        { term: 'futsal', count: 25 },
        { term: 'silat', count: 22 }
    ];
    
    const popularSection = document.createElement('div');
    popularSection.className = 'popular-searches mt-6 text-left max-w-lg mx-auto';
    popularSection.innerHTML = `
        <p class="text-sm text-gray-600 mb-4">Pencarian Populer:</p>
        <div class="space-y-2">
            ${popularSearches.map(search => `
                <button onclick="searchSuggestion('${search.term}')" class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <span class="text-sm text-gray-700 font-medium">${search.term}</span>
                    <span class="text-xs text-gray-500">${search.count} pencarian</span>
                </button>
            `).join('')}
        </div>
    `;
    
    const emptyState = document.getElementById('searchEmpty');
    emptyState.appendChild(popularSection);
}

function showLoadingState() {
    hideAllStates();
    document.getElementById('searchLoading').classList.remove('hidden');
}

function showNoResultsState(query) {
    hideAllStates();
    document.getElementById('searchQuery').textContent = query;
    document.getElementById('searchNoResults').classList.remove('hidden');
}

function showResults(results) {
    hideAllStates();
    
    const resultsContainer = document.getElementById('searchResultsList');
    resultsContainer.innerHTML = '';
    
    // Add results counter - clean design
    const searchInput = document.getElementById('globalSearchInput');
    const query = searchInput ? searchInput.value.trim() : '';
    const counter = document.createElement('div');
    counter.className = 'search-counter mb-6 p-4';
    counter.innerHTML = `
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-700">
                <i class="fas fa-search mr-2"></i>
                Ditemukan <strong>${results.length}</strong> hasil untuk "<strong>${query}</strong>"
            </span>
            <span class="text-xs text-gray-500">
                ${results.length} dari ${getTotalDataCount()} konten
            </span>
        </div>
    `;
    resultsContainer.appendChild(counter);
    
    // Show top results first
    const topResults = results.slice(0, 3);
    if (topResults.length > 0) {
        const topSection = document.createElement('div');
        topSection.className = 'search-top-results mb-6 p-4';
        topSection.innerHTML = `
            <div class="search-category-header flex items-center text-sm mb-4">
                <i class="fas fa-star mr-2"></i>
                Hasil Teratas (${topResults.length})
            </div>
        `;
        resultsContainer.appendChild(topSection);
        
        topResults.forEach((result, index) => {
            const resultElement = createResultElement(result, index);
            resultsContainer.appendChild(resultElement);
        });
    }
    
    // Group remaining results by category
    const remainingResults = results.slice(3);
    const groupedResults = groupResultsByCategory(remainingResults);
    
    // Show remaining results by category
    Object.keys(groupedResults).forEach(category => {
        const categoryResults = groupedResults[category];
        const categoryHeader = document.createElement('div');
        categoryHeader.className = 'mt-6 mb-4';
        categoryHeader.innerHTML = `
            <div class="search-category-header flex items-center text-sm mb-3">
                <i class="fas fa-folder mr-2"></i>
                ${category} (${categoryResults.length})
            </div>
        `;
        resultsContainer.appendChild(categoryHeader);
        
        categoryResults.forEach((result, index) => {
            const resultElement = createResultElement(result, index + 3);
            resultsContainer.appendChild(resultElement);
        });
    });
}

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

<!-- News Detail Modal -->
<div id="newsModal" class="fixed inset-0 z-50 hidden" style="display: none;">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeNewsModal()"></div>
  <div class="relative z-10 max-w-4xl mx-auto my-16 md:my-20 px-4">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="relative h-64 md:h-96 bg-gray-100">
        <img id="newsModalImage" src="" alt="Detail Berita" class="w-full h-full object-cover" />
        <!-- Gradient overlay with title/date on image -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
        <div class="absolute left-4 right-4 bottom-4">
          <div class="flex items-center gap-2 text-white/90 text-xs md:text-sm mb-1">
            <i class="fas fa-calendar text-white/90"></i>
            <span id="newsModalDate" class="drop-shadow"></span>
          </div>
          <h3 id="newsModalTitle" class="text-white text-xl md:text-2xl font-extrabold drop-shadow"></h3>
        </div>
        <button type="button" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center" onclick="closeNewsModal()" aria-label="Tutup">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-5 md:p-6">
        <p id="newsModalContent" class="text-sm md:text-base text-gray-700 leading-relaxed"></p>
      </div>
    </div>
  </div>
  <script>
    function openNewsModal(data){
      try {
        const m = document.getElementById('newsModal');
        if(!m) return;
        document.getElementById('newsModalTitle').textContent = data.title || 'Berita';
        document.getElementById('newsModalDate').textContent = data.date || '';
        document.getElementById('newsModalContent').textContent = data.content || '';
        const img = document.getElementById('newsModalImage');
        img.src = data.image || '/images/default-berita.jpg';
        img.alt = data.title || 'Berita';
        m.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      } catch(e){ console.error('openNewsModal failed', e); }
    }
    function closeNewsModal(){
      const m = document.getElementById('newsModal');
      if(!m) return;
      m.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
    document.addEventListener('keydown', function(evt){ if(evt.key==='Escape') closeNewsModal();});
  </script>
</div>
