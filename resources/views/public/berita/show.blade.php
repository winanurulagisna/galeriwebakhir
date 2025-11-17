@extends('public.layouts.app')

@section('title', $post->judul . ' - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-6">
    
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

    <!-- Back Button -->
    <div class="mb-4 md:mb-6">
        <button onclick="window.history.back()" 
           class="inline-flex items-center gap-1 md:gap-2 px-3 py-2 md:px-4 md:py-2 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg text-gray-700 hover:text-gray-900 transition-all duration-200 shadow-sm hover:shadow-md group text-sm md:text-base">
            <svg class="w-4 h-4 md:w-5 md:h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="font-medium">Kembali ke Halaman Berita Sekolah</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <article class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Featured Image -->
                @if($post->image)
                <div class="aspect-video bg-gray-200">
                    <img src="{{ $post->image ?: '/images/default-berita.jpg' }}" 
                         alt="{{ $post->judul }}" 
                         class="w-full h-full object-cover"
                         onerror="this.onerror=null; this.src='/images/placeholder.svg';">
                </div>
                @endif
                
                <div class="p-6">
                    <!-- Meta Information -->
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs md:text-sm">
                                {{ $post->category ? $post->category->judul : 'Umum' }}
                            </span>
                            <span class="text-gray-500 text-xs md:text-sm">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $post->created_at->format('d M Y') }}
                            </span>
                            <span class="text-gray-500 text-xs md:text-sm">
                                <i class="fas fa-eye mr-1"></i>
                                {{ $post->views }} views
                            </span>
                        </div>
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 leading-tight">
                        {{ $post->judul }}
                    </h1>

                    <!-- Content -->
                    @php
                        // Utamakan kolom url; fallback ke parsing dari isi bila kosong
                        $externalUrl = $post->url ?? null;
                        $cleanContent = $post->isi;
                        if (!empty($externalUrl)) {
                            // hapus hanya kemunculan pertama dari isi bila ada
                            $cleanContent = preg_replace('/'.preg_quote($externalUrl, '/').'/', '', (string)$post->isi, 1);
                        } elseif (!empty($post->isi)) {
                            if (preg_match('/https?:\/\/[^\s"\']+/i', $post->isi, $m)) {
                                $externalUrl = $m[0];
                                $cleanContent = preg_replace('/'.preg_quote($externalUrl, '/').'/', '', $post->isi, 1);
                            }
                        }
                    @endphp
                    <div class="prose prose-lg max-w-none text-gray-700">
                        {!! $cleanContent !!}
                    </div>

                    <!-- Interaction Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex flex-wrap items-center gap-4">
                                <!-- Like Button -->
                                @php
                                    $isLiked = auth()->check() && \App\Models\PostLike::where('photo_id', $post->id)->where('user_id', auth()->id())->exists();
                                    $likesCount = \App\Models\PostLike::where('photo_id', $post->id)->count();
                                    $downloadCount = (int)($post->downloads_count ?? 0);
                                @endphp
                                <form method="POST" action="{{ route('post.like', $post) }}" class="inline like-form" data-post-id="{{ $post->id }}">
                                    @csrf
                                    <button type="submit" class="like-btn group flex items-center gap-1 md:gap-2 transition-colors {{ $isLiked ? 'text-pink-500' : 'text-gray-500 hover:text-pink-500' }}" data-liked="{{ $isLiked ? 'true' : 'false' }}">
                                        <i class="{{ $isLiked ? 'fas' : 'far' }} fa-heart group-hover:fas text-base md:text-lg like-icon"></i>
                                        <span class="font-medium text-sm md:text-base">
                                            <span class="like-count">{{ $likesCount }}</span> Suka
                                        </span>
                                    </button>
                                </form>
                                
                                <!-- Comment Button -->
                                @php
                                    $commentsCount = \App\Models\PostComment::where('photo_id', $post->id)->where('comment_type', 'post')->where('status', 'approved')->count();
                                @endphp
                                <a href="#comments" class="comment-btn group flex items-center gap-1 md:gap-2 text-gray-500 hover:text-blue-500 transition-colors">
                                    <i class="far fa-comment text-base md:text-lg"></i>
                                    <span class="font-medium text-sm md:text-base">{{ $commentsCount }} Komentar</span>
                                </a>
                                
                                <!-- Download Button -->
                                @auth
                                    @if(auth()->user()->hasVerifiedEmail())
                                        <a href="{{ route('berita.download', $post) }}" class="download-btn group flex items-center gap-1 md:gap-2 text-gray-500 hover:text-green-500 transition-colors">
                                            <i class="fas fa-download text-base md:text-lg"></i>
                                            <span class="font-medium text-sm md:text-base">
                                                <span class="download-count">{{ $downloadCount }}</span> Unduh
                                            </span>
                                        </a>
                                    @else
                                        <div class="flex items-center gap-1 md:gap-2 px-3 py-1 md:px-4 md:py-2 bg-yellow-50 text-yellow-600 rounded-lg">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="md:w-5 md:h-5">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                            <span class="font-semibold download-count text-sm md:text-base">{{ $downloadCount }}</span>
                                            <span class="text-xs md:text-sm">Unduh</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="flex items-center gap-1 md:gap-2 px-3 py-1 md:px-4 md:py-2 bg-gray-100 text-gray-500 rounded-lg">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="md:w-5 md:h-5">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        <span class="font-semibold download-count text-sm md:text-base">{{ $downloadCount }}</span>
                                        <span class="text-xs md:text-sm">Unduh</span>
                                    </div>
                                @endauth
                            </div>
                            
                            <!-- Views Counter -->
                            <div class="text-xs md:text-sm text-gray-500">
                                <i class="fas fa-eye mr-1"></i>
                                {{ number_format($post->views ?? 0) }} kali dilihat
                            </div>
                        </div>
                    </div>

                    <!-- Tags (if any) -->
                    @if($post->tags)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Tags:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $post->tags) as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                {{ trim($tag) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Share Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Bagikan:</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank"
                               class="flex items-center gap-1 md:gap-2 px-3 py-2 md:px-4 md:py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm md:text-base">
                                <i class="fab fa-facebook text-sm md:text-base"></i>
                                <span>Facebook</span>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->judul) }}" 
                               target="_blank"
                               class="flex items-center gap-1 md:gap-2 px-3 py-2 md:px-4 md:py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors text-sm md:text-base">
                                <i class="fab fa-twitter text-sm md:text-base"></i>
                                <span>Twitter</span>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($post->judul . ' - ' . request()->url()) }}" 
                               target="_blank"
                               class="flex items-center gap-1 md:gap-2 px-3 py-2 md:px-4 md:py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm md:text-base">
                                <i class="fab fa-whatsapp text-sm md:text-base"></i>
                                <span>WhatsApp</span>
                            </a>
                            <button onclick="copyBeritaLink()" 
                                    class="flex items-center gap-1 md:gap-2 px-3 py-2 md:px-4 md:py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm md:text-base">
                                <i class="fas fa-copy text-sm md:text-base"></i>
                                <span>Copy Link</span>
                            </button>
                        </div>
                    </div>
                    
                    <script>
                    function copyBeritaLink() {
                        const url = window.location.href;
                        const textArea = document.createElement('textarea');
                        textArea.value = url;
                        textArea.style.position = 'fixed';
                        textArea.style.left = '-999999px';
                        document.body.appendChild(textArea);
                        textArea.select();
                        try {
                            document.execCommand('copy');
                            alert('✓ Link berhasil disalin!');
                        } catch (err) {
                            alert('✗ Gagal menyalin link');
                        }
                        document.body.removeChild(textArea);
                    }
                    </script>

                    <!-- Comments Section -->
                    <div class="mt-8 pt-6 border-t border-gray-200" id="comments">
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komentar</h3>
                            
                            <!-- Comment Form -->
                            @auth
                                @if(auth()->user()->hasVerifiedEmail())
                                    <form method="POST" action="{{ route('post.comment.store', $post) }}" class="mb-6">
                                        @csrf
                                        <textarea 
                                            name="body" 
                                            rows="3" 
                                            class="w-full px-3 py-2 md:px-4 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm md:text-base"
                                            placeholder="Tulis komentar Anda..."
                                            required
                                            maxlength="1000"></textarea>
                                        <div class="flex flex-wrap justify-between items-center gap-2 mt-2">
                                            <span class="text-xs md:text-sm text-gray-500">Maksimal 1000 karakter</span>
                                            <button 
                                                type="submit" 
                                                class="px-4 py-2 md:px-6 md:py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm md:text-base">
                                                Kirim Komentar
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="mb-6 p-3 md:p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-yellow-800 text-sm md:text-base">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            Silakan verifikasi email Anda untuk dapat berkomentar.
                                            <a href="{{ route('verification.notice') }}" class="underline font-semibold">Verifikasi sekarang</a>
                                        </p>
                                    </div>
                                @endif
                            @else
                                <div class="mb-6 p-3 md:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-blue-800 text-sm md:text-base">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Silakan <a href="{{ route('login') }}" class="underline font-semibold">login</a> untuk dapat berkomentar.
                                    </p>
                                </div>
                            @endauth

                            <!-- Comments List -->
                            <div id="comments-list" class="space-y-4">
                                @php
                                    $comments = \App\Models\PostComment::where('photo_id', $post->id)
                                        ->where('status', 'approved')
                                        ->with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                @endphp
                                
                                @forelse($comments as $comment)
                                    <div class="flex gap-2 md:gap-3 p-3 md:p-4 bg-gray-50 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm md:text-base">
                                                {{ strtoupper(substr($comment->user->name ?? 'A', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-1 md:gap-2 mb-1">
                                                <span class="font-semibold text-gray-900 text-sm md:text-base">{{ $comment->user->name ?? 'Anonymous' }}</span>
                                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm md:text-base">{{ $comment->body }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="far fa-comments text-4xl mb-2"></i>
                                        <p>Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Related Posts -->
            @if($relatedPosts && $relatedPosts->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4 md:mb-6">Berita Terkait</h2>
                <div class="space-y-3 md:space-y-4">
                    @foreach($relatedPosts as $relatedPost)
                    <div class="flex gap-3 md:space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-200 rounded-lg overflow-hidden">
                                <img src="{{ $relatedPost->image ?: '/images/default-berita.jpg' }}" 
                                     alt="{{ $relatedPost->judul }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 mb-1 md:mb-2 line-clamp-2 text-sm md:text-base">
                                <a href="{{ route('berita.show', $relatedPost) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $relatedPost->judul }}
                                </a>
                            </h3>
                            <p class="text-xs md:text-sm text-gray-500">
                                {{ $relatedPost->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif



            
@endsection

@section('styles')
<style>
.prose {
    line-height: 1.7;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #1f2937;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose p {
    margin-bottom: 1.5rem;
}

.prose ul, .prose ol {
    margin-bottom: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose img {
    border-radius: 0.5rem;
    margin: 1.5rem 0;
    max-width: 100%;
    height: auto;
}

.prose blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6b7280;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

a {
    color: #3b82f6;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Mobile-specific improvements */
@media (max-width: 768px) {
    .prose img {
        margin: 1rem 0;
    }
    
    .prose h1, .prose h2, .prose h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .prose p {
        margin-bottom: 1rem;
    }
    
    .prose ul, .prose ol {
        margin-bottom: 1rem;
        padding-left: 1.25rem;
    }
}
</style>

<script>
// Function to handle berita download
async function downloadBerita(button, url, postId) {
    const downloadBtn = button;
    const downloadCountEl = downloadBtn.querySelector('.download-count');
    let downloadCount = parseInt(downloadCountEl.textContent) || 0;
    
    try {
        // Show loading state
        const originalHtml = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengunduh...';
        downloadBtn.classList.add('opacity-75', 'cursor-not-allowed');
        
        // Create a temporary link and trigger download
        const a = document.createElement('a');
        a.href = url;
        a.download = '';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        // Update download count immediately
        downloadCount++;
        downloadCountEl.textContent = downloadCount;
        
        // Update all download count elements on the page
        document.querySelectorAll('.download-count').forEach(el => {
            el.textContent = downloadCount;
        });
        
        // Show success message
        showToast('Berita berhasil diunduh', 'success');
        
    } catch (error) {
        console.error('Download error:', error);
        showToast(error.message || 'Terjadi kesalahan saat mengunduh', 'error');
    } finally {
        // Restore button state
        downloadBtn.innerHTML = `
            <i class="fas fa-download text-lg"></i>
            <span class="font-medium">
                <span class="download-count">${downloadCount}</span> Unduh
            </span>`;
        downloadBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Fix image URLs with special characters
    function encodeImageUrls() {
        document.querySelectorAll('img[src]').forEach(img => {
            const originalSrc = img.getAttribute('src');
            if (originalSrc && originalSrc.includes('/images/')) {
                const lastSlashIndex = originalSrc.lastIndexOf('/');
                if (lastSlashIndex !== -1) {
                    const path = originalSrc.substring(0, lastSlashIndex + 1);
                    const filename = originalSrc.substring(lastSlashIndex + 1);
                    const encodedUrl = path + encodeURIComponent(filename);
                    img.src = encodedUrl;
                    console.log('Encoded berita image URL:', originalSrc, '->', encodedUrl);
                }
            }
        });
    }
    
    // Run URL encoding after page load
    encodeImageUrls();
    
    // Like Button Handler for show page
    const likeForm = document.querySelector('.like-form');
    if (likeForm) {
        likeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('.like-btn');
            const icon = btn.querySelector('.like-icon');
            const counter = btn.querySelector('.like-count');
            
            // Disable button during request
            btn.disabled = true;
            btn.style.opacity = '0.6';
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update UI based on server response
                    if (data.liked) {
                        // Liked state
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.classList.add('text-pink-500');
                        btn.classList.remove('text-gray-500');
                        btn.dataset.liked = 'true';
                    } else {
                        // Unliked state
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('text-pink-500');
                        btn.classList.add('text-gray-500');
                        btn.dataset.liked = 'false';
                    }
                    
                    // Update counter with server data
                    counter.textContent = data.total_likes;
                    
                    // Show success message briefly
                    if (data.message) {
                        showToast(data.message, 'success');
                    }
                } else {
                    // Handle error response
                    showToast(data.message || 'Gagal memproses like', 'error');
                    
                    // Redirect to login if unauthorized
                    if (response.status === 401) {
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 2000);
                    }
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                // Re-enable button
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });
    }
    
    // Toast notification function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => toast.classList.add('opacity-100'), 100);
        
        // Hide and remove toast
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }
});
</script>
@endsection