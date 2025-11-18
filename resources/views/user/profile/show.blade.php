@extends('public.layouts.app')

@section('title', 'Profile - SMKN 4 KOTA BOGOR')

@section('content')
<style>
    /* Hide broken images - but NOT photos with alt="Foto" which is our fallback title */
    img[src=""], img[src*="placeholder"] {
        display: none !important;
    }
    /* Hide parent div only if image actually fails to load, not just has alt="Foto" */
    .photo-error {
        display: none !important;
    }
</style>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Profile Header -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6 md:mb-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-5 md:px-6 py-5 md:py-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Profile</h1>
                    <a href="{{ route('user.profile.edit') }}" class="bg-white/20 hover:bg-white/30 text-white px-3.5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 text-sm md:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Edit
                    </a>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="px-4 md:px-5 py-4 md:py-6">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-4 md:gap-5">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center text-white font-bold text-xl md:text-3xl shadow-xl border-2 md:border-3 border-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-lg md:text-2xl font-bold text-gray-900 mb-1 md:mb-1.5">{{ $user->name }}</h2>
                        <p class="text-xs md:text-sm text-gray-500 mb-1 md:mb-1.5">{{ $user->email }}</p>
                        @if($user->hasVerifiedEmail())
                            <span class="inline-flex items-center gap-1 text-xs md:text-sm text-green-600 bg-green-50 px-2.5 py-0.5 md:px-3 md:py-1 rounded-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Terverifikasi
                            </span>
                        @endif
                    </div>

                    <!-- Statistics -->
                    <div class="flex gap-3 md:gap-5">
                        <div class="text-center">
                            <div class="text-lg md:text-xl font-bold text-pink-600">{{ $likesCount }}</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Likes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg md:text-xl font-bold text-blue-600">{{ $commentsCount }}</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Komentar</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg md:text-xl font-bold text-emerald-600">{{ $downloadsCount }}</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Unduhan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Tabs -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button class="tab-btn active flex-1 py-3 md:py-4 px-2 md:px-6 text-center font-semibold text-pink-600 border-b-2 border-pink-600 bg-pink-50" data-tab="likes">
                        <div class="flex flex-col md:flex-row items-center justify-center gap-1 md:gap-2">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs md:text-sm">Disukai</span>
                        </div>
                    </button>
                    <button class="tab-btn flex-1 py-3 md:py-4 px-2 md:px-6 text-center font-semibold text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all" data-tab="comments">
                        <div class="flex flex-col md:flex-row items-center justify-center gap-1 md:gap-2">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            <span class="text-xs md:text-sm">Dikomentar</span>
                        </div>
                    </button>
                    <button class="tab-btn flex-1 py-3 md:py-4 px-2 md:px-6 text-center font-semibold text-gray-500 hover:text-green-600 hover:bg-green-50 transition-all" data-tab="downloads">
                        <div class="flex flex-col md:flex-row items-center justify-center gap-1 md:gap-2">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <span class="text-xs md:text-sm">Diunduh</span>
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-3 md:p-6">
                <!-- Likes Tab -->
                <div id="likes-tab" class="tab-content">
                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 md:gap-2">
                        <!-- Photos will be loaded here -->
                        <div id="likes-grid" class="contents">
                            <!-- Photos loaded via AJAX -->
                        </div>
                    </div>
                    
                    <div id="likes-loading" class="text-center py-8 hidden">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-pink-600"></div>
                        <p class="text-gray-500 mt-2">Memuat...</p>
                    </div>
                    <div id="likes-empty" class="text-center py-12 hidden">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada yang disukai</p>
                    </div>
                </div>

                <!-- Comments Tab -->
                <div id="comments-tab" class="tab-content hidden">
                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 md:gap-2">
                        <div id="comments-grid" class="contents">
                            <!-- Photos loaded via AJAX -->
                        </div>
                    </div>
                    
                    <div id="comments-loading" class="text-center py-8 hidden">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="text-gray-500 mt-2">Memuat...</p>
                    </div>
                    <div id="comments-empty" class="text-center py-12 hidden">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada yang dikomentar</p>
                    </div>
                </div>

                <!-- Downloads Tab -->
                <div id="downloads-tab" class="tab-content hidden">
                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 md:gap-2">
                        <div id="downloads-grid" class="contents">
                            <!-- Photos loaded via AJAX -->
                        </div>
                    </div>
                    
                    <div id="downloads-loading" class="text-center py-8 hidden">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                        <p class="text-gray-500 mt-2">Memuat...</p>
                    </div>
                    <div id="downloads-empty" class="text-center py-12 hidden">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada yang diunduh</p>
                    </div>
                </div>

                <!-- Load More Button -->
                <div class="text-center mt-8">
                    <button id="load-more-btn" class="hidden bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200">
                        Muat Lebih Banyak
                    </button>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-teal-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="font-medium">Kembali ke Beranda</span>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentTab = 'likes';
    let currentPage = 1;
    let hasMorePages = true;
    let isLoading = false;

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');
            switchTab(tab);
        });
    });

    // Load more button
    document.getElementById('load-more-btn').addEventListener('click', function() {
        loadPhotos(currentTab, currentPage + 1, true);
    });

    function switchTab(tab) {
        if (isLoading) return;

        currentTab = tab;
        currentPage = 1;
        hasMorePages = true;

        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            if (tab === 'likes') {
                btn.classList.remove('text-pink-600', 'border-pink-600', 'bg-pink-50');
                btn.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
                btn.classList.remove('text-green-600', 'border-green-600', 'bg-green-50');
                btn.classList.add('text-gray-500');
            } else if (tab === 'comments') {
                btn.classList.remove('text-pink-600', 'border-pink-600', 'bg-pink-50');
                btn.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
                btn.classList.remove('text-green-600', 'border-green-600', 'bg-green-50');
                btn.classList.add('text-gray-500');
            } else if (tab === 'downloads') {
                btn.classList.remove('text-pink-600', 'border-pink-600', 'bg-pink-50');
                btn.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
                btn.classList.remove('text-green-600', 'border-green-600', 'bg-green-50');
                btn.classList.add('text-gray-500');
            }
        });

        const activeBtn = document.querySelector(`[data-tab="${tab}"]`);
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-gray-500');
        
        if (tab === 'likes') {
            activeBtn.classList.add('text-pink-600', 'border-b-2', 'border-pink-600', 'bg-pink-50');
        } else if (tab === 'comments') {
            activeBtn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600', 'bg-blue-50');
        } else if (tab === 'downloads') {
            activeBtn.classList.add('text-green-600', 'border-b-2', 'border-green-600', 'bg-green-50');
        }

        // Show/hide tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(`${tab}-tab`).classList.remove('hidden');

        // Load photos for the new tab
        loadPhotos(tab, 1, false);
    }

    async function loadPhotos(tab, page, append = false) {
        if (isLoading) return;
        
        isLoading = true;
        const loadingEl = document.getElementById(`${tab}-loading`);
        const gridEl = document.getElementById(`${tab}-grid`);
        const emptyEl = document.getElementById(`${tab}-empty`);
        const loadMoreBtn = document.getElementById('load-more-btn');

        // Show loading
        loadingEl.classList.remove('hidden');
        emptyEl.classList.add('hidden');
        loadMoreBtn.classList.add('hidden');

        try {
            // Add cache-busting parameter
            const cacheBuster = Date.now();
            const response = await fetch(`/user/activity/${tab}?page=${page}&_=${cacheBuster}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response format');
            }
            
            // Check if response indicates error
            if (data.success === false) {
                throw new Error(data.message || data.error || 'Unknown error');
            }

            if (data.success) {
                if (!append) {
                    gridEl.innerHTML = '';
                }

                if (data.photos && data.photos.length > 0) {
                    console.log(`Loading ${data.photos.length} photos for ${tab} tab`);
                    console.log('Photos data:', data.photos);
                    
                    let renderedCount = 0;
                    data.photos.forEach((item, index) => {
                        const photo = item.photo || item;
                        if (photo) {
                            try {
                                // Enhanced debug for berita photos
                                if (photo.is_berita || photo.related_type === 'berita' || (item && item.is_berita_download)) {
                                    console.log(`🔥 BERITA ITEM ${index + 1}:`, {
                                        photo_id: photo.id,
                                        file: photo.file,
                                        optimal_url: photo.optimal_url,
                                        related_type: photo.related_type,
                                        is_berita: photo.is_berita,
                                        has_actual_image: photo.has_actual_image,
                                        berita_title: photo.berita_title,
                                        judul: photo.judul,
                                        is_berita_download: item ? item.is_berita_download : false
                                    });
                                }
                                
                                const photoEl = createPhotoElement(photo, tab, item);
                                gridEl.appendChild(photoEl);
                                renderedCount++;
                                console.log(`Photo ${index + 1} rendered:`, photo.judul || photo.berita_title || 'No title', `(Type: ${photo.related_type || 'gallery'})`);
                            } catch (error) {
                                console.error(`Error rendering photo ${index + 1}:`, error, photo);
                            }
                        } else {
                            console.warn(`Photo ${index + 1} is null or undefined:`, item);
                        }
                    });
                    
                    // Verifikasi DOM
                    setTimeout(() => {
                        const actualCount = gridEl.children.length;
                        console.log(`Expected: ${data.photos.length}, Rendered: ${renderedCount}, DOM Count: ${actualCount}`);
                        if (actualCount !== data.photos.length) {
                            console.error('Mismatch between expected and actual photo count!');
                        }
                    }, 100);

                    // Update pagination
                    currentPage = page;
                    hasMorePages = data.hasMore;

                    if (hasMorePages) {
                        loadMoreBtn.classList.remove('hidden');
                    }
                } else if (!append) {
                    emptyEl.classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Error loading photos:', error);
            // Show error message to user
            if (!append) {
                gridEl.innerHTML = `
                    <div class="col-span-full text-center py-8">
                        <p class="text-red-500 mb-2">Terjadi kesalahan saat memuat foto.</p>
                        <p class="text-sm text-gray-600 mb-3">${error.message || 'Unknown error'}</p>
                        <button onclick="location.reload()" class="mt-2 bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg">
                            Muat Ulang Halaman
                        </button>
                    </div>
                `;
            }
        } finally {
            isLoading = false;
            loadingEl.classList.add('hidden');
        }
    }

    function createPhotoElement(photo, tab, item = null) {
        try {
            const div = document.createElement('div');
            div.className = 'group relative aspect-square bg-gray-100 rounded-md overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer';
            
            // Validate photo object
            if (!photo || !photo.id) {
                console.warn('Invalid photo object:', photo);
                return div;
            }
            
            // Handle gallery name for both gallery and berita photos
            let galleryName = 'Galeri';
            let isBerita = false;
            
            // Check multiple indicators for berita
            if (photo.is_berita || 
                photo.related_type === 'berita' || 
                photo.berita_title ||
                (item && item.is_berita_download)) {
                galleryName = 'Berita';
                isBerita = true;
            } else if (photo.gallery && photo.gallery.title) {
                galleryName = photo.gallery.title;
            }
            
            // Use berita title if available, otherwise use judul or default
            const photoTitle = photo.berita_title || photo.judul || photo.caption || 'Foto';
            
            // Get berita URL if available
            const beritaUrl = photo.berita_url || (isBerita ? `/berita/${photo.id}` : null);
        
        // Handle image URL for berita vs regular photos
        let imageUrl;
        if (isBerita) {
            // For berita, use actual berita image or default
            imageUrl = photo.optimal_url || photo.file || '/images/default-berita.jpg';
            
            // If we have an actual image but it's still showing default, try to get from photo object
            if ((!imageUrl || imageUrl === '/images/default-berita.jpg') && photo.photo && photo.photo.file) {
                imageUrl = photo.photo.file;
            }
            
            // Handle URL encoding for berita images
            if (imageUrl && imageUrl !== '/images/default-berita.jpg') {
                try {
                    // Only encode if it's not already encoded
                    if (imageUrl.includes(' ')) {
                        const url = new URL(imageUrl, window.location.origin);
                        // Rebuild the URL with encoded pathname
                        const pathParts = url.pathname.split('/');
                        const filename = pathParts.pop();
                        const encodedFilename = encodeURIComponent(decodeURIComponent(filename))
                            .replace(/\(/g, '%28')
                            .replace(/\)/g, '%29');
                        pathParts.push(encodedFilename);
                        imageUrl = pathParts.join('/');
                    }
                } catch (e) {
                    console.warn('Error processing image URL:', e);
                }
            }
        } else {
            // For regular photos, use optimal_url, file, or file_path
            imageUrl = photo.optimal_url || photo.file || photo.file_path || '/images/placeholder.svg';
        }
        
        // Final fallback for image URL - but don't use berita default for regular photos
        if (!imageUrl) {
            imageUrl = '/images/placeholder.svg';
        }
        
        // Log for debugging
        console.log('Photo element created:', {
            id: photo.id,
            isBerita,
            imageUrl,
            photoTitle,
            galleryName
        });
        
        div.innerHTML = `
            <img src="${imageUrl}" alt="${photoTitle.replace(/"/g, '&quot;')}" 
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                 onerror="console.error('Image failed to load:', '${imageUrl.replace(/'/g, "\\'")}'); 
                        this.classList.add('opacity-0');
                        this.nextElementSibling.classList.remove('hidden');">
            <div class="hidden absolute inset-0 bg-gray-100 flex items-center justify-center">
                <span class="text-xs text-gray-500">Gambar tidak tersedia</span>
            </div>
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center">
                <div class="text-white text-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-2">
                    <div class="text-xs font-semibold truncate px-1">${photoTitle}</div>
                    <div class="text-[10px] mt-0.5 text-gray-200">${galleryName}</div>
                    ${photo.is_heic ? '<div class="text-[9px] mt-1 text-yellow-300 bg-black/50 px-1 rounded">HEIC</div>' : ''}
                    ${isBerita ? '<div class="text-[9px] mt-1 text-blue-300 bg-black/50 px-1 rounded">BERITA</div>' : ''}
                </div>
            </div>
        `;

            // Add click handler
            div.addEventListener('click', function() {
                if (isBerita && beritaUrl) {
                    // For berita, open berita page with correct URL
                    window.open(beritaUrl, '_blank');
                } else if (photo.file || photo.file_path || photo.optimal_url) {
                    // For photos, open image directly
                    const photoUrl = photo.file || photo.file_path || photo.optimal_url;
                    window.open(photoUrl, '_blank');
                }
            });

            return div;
        } catch (error) {
            console.error('Error creating photo element:', error, photo);
            // Return empty div on error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'aspect-square bg-gray-200 rounded-md flex items-center justify-center';
            errorDiv.innerHTML = '<span class="text-xs text-gray-400">Error</span>';
            return errorDiv;
        }
    }

    // Initialize with likes tab
    loadPhotos('likes', 1, false);
    
    // Handle broken images properly
    document.addEventListener('DOMContentLoaded', function() {
        // Handle image load errors for existing images
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                // Only hide if image actually fails to load, not just because alt="Foto"
                const parent = this.closest('.group, .aspect-square, a');
                if (parent && this.src && this.src !== window.location.href) {
                    parent.classList.add('photo-error');
                }
            });
        });
    });
});
</script>
@endsection
