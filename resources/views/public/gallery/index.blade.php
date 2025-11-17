@extends('public.layouts.app')

@section('title', 'Galeri Sekolah - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="gallery-title">Galeri Sekolah</h1>
        <p class="gallery-subtitle">Dokumentasi kegiatan dan pencapaian siswa SMKN 4 Kota Bogor</p>
    </div>

    <!-- Category Filter Tabs -->
    <div class="category-filter-container">
        <div class="category-filter">
            <button class="filter-btn active" data-category="all">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Semua Galeri</span>
                <span class="filter-count" id="count-all">0</span>
            </button>
            <button class="filter-btn" data-category="Kegiatan Sekolah">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span>Kegiatan Sekolah</span>
                <span class="filter-count" id="count-Kegiatan-Sekolah">0</span>
            </button>
            <button class="filter-btn" data-category="Ekstrakulikuler">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <span>Ekstrakurikuler</span>
                <span class="filter-count" id="count-Ekstrakulikuler">0</span>
            </button>
        </div>
    </div>
    @if(isset($albums) && $albums->count())
        <div class="album-grid">
            @foreach($albums as $index => $gallery)
            @php
                // Atur delay animasi yang lebih baik untuk grid
                $delay = 0.1 + ($index * 0.1);
                // Tambahkan kelas 'featured-card' untuk album pertama
                $featuredClass = $index === 0 ? 'featured-card' : '';
            @endphp
            <div class="gallery-card {{ $featuredClass }}" data-category="{{ $gallery->category ?? 'general' }}" style="animation-delay: {{ $delay }}s;">
                <a href="{{ route('gallery.show', $gallery) }}" class="block gallery-thumb">
                    @php
                        $file = ($gallery->photos && $gallery->photos->count() > 0) ? ($gallery->photos->first()->file ?? null) : null;
                        $src = $file ? \App\Helpers\ImageUrlHelper::getSafeImageUrl($file) : asset('images/default-gallery.jpg');
                        $photoCount = $gallery->photos ? $gallery->photos->count() : 0;
                    @endphp
                    <img src="{{ $src }}" alt="{{ $gallery->post?->judul ?? $gallery->title }}" class="gallery-img">
                    @php
                        $categoryMap = [
                            'Ekstrakulikuler' => ['icon' => '🏆', 'label' => 'Ekstrakurikuler'],
                            'Berita Sekolah' => ['icon' => '📰', 'label' => 'Berita & Kegiatan'],
                            'Kegiatan Sekolah' => ['icon' => '📅', 'label' => 'Kegiatan Sekolah'],
                            'Prestasi' => ['icon' => '🎓', 'label' => 'Prestasi'],
                            'general' => ['icon' => '📸', 'label' => 'Galeri']
                        ];
                        $category = $gallery->category ?? 'general';
                        $categoryInfo = $categoryMap[$category] ?? $categoryMap['general'];
                    @endphp
                    <div class="category-badge category-{{ Str::slug($category) }}">
                        {{ $categoryInfo['icon'] }} {{ $categoryInfo['label'] }}
                    </div>
                    <div class="photo-count-badge">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $photoCount }}</span>
                    </div>
                </a>
                <div class="card-info">
                    <h3 class="gallery-title-below"><a href="{{ route('gallery.show', $gallery) }}">{{ $gallery->post?->judul ?? $gallery->title }}</a></h3>
                    <p class="gallery-meta">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        {{ $gallery->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    @if(isset($albums) && $albums->isEmpty())
        <!-- No Galleries -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <i class="fas fa-images text-gray-400 text-6xl mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Belum ada galeri</h3>
                <p class="text-gray-600 mb-8">Galeri foto akan ditampilkan di sini setelah ditambahkan melalui admin panel.</p>
                
                <!-- Sample Gallery (Fallback) -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Galeri Contoh</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-camera text-blue-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Kegiatan Sekolah</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-graduation-cap text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Ekstrakurikuler</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-trophy text-yellow-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Prestasi</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-users text-purple-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Kegiatan Siswa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
/* Category Filter Container */
.category-filter-container {
    display: flex;
    justify-content: center;
    animation: fadeInUp 0.6s ease-out 0.3s both;
    margin-bottom: 3rem;
    clear: both;
}

.category-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
    padding: 1rem 1.5rem;
    background: rgba(249, 250, 251, 0.8);
    border-radius: 20px;
    border: 2px solid rgba(229, 231, 235, 0.5);
    backdrop-filter: blur(10px);
    max-width: 100%;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.filter-btn svg {
    transition: transform 0.3s ease;
}

.filter-btn:hover {
    border-color: #9ca3af;
    color: #374151;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.filter-btn:hover svg {
    transform: scale(1.1);
}

.filter-btn.active {
    background: #374151;
    border-color: #374151;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.filter-btn.active svg {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.filter-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 6px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
}

.filter-btn.active .filter-count {
    background: rgba(255, 255, 255, 0.25);
}

/* Category Badge on Cards */
.category-badge {
    position: absolute;
    bottom: 12px;
    left: 12px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    z-index: 2;
    backdrop-filter: blur(8px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.category-acara-sekolah {
    background: linear-gradient(135deg, rgba(84, 172, 191, 0.95), rgba(38, 101, 140, 0.95));
    color: white;
}

.category-ekstrakurikuler {
    background: linear-gradient(135deg, rgba(38, 101, 140, 0.95), rgba(2, 56, 89, 0.95));
    color: white;
}

.category-berita {
    background: linear-gradient(135deg, rgba(167, 235, 242, 0.95), rgba(84, 172, 191, 0.95));
    color: #023859;
}

.category-general {
    background: linear-gradient(135deg, rgba(84, 172, 191, 0.95), rgba(38, 101, 140, 0.95));
    color: white;
}

.gallery-thumb:hover .category-badge {
    transform: scale(1.05);
}

/* Hide/Show Cards on Filter */
.gallery-card.hidden {
    display: none;
}

/* Page headings with gradient */
.gallery-title { 
    background: linear-gradient(135deg, #023859 0%, #26658C 50%, #54ACBF 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: #023859; /* Fallback for browsers that don't support background-clip */
    font-weight:700; 
    font-size:1.75rem; 
    margin-bottom: 0.5rem;
    animation: fadeInDown 0.6s ease-out;
    position: relative;
    display: inline-block;
}

/* Add subtle shadow for depth */
.gallery-title::before {
    content: attr(data-text);
    position: absolute;
    left: 2px;
    top: 2px;
    z-index: -1;
    background: linear-gradient(135deg, rgba(2, 56, 89, 0.1), rgba(84, 172, 191, 0.1));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.gallery-subtitle { 
    color:#64748b; 
    font-size:0.9375rem; 
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

/* Masonry Grid Layout with Featured Card */
.album-grid { 
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (min-width:768px){ 
    .album-grid{ 
        grid-template-columns: repeat(4, 1fr); 
        gap: 1.25rem;
    }
    
    /* Featured card takes 2x2 space on tablet+ */
    .album-grid .featured-card {
        grid-column: span 2;
        grid-row: span 2;
    }
}

@media (min-width:1024px){ 
    .album-grid{ 
        grid-template-columns: repeat(4, 1fr); 
        gap: 1.5rem;
    }
}

/* Enhanced Card with White Background */
.gallery-card { 
    display:flex; 
    flex-direction:column; 
    gap:.5rem; 
    background:#fff; 
    border-radius:12px; 
    box-shadow:0 4px 12px rgba(0,0,0,.08); 
    padding:0.6rem; 
    border:1px solid #e5e7eb;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    /* Staggered entrance animation */
    opacity: 0;
    animation: cardFadeInUp 0.6s ease-out forwards;
}

@keyframes cardFadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Featured Card - Larger and More Prominent */
.gallery-card.featured-card {
    border-width: 3px;
    border-color: #d1d5db;
    background: #fff;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
}

.gallery-card.featured-card::before {
    background: rgba(0, 0, 0, 0.02);
}

.gallery-card.featured-card:hover {
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15), 0 10px 25px rgba(0, 0, 0, 0.08);
    transform: translateY(-15px) scale(1.02);
}

.gallery-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.02);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
    z-index: 0;
}

.gallery-card:hover::before {
    opacity: 1;
}

.gallery-card:hover { 
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
}

/* Thumbnail with Gradient Overlay */
.gallery-thumb { 
    position:relative; 
    aspect-ratio:1/1; 
    border-radius:10px; 
    overflow:hidden; 
    background: linear-gradient(135deg, #e5e7eb 0%, #f3f4f6 100%);
    z-index: 1;
}

/* Photo Count Badge */
.photo-count-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, rgba(2, 56, 89, 0.95), rgba(38, 101, 140, 0.95));
    backdrop-filter: blur(8px);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 4px 12px rgba(2, 56, 89, 0.3);
    z-index: 2;
    transition: all 0.3s ease;
}

.photo-count-badge svg {
    opacity: 0.9;
}

.gallery-thumb:hover .photo-count-badge {
    transform: scale(1.1);
    background: rgba(0, 0, 0, 0.8);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

/* Featured Star Badge */
.featured-star-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #f3f4f6;
    color: #374151;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 2;
    animation: pulseGlow 2s ease-in-out infinite;
}

@keyframes pulseGlow {
    0%, 100% { 
        transform: scale(1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    50% { 
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
}

.featured-star-badge svg {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.gallery-thumb::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 0%, transparent 50%, rgba(0,0,0,0.4) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.gallery-thumb:hover::after {
    opacity: 1;
}

.gallery-img { 
    width:100%; 
    height:100%; 
    object-fit:cover; 
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); 
    display:block;
}

.gallery-thumb:hover .gallery-img { 
    transform: scale(1.15) rotate(2deg);
}

/* Card Info Container */
.card-info {
    z-index: 1;
    position: relative;
}

/* Enhanced Title */
.gallery-title-below { 
    margin: 0 0 3px 0; 
    font-size:0.875rem; 
    font-weight:600; 
    color:#023859;
    transition: color 0.3s ease;
    line-height: 1.3;
}

/* Featured Card Title - Larger */
.gallery-title-below.featured-title {
    font-size: 1rem;
    font-weight: 700;
}

/* Gallery Meta Info */
.gallery-meta {
    margin: 0;
    font-size: 0.75rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 500;
}

.gallery-title-below a{ 
    color:inherit; 
    text-decoration:none;
    position: relative;
}

.gallery-title-below a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #54ACBF, #26658C);
    transition: width 0.3s ease;
}

.gallery-title-below a:hover::after {
    width: 100%;
}

.gallery-card:hover .gallery-title-below {
    color: #26658C;
}

.gallery-card:hover .gallery-meta {
    color: #54ACBF;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInDown {
    from { 
        opacity: 0; 
        transform: translateY(-20px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(20px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

/* Button (if needed later) */
.load-more-btn{ 
    display:inline-block; 
    padding:.4rem .75rem; 
    background: linear-gradient(135deg, #26658C 0%, #023859 100%);
    color: white;
    border: none;
    border-radius:8px; 
    font-weight:600; 
    font-size: 0.8125rem;
    transition:all .3s ease;
    box-shadow: 0 2px 8px rgba(38, 101, 140, 0.25);
}

.load-more-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(2, 56, 89, 0.3);
}

/* Responsive Filter */
@media (max-width: 640px) {
    .filter-btn span:not(.filter-count) {
        display: none;
    }
    .filter-btn {
        padding: 0.6rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryCards = document.querySelectorAll('.gallery-card');
    
    // Count cards per category
    function updateCounts() {
        const counts = {
            all: 0,
            'Kegiatan Sekolah': 0,
            'Ekstrakulikuler': 0,
            'Berita Sekolah': 0,
            general: 0
        };
        
        galleryCards.forEach(card => {
            const category = card.getAttribute('data-category');
            counts.all++;
            // Count by exact category name
            if (counts[category] !== undefined) {
                counts[category]++;
            }
        });
        
        // Update count displays with normalized IDs
        Object.keys(counts).forEach(cat => {
            const normalizedCat = cat.replace(/\s+/g, '-');
            const countEl = document.getElementById(`count-${normalizedCat}`);
            if (countEl) {
                countEl.textContent = counts[cat];
            }
        });
    }
    
    // Filter functionality
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter cards with animation
            galleryCards.forEach((card, index) => {
                const cardCategory = card.getAttribute('data-category');
                
                if (category === 'all' || cardCategory === category) {
                    card.classList.remove('hidden');
                    card.style.animation = 'none';
                    setTimeout(() => {
                        card.style.animation = `cardFadeInUp 0.6s ease-out ${0.1 * index}s forwards`;
                    }, 10);
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });
    
    // Initialize counts
    updateCounts();
});
</script>
@endsection