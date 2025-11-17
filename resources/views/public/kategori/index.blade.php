@extends('public.layouts.app')

@section('title', 'Kategori - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Kategori</h1>
        <p class="text-gray-600">Jelajahi berbagai kategori konten SMKN 4 KOTA BOGOR</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="categoriesGrid">
        <!-- Categories will be populated by JavaScript -->
    </div>

    <div class="mt-8 text-center" id="loadingMessage">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-gray-600">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memuat kategori...
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Load categories from API
async function loadCategories() {
    try {
        const response = await fetch('/api/categories');
        const categories = await response.json();
        
        const categoriesGrid = document.getElementById('categoriesGrid');
        const loadingMessage = document.getElementById('loadingMessage');
        
        if (categoriesGrid) {
            if (categories.length === 0) {
                categoriesGrid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <i class="fas fa-tags text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold mb-2">Belum ada kategori</h3>
                            <p>Kategori akan ditampilkan di sini</p>
                        </div>
                    </div>
                `;
            } else {
                const categoryItems = categories.map((category, index) => `
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="h-48 bg-cover bg-center relative" style="background-image: url('https://picsum.photos/400/200?random=${60 + index}')">
                            <a href="https://picsum.photos/800/400?random=${60 + index}" 
                               class="glightbox" 
                               data-gallery="kategori-gallery" 
                               data-title="${category.judul}" 
                               data-description="${category.description || 'Kategori SMKN 4 KOTA BOGOR'}">
                                <div class="h-full bg-black bg-opacity-40 flex items-center justify-center">
                                    <i class="fas fa-folder text-white text-6xl"></i>
                                </div>
                                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                    <div class="opacity-0 hover:opacity-100 transition-opacity duration-300">
                                        <i class="fas fa-expand text-white text-2xl"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-800 mb-2 text-lg">${category.judul}</h3>
                            <p class="text-gray-600 mb-4">${category.description || 'Tidak ada deskripsi'}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    ${category.created_at ? new Date(category.created_at).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}
                                </span>
                                <span class="text-sm text-blue-600 font-medium">
                                    <i class="fas fa-file-alt mr-1"></i>
                                    ${category.posts_count || 0} Post
                                </span>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                categoriesGrid.innerHTML = categoryItems;
            }
        }
        
        if (loadingMessage) {
            loadingMessage.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        loadSampleCategories();
    }
}

// Load sample categories data
function loadSampleCategories() {
    const sampleCategories = [
        {
            name: 'Akademik',
            description: 'Berita dan informasi seputar kegiatan akademik sekolah',
            created_at: '2024-01-01',
            posts_count: 15
        },
        {
            name: 'Ekstrakurikuler',
            description: 'Kegiatan ekstrakurikuler dan prestasi siswa',
            created_at: '2024-01-01',
            posts_count: 8
        },
        {
            name: 'Kegiatan Sekolah',
            description: 'Berbagai kegiatan dan acara sekolah',
            created_at: '2024-01-01',
            posts_count: 12
        },
        {
            name: 'Prestasi',
            description: 'Prestasi dan penghargaan yang diraih sekolah',
            created_at: '2024-01-01',
            posts_count: 6
        },
        {
            name: 'Teknologi',
            description: 'Informasi seputar teknologi dan inovasi',
            created_at: '2024-01-01',
            posts_count: 10
        },
        {
            name: 'Industri',
            description: 'Kerjasama dengan dunia industri dan prakerin',
            created_at: '2024-01-01',
            posts_count: 5
        }
    ];
    
    const categoriesGrid = document.getElementById('categoriesGrid');
    const loadingMessage = document.getElementById('loadingMessage');
    
    if (categoriesGrid) {
        const categoryItems = sampleCategories.map((category, index) => `
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="h-48 bg-cover bg-center relative" style="background-image: url('https://picsum.photos/400/200?random=${70 + index}')">
                    <a href="https://picsum.photos/800/400?random=${70 + index}" 
                       class="glightbox" 
                       data-gallery="kategori-gallery" 
                       data-title="${category.judul}" 
                       data-description="${category.description}">
                        <div class="h-full bg-black bg-opacity-40 flex items-center justify-center">
                            <i class="fas fa-folder text-white text-6xl"></i>
                        </div>
                        <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-expand text-white text-2xl"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="p-6">
                    <h3 class="font-semibold text-gray-800 mb-2 text-lg">${category.judul}</h3>
                    <p class="text-gray-600 mb-4">${category.description}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            ${category.created_at}
                        </span>
                        <span class="text-sm text-blue-600 font-medium">
                            <i class="fas fa-file-alt mr-1"></i>
                            ${category.posts_count} Post
                        </span>
                    </div>
                </div>
            </div>
        `).join('');
        
        categoriesGrid.innerHTML = categoryItems;
    }
    
    if (loadingMessage) {
        loadingMessage.style.display = 'none';
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    
    // Initialize GLightbox
    initLightbox();
});

// Initialize GLightbox
function initLightbox() {
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });
}
</script>
@endsection
