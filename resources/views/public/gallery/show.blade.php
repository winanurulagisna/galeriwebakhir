@extends('public.layouts.app')

@section('title', ($gallery->post ? $gallery->post->judul : 'Galeri') . ' - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('gallery.index') }}" class="inline-flex items-center text-[#023859] font-semibold hover:underline">
            <span class="mr-2">&larr;</span> Kembali ke Album
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Enhanced Header with Gradient -->
    <div class="text-center mb-12 header-animate">
        <h1 class="gallery-detail-title mb-4">
            {{ $gallery->post ? $gallery->post->judul : 'Galeri Foto' }}
        </h1>
        @if($gallery->post && $gallery->post->isi)
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                {{ Str::limit(strip_tags($gallery->post->isi), 150) }}
            </p>
        @endif
        @if($gallery->photos && $gallery->photos->count() > 0)
            <div class="mt-4 flex items-center justify-center gap-6 text-sm text-gray-500">
                <span class="flex items-center gap-2">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                    <strong>{{ $gallery->photos->count() }}</strong> Foto
                </span>
                <span class="flex items-center gap-2">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    {{ $gallery->created_at->format('d M Y') }}
                </span>
            </div>
        @endif
    </div>

    @if($gallery->photos && $gallery->photos->count() > 0)
        <!-- Featured Photo Section -->
        <div class="featured-photo-section mb-12">
            @php $featuredPhoto = $gallery->photos->first(); @endphp
            <div class="featured-photo-container">
                <a href="{{ \App\Helpers\ImageUrlHelper::getSafeImageUrl($featuredPhoto->file) }}" 
                   class="glightbox featured-photo-link" 
                   data-gallery="gallery-{{ $gallery->id }}"
                   data-title="{{ $featuredPhoto->caption ?: ($gallery->post ? $gallery->post->judul : 'Galeri') }}">
                    <img src="{{ \App\Helpers\ImageUrlHelper::getSafeImageUrl($featuredPhoto->file) }}" 
                         alt="{{ $featuredPhoto->caption ?: 'Featured Photo' }}" 
                         class="featured-photo-img"
                         onload="console.log('✓ Featured image loaded:', this.src);"
                         onerror="console.error('✗ Failed to load featured image:', this.src, 'Original:', '{{ $featuredPhoto->file }}'); this.onerror=null;">
                    <div class="featured-photo-overlay">
                        <div class="featured-badge">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>Foto Unggulan</span>
                        </div>
                        <div class="featured-caption">
                            <h3>{{ $featuredPhoto->caption ?: 'Klik untuk melihat' }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Gallery -->
        <div class="lg:col-span-3">
            @if($gallery->photos && $gallery->photos->count() > 0)
                <!-- Photo Grid - Instagram Style with Enhanced Animation -->
                <div class="photo-grid-enhanced mb-8">
                    @foreach($gallery->photos as $index => $photo)
                    <div class="instagram-card bg-white rounded-xl overflow-hidden shadow-md border border-gray-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <!-- Image Container -->
                        <div class="aspect-square bg-gray-100 relative overflow-hidden">
                            <a href="{{ \App\Helpers\ImageUrlHelper::getSafeImageUrl($photo->file) }}" 
                               class="glightbox block w-full h-full" 
                               data-gallery="gallery-{{ $gallery->id }}" 
                               data-title="{{ $photo->caption ?: ($gallery->post ? $gallery->post->judul : 'Galeri') }}" 
                               data-description="{{ $photo->caption ?: '' }}">
                                <img src="{{ \App\Helpers\ImageUrlHelper::getSafeImageUrl($photo->file) }}" 
                                     alt="{{ $photo->caption ?: 'Foto' }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                                     onload="console.log('✓ Image loaded successfully:', this.src);"
                                     onerror="console.error('✗ Failed to load image:', this.src, 'Original:', '{{ $photo->file }}'); this.onerror=null;">
                            </a>
                        </div>
                        
                        <!-- Instagram-style Footer -->
                        <div class="bg-white p-3">
                            <!-- Action Icons Row -->
                            <div class="flex items-center gap-3 mb-2">
                                <!-- Like Button (PHP Form) -->
                                @php 
                                    $isLiked = isset($likedPhotoIds) && in_array($photo->id, $likedPhotoIds);
                                    $likeClass = $isLiked ? 'text-red-500' : 'text-gray-700';
                                    $likeText = $isLiked ? 'Hapus like' : 'Suka foto';
                                @endphp
                                <form method="POST" action="{{ route('photo.like', $photo) }}" class="inline">
                                    @csrf
                                    <button 
                                        type="submit"
                                        class="p-1 rounded-full hover:bg-gray-100 transition-all duration-200 hover:scale-110 active:scale-95 {{ $likeClass }} focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-opacity-50"
                                        title="{{ $likeText }}">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ $isLiked ? '#ef4444' : 'none' }}" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                    </button>
                                </form>
                                
                                <!-- Comment Icon -->
                                @auth
                                    @if(auth()->user()->hasVerifiedEmail())
                                        <button type="button" class="toggle-comment p-1 rounded-full hover:bg-gray-100 transition-all duration-200 hover:scale-110 active:scale-95" data-photo-id="{{ $photo->id }}">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" onclick="window.location.href='{{ route('verification.notice') }}'" class="p-1 rounded-full hover:bg-yellow-50 transition-all duration-200 hover:scale-110 active:scale-95" title="Verifikasi email untuk komentar">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                @else
                                    <button type="button" onclick="window.location.href='{{ route('login') }}'" class="p-1 rounded-full hover:bg-gray-100 transition-all duration-200 hover:scale-110 active:scale-95" title="Login untuk komentar">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                        </svg>
                                    </button>
                                @endauth
                                
                                <!-- Share Icon -->
                                <button 
                                    class="share-btn p-1 rounded-full hover:bg-gray-100 transition-all duration-200 hover:scale-110 active:scale-95"
                                    data-photo-id="{{ $photo->id }}"
                                    data-photo-url="{{ \App\Helpers\ImageUrlHelper::getSafeImageUrl($photo->file) }}"
                                    data-photo-title="{{ $photo->caption ?: ($gallery->post ? $gallery->post->judul : 'Foto') }}"
                                    data-gallery-url="{{ route('gallery.show', $gallery) }}"
                                    aria-label="Bagikan foto" 
                                    title="Bagikan foto">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                </button>
                                
                                <!-- Download Button -->
                                @auth
                                    @if(auth()->user()->hasVerifiedEmail())
                                        <a href="{{ route('photo.download', $photo) }}" 
                                            class="ml-auto p-1 rounded-full hover:bg-blue-50 transition-all duration-200 hover:scale-110 active:scale-95"
                                            aria-label="Unduh foto" title="Unduh foto">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                        </a>
                                    @else
                                        <button type="button" onclick="window.location.href='{{ route('verification.notice') }}'" 
                                            class="ml-auto p-1 rounded-full hover:bg-yellow-50 transition-all duration-200 hover:scale-110 active:scale-95"
                                            aria-label="Verifikasi untuk download" title="Verifikasi email untuk download">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                        </button>
                                    @endif
                                @else
                                    <button type="button" onclick="window.location.href='{{ route('login') }}'" 
                                        class="ml-auto p-1 rounded-full hover:bg-blue-50 transition-all duration-200 hover:scale-110 active:scale-95"
                                        aria-label="Login untuk download" title="Login untuk download">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                    </button>
                                @endauth
                            </div>
                            
                            <!-- Likes Count -->
                            <div class="mb-1.5 like-count" data-photo-id="{{ $photo->id }}">
                                <span class="font-semibold text-xs text-gray-900">
                                    <span class="count">{{ $photo->likes_count ?? 0 }}</span> likes
                                </span>
                            </div>
                            
                            <!-- Caption -->
                            <div class="mb-1.5">
                                <p class="text-xs text-gray-900 leading-relaxed">
                                    <span class="font-bold">{{ $gallery->post ? $gallery->post->judul : 'Gallery' }}</span>
                                    @if($photo->caption)
                                        {{ $photo->caption }}
                                    @else
                                        <span class="text-gray-400">Tanpa caption</span>
                                    @endif
                                </p>
                            </div>
                            
                            <!-- Comments Count -->
                            @php
                                $countComments = 0;
                                if (!empty($hasCommentsTable) && $hasCommentsTable) {
                                    $countComments = $photo->getAttribute('comments_count') ?? 0;
                                }
                                if (!empty($jsonCommentsByPhoto) && !empty($jsonCommentsByPhoto[$photo->id])) {
                                    $countComments += count($jsonCommentsByPhoto[$photo->id]);
                                }
                            @endphp
                            @if($countComments > 0)
                            <button type="button" class="text-xs text-gray-500 mb-2 hover:text-gray-700 toggle-comments-view" data-photo-id="{{ $photo->id }}">
                                Lihat semua {{ $countComments }} komentar
                            </button>
                            @endif
                            
                            <!-- Comments List (Instagram Style) -->
                            <div id="comments-list-{{ $photo->id }}" class="hidden space-y-2 mb-2 max-h-48 overflow-y-auto" style="scrollbar-width: thin;">
                                @if(!empty($jsonCommentsByPhoto) && !empty($jsonCommentsByPhoto[$photo->id]))
                                    @foreach($jsonCommentsByPhoto[$photo->id] as $jc)
                                    <div class="flex gap-2">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-[10px] font-bold">
                                            {{ strtoupper(substr($jc['name'] ?? 'P', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs">
                                                <span class="font-bold text-gray-900">{{ $jc['name'] ?? 'Pengunjung' }}</span>
                                                <span class="text-gray-700 ml-1">{{ $jc['body'] ?? '' }}</span>
                                            </p>
                                            @php
                                                $created = $jc['created_at'] ?? null;
                                                $human = '-';
                                                if (is_string($created) && strtotime($created) !== false) {
                                                    try { $human = \Carbon\Carbon::parse($created)->diffForHumans(); } catch (\Throwable $e) { $human = '-'; }
                                                }
                                            @endphp
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $human }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                                
                                @if(!empty($hasCommentsTable) && $hasCommentsTable && isset($photo->comments) && $photo->comments->count())
                                    @foreach($photo->comments as $c)
                                    <div class="flex gap-2">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-[10px] font-bold">
                                            {{ strtoupper(substr($c->user->name ?? $c->first_name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs">
                                                <span class="font-bold text-gray-900">{{ $c->user->name ?? (trim(($c->first_name ?? '').' '.($c->last_name ?? '')) ?: ($c->email ?? 'Pengguna')) }}</span>
                                                <span class="text-gray-700 ml-1">{{ $c->body }}</span>
                                            </p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $c->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <!-- Timestamp -->
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">
                                {{ $photo->created_at ? $photo->created_at->diffForHumans() : '-' }}
                            </p>

                            @if(session('comment_success_photo') == $photo->id && session('success'))
                            <div class="mt-2 text-xs text-green-700 bg-green-50 border border-green-200 rounded px-3 py-2">
                                {{ session('success') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
            </div>

                <!-- Gallery Info -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Informasi Galeri</h2>
                            <p class="text-gray-600">Total {{ $gallery->photos->count() }} foto dalam galeri ini</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $gallery->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    @if($gallery->post && $gallery->post->isi)
                    <div class="prose prose-lg max-w-none text-gray-700">
                        {!! $gallery->post->isi !!}
                    </div>
                    @endif
                </div>
            @else
                <!-- No Photos -->
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <i class="fas fa-images text-gray-400 text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Belum ada foto</h3>
                    <p class="text-gray-600">Galeri ini belum memiliki foto. Foto akan ditampilkan di sini setelah ditambahkan.</p>
                </div>
            @endif
                </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Related Galleries -->
            @if($relatedGalleries && $relatedGalleries->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Galeri Terkait</h2>
                <div class="space-y-4">
                    @foreach($relatedGalleries as $relatedGallery)
                    <div class="flex space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                @if($relatedGallery->photos && $relatedGallery->photos->count() > 0)
                                    @php
                                        $rfile = $relatedGallery->photos->first()->file ?? null;
                                        $thumb = $rfile ? \App\Helpers\ImageUrlHelper::getSafeImageUrl($rfile) : asset('images/default-ekstrakurikuler.jpg');
                                    @endphp
                                    <img src="{{ $thumb }}" 
                                         alt="{{ $relatedGallery->post ? $relatedGallery->post->judul : 'Galeri' }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                                <a href="{{ route('gallery.show', $relatedGallery) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $relatedGallery->post ? $relatedGallery->post->judul : 'Galeri' }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ $relatedGallery->photos ? $relatedGallery->photos->count() : 0 }} foto
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            

            

            <!-- Back to Gallery -->
            <div class="bg-purple-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-3">Jelajahi Galeri Lainnya</h3>
                <p class="text-purple-700 mb-4">Temukan koleksi foto dan dokumentasi kegiatan SMKN 4 KOTA BOGOR</p>
                <a href="{{ route('gallery.index') }}" 
                   class="inline-flex items-center bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-images mr-2"></i>
                    Lihat Semua Galeri
                    </a>
                </div>
            </div>
    </div>
</div>

<!-- Comment Modal (Auth Required) -->
@auth
    @if(auth()->user()->hasVerifiedEmail())
        <div id="commentModal" class="login-overlay">
            <div class="login-modal" style="position: relative; max-width: 500px;">
                <button class="login-close" onclick="closeCommentModal()">&times;</button>
                <h2 class="login-title" style="font-size: 28px;">Tulis Komentar</h2>
                <p class="login-subtitle">Berikan komentar Anda untuk foto ini</p>
                
                <form id="commentModalForm" method="POST" action="#" style="margin-top: 24px;">
                    @csrf
                    
                    <!-- User Info (Auto-filled) -->
                    <div style="margin-bottom: 16px; padding: 12px; background: #f3f4f6; border-radius: 12px;">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    
                    <div style="margin-bottom: 16px;">
                        <label class="login-label">Komentar</label>
                        <textarea name="body" rows="4" class="login-input" placeholder="Tulis komentar Anda..." required style="resize: vertical; min-height: 100px; font-family: inherit;"></textarea>
                    </div>
                    
                    <button type="submit" class="login-btn">Kirim Komentar</button>
                </form>
            </div>
        </div>
    @endif
@endauth

<!-- Include Share Modal Component -->
@include('components.share-modal')

@endsection

@section('styles')
<style>
/* Enhanced Header Animation */
.header-animate {
    animation: fadeInDown 0.8s ease-out;
}

.gallery-detail-title {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #023859 0%, #26658C 30%, #54ACBF 70%, #A7EBF2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.3;
    animation: gradientShift 3s ease infinite;
    background-size: 200% auto;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Featured Photo Section */
.featured-photo-section {
    animation: fadeInUp 0.8s ease-out 0.3s both;
}

.featured-photo-container {
    position: relative;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(38, 101, 140, 0.2);
    background: linear-gradient(135deg, #A7EBF2 0%, #54ACBF 100%);
}

.featured-photo-link {
    display: block;
    position: relative;
    aspect-ratio: 21/9;
    overflow: hidden;
}

.featured-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.featured-photo-link:hover .featured-photo-img {
    transform: scale(1.08);
}

.featured-photo-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2rem;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.featured-photo-link:hover .featured-photo-overlay {
    opacity: 1;
}

.featured-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #54ACBF 0%, #26658C 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.875rem;
    box-shadow: 0 4px 15px rgba(84, 172, 191, 0.5);
    align-self: flex-start;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.featured-caption {
    align-self: flex-end;
    width: 100%;
}

.featured-caption h3 {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    margin: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced Photo Grid */
.photo-grid-enhanced {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1rem;
    animation: fadeIn 0.8s ease-out 0.6s both;
}

@media (min-width: 640px) {
    .photo-grid-enhanced {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }
}

@media (min-width: 1024px) {
    .photo-grid-enhanced {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Enhanced Instagram Cards */
.instagram-card {
    border-radius: 12px !important;
    border: 1px solid #e5e7eb !important;
    background: white !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Style for links */
a {
    color: #1a56db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.instagram-card:hover {
    transform: translateY(-4px) scale(1.01) !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
}

.instagram-card .aspect-square {
    border-radius: 10px 10px 0 0;
}

.instagram-card .aspect-square img {
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.instagram-card:hover .aspect-square img {
    transform: scale(1.1);
}

/* Enhanced Action Buttons */
.like-btn, .toggle-comment, .share-btn, .download-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.like-btn:hover, .toggle-comment:hover, .share-btn:hover, .download-btn:hover {
    background: linear-gradient(135deg, rgba(167, 235, 242, 0.15), rgba(84, 172, 191, 0.15)) !important;
    transform: scale(1.15) rotate(5deg) !important;
}

.like-btn:active, .toggle-comment:active, .share-btn:active, .download-btn:active {
    transform: scale(0.95) !important;
}

.prose {
    line-height: 1.7;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #1f2937;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.prose p {
    margin-bottom: 1rem;
}

.prose ul, .prose ol {
    margin-bottom: 1rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Gallery hover effects */
.gallery-item:hover {
    transform: translateY(-4px);
    transition: transform 0.3s ease;
}
/* Download modal */
.dl-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);display:none;align-items:center;justify-content:center;z-index:50;}
.dl-overlay{backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);}
.dl-overlay.show{display:flex;animation:fadeIn .15s ease-in-out}
.dl-modal{width:100%;max-width:380px;background:#fff;border-radius:16px;box-shadow:0 20px 40px rgba(2,56,89,.18);transform:translateY(8px) scale(.98);opacity:0;transition:all .18s ease}
.dl-overlay.show .dl-modal{transform:translateY(0) scale(1);opacity:1}
.dl-head{padding:16px 18px;border-bottom:1px solid #eef2f7;display:flex;align-items:center;justify-content:space-between}
.dl-title{font-weight:800;color:#0f172a}
.dl-body{padding:16px 18px}
.dl-actions{padding:12px 18px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid #eef2f7}
.btn-blue{background:#2563eb;color:#fff;border:none;border-radius:10px;padding:10px 14px;font-weight:800}
.btn-blue:hover{filter:brightness(1.03)}
.btn-ghost{background:#f1f5f9;color:#0f172a;border:1px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-weight:700}
.err{color:#b91c1c;font-size:12px;margin-top:6px;display:none}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

/* Login modal for comments */
.login-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;z-index:60;}
.login-overlay{backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);}
.login-overlay.show{display:flex;animation:fadeIn .2s ease-in-out}
.login-modal{width:100%;max-width:440px;background:#fff;border-radius:20px;box-shadow:0 25px 50px rgba(0,0,0,.25);transform:translateY(20px) scale(.95);opacity:0;transition:all .2s ease;padding:40px;}
.login-overlay.show .login-modal{transform:translateY(0) scale(1);opacity:1}
.login-title{font-size:32px;font-weight:700;color:#1f2937;margin-bottom:8px;text-align:center;}
.login-subtitle{font-size:15px;color:#6b7280;margin-bottom:32px;text-align:center;}
.login-label{display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;}
.login-input{width:100%;padding:12px 16px;border:1px solid #d1d5db;border-radius:12px;font-size:15px;transition:all .2s;outline:none;}
.login-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1);}
.login-input::placeholder{color:#9ca3af;}
.login-forgot{display:block;font-size:14px;font-weight:600;color:#1f2937;margin-top:12px;margin-bottom:24px;text-decoration:none;}
.login-forgot:hover{color:#6366f1;}
.login-btn{width:100%;padding:14px;background:#1f2937;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s;margin-bottom:16px;}
.login-btn:hover{background:#111827;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.15);}
.login-google{width:100%;padding:12px;background:#fff;color:#1f2937;border:1px solid #d1d5db;border-radius:12px;font-size:15px;font-weight:600;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:24px;}
.login-google:hover{background:#f9fafb;border-color:#9ca3af;}
.login-signup{text-align:center;font-size:14px;color:#6b7280;}
.login-signup a{color:#1f2937;font-weight:600;text-decoration:none;}
.login-signup a:hover{color:#6366f1;}
.login-close{position:absolute;top:20px;right:20px;width:32px;height:32px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;font-size:20px;color:#6b7280;}
.login-close:hover{background:#e5e7eb;color:#1f2937;}

/* Screen reader only text */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Enhanced like button styles for better responsiveness */
.like-btn {
    min-width: 44px;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
}

.like-btn:focus {
    outline: 2px solid #ef4444;
    outline-offset: 2px;
}

.like-btn:active {
    transform: scale(0.95);
}

/* Responsive adjustments for mobile */
@media (max-width: 640px) {
    .like-btn {
        min-width: 40px;
        min-height: 40px;
        padding: 8px;
    }
    
    .like-btn svg {
        width: 20px;
        height: 20px;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .like-btn {
        border: 2px solid currentColor;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .like-btn {
        transition: none;
    }
    
    .like-btn:hover {
        transform: none;
    }
    
    .like-btn:active {
        transform: none;
    }
}

</style>
@endsection

@section('scripts')
<script>
// Initialize GLightbox for gallery
document.addEventListener('DOMContentLoaded', function() {
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: false,
        moreText: 'Lihat lebih banyak',
        moreLength: 60,
        closeOnOutsideClick: true,
        draggable: true,
        dragToleranceX: 40,
        dragToleranceY: 65,
        preload: true,
        svg: {
            close: '<svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>',
            next: '<svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>',
            prev: '<svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>',
            zoom: '<svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M12 10h-2v2H9v-2H7V9h2V7h1v2h2v1z"/></svg>'
        }
    });

    // Likes now use PHP forms - no JavaScript needed!

    // Real-time comments system
    const commentsPollingInterval = 10000; // 10 seconds
    const photoIds = [
        @foreach($gallery->photos as $photo)
            {{ $photo->id }},
        @endforeach
    ];
    
    // Function to update comments for a specific photo
    async function updatePhotoComments(photoId) {
        try {
            const response = await fetch(`/api/comments?photo_id=${photoId}`);
            const data = await response.json();
            
            if (data.success) {
                // Update comments count
                const countElement = document.querySelector(`.toggle-comments-view[data-photo-id="${photoId}"]`);
                if (countElement && data.count > 0) {
                    countElement.textContent = `Lihat semua ${data.count} komentar`;
                    countElement.style.display = 'block';
                } else if (countElement && data.count === 0) {
                    countElement.style.display = 'none';
                }
                
                // Update comments list
                const commentsList = document.getElementById(`comments-list-${photoId}`);
                if (commentsList && data.comments.length > 0) {
                    let commentsHtml = '';
                    data.comments.forEach(comment => {
                        const timeAgo = moment(comment.created_at).fromNow();
                        commentsHtml += `
                            <div class="flex gap-2">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-[10px] font-bold">
                                    ${comment.avatar}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs">
                                        <span class="font-bold text-gray-900">${comment.name}</span>
                                        <span class="text-gray-700 ml-1">${comment.body}</span>
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">${timeAgo}</p>
                                </div>
                            </div>
                        `;
                    });
                    commentsList.innerHTML = commentsHtml;
                }
            }
        } catch (error) {
            console.log('Error updating comments:', error);
        }
    }
    
    // Function to poll all photos for new comments
    function pollAllComments() {
        photoIds.forEach(photoId => {
            updatePhotoComments(photoId);
        });
    }
    
    // Start polling when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Set moment.js locale to Indonesian
        moment.locale('id');
        
        // Initial load
        setTimeout(pollAllComments, 2000);
        
        // Set up polling interval
        setInterval(pollAllComments, commentsPollingInterval);
        
        console.log('Real-time comments polling started');
    });

    // Store photo URLs in a map for easy access
    const photoCommentUrls = new Map();
    @foreach($gallery->photos as $photo)
        photoCommentUrls.set({{ $photo->id }}, '{{ route('photo.comment.store', $photo) }}');
    @endforeach

    // Open comment modal when clicking comment icon
    document.querySelectorAll('.toggle-comment').forEach(btn => {
        btn.addEventListener('click', () => {
            const photoId = btn.getAttribute('data-photo-id');
            openCommentModal(photoId);
        });
    });
    
    // Comment modal functions
    window.openCommentModal = function(photoId) {
        const modal = document.getElementById('commentModal');
        const form = document.getElementById('commentModalForm');
        
        // Get the correct URL from our map
        const actionUrl = photoCommentUrls.get(parseInt(photoId));
        
        if (!actionUrl) {
            console.error('No URL found for photo ID:', photoId);
            alert('Error: Tidak dapat menemukan URL untuk foto ini.');
            return;
        }
        
        // Set form action
        form.setAttribute('action', actionUrl);
        
        // Show modal
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Render reCAPTCHA if not already rendered
        setTimeout(() => {
            const recaptchaEl = document.getElementById('modal-recaptcha');
            if (recaptchaEl && recaptchaEl.innerHTML === '') {
                if (window.grecaptcha && typeof window.grecaptcha.render === 'function') {
                    try {
                        window.grecaptcha.render('modal-recaptcha', {
                            sitekey: recaptchaEl.getAttribute('data-sitekey')
                        });
                    } catch(e) {
                        console.log('reCAPTCHA already rendered or error:', e);
                    }
                }
            }
        }, 300);
    };
    
    window.closeCommentModal = function() {
        const modal = document.getElementById('commentModal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        // Reset form
        document.getElementById('commentModalForm').reset();
        if (window.grecaptcha) {
            try {
                window.grecaptcha.reset();
            } catch(e) {}
        }
    };
    
    // Close modal when clicking outside
    document.getElementById('commentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCommentModal();
        }
    });
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('commentModal').classList.contains('show')) {
            closeCommentModal();
        }
    });

    // Handle comment form submission with AJAX
    document.getElementById('commentModalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success toast
                showToast(data.message || 'Komentar berhasil dikirim!', 'success');
                
                // Close modal
                closeCommentModal();
                
                // Reset form
                form.reset();
            } else {
                showToast(data.message || 'Gagal mengirim komentar', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat mengirim komentar', 'error');
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Kirim Komentar';
        });
    });

    // Toggle inline comments view
    document.querySelectorAll('.toggle-comments-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const photoId = this.getAttribute('data-photo-id');
            const commentsList = document.getElementById(`comments-list-${photoId}`);
            
            if (commentsList.classList.contains('hidden')) {
                commentsList.classList.remove('hidden');
                this.textContent = 'Sembunyikan komentar';
            } else {
                commentsList.classList.add('hidden');
                const countMatch = this.textContent.match(/\d+/);
                const count = countMatch ? countMatch[0] : '0';
                this.textContent = `Lihat semua ${count} komentar`;
            }
        });
    });

    // Share functionality - Use enhanced modal
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const photoUrl = this.getAttribute('data-photo-url');
            const photoTitle = this.getAttribute('data-photo-title');
            const galleryUrl = this.getAttribute('data-gallery-url');
            
            // Animation
            this.style.transform = 'scale(1.2)';
            setTimeout(() => this.style.transform = 'scale(1)', 200);
            
            // Open enhanced share modal
            openShareModal(photoUrl, photoTitle, galleryUrl);
        });
    });
    
    // Fix image URLs with special characters
    function encodeImageUrls() {
        // Fix all img src attributes
        document.querySelectorAll('img[src]').forEach(img => {
            const originalSrc = img.getAttribute('src');
            if (originalSrc && originalSrc.includes('/images/')) {
                const lastSlashIndex = originalSrc.lastIndexOf('/');
                if (lastSlashIndex !== -1) {
                    const path = originalSrc.substring(0, lastSlashIndex + 1);
                    const filename = originalSrc.substring(lastSlashIndex + 1);
                    const encodedUrl = path + encodeURIComponent(filename);
                    img.src = encodedUrl;
                    console.log('Encoded image URL:', originalSrc, '->', encodedUrl);
                }
            }
        });
        
        // Fix all glightbox href attributes
        document.querySelectorAll('a.glightbox[href]').forEach(link => {
            const originalHref = link.getAttribute('href');
            if (originalHref && originalHref.includes('/images/')) {
                const lastSlashIndex = originalHref.lastIndexOf('/');
                if (lastSlashIndex !== -1) {
                    const path = originalHref.substring(0, lastSlashIndex + 1);
                    const filename = originalHref.substring(lastSlashIndex + 1);
                    const encodedUrl = path + encodeURIComponent(filename);
                    link.href = encodedUrl;
                    console.log('Encoded lightbox URL:', originalHref, '->', encodedUrl);
                }
            }
        });
    }
    
    // Run URL encoding after page load
    encodeImageUrls();
});
</script>
@endsection
