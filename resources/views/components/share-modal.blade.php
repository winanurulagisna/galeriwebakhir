<!-- Enhanced Share Modal -->
<div id="shareModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[9999]" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all" id="shareModalContent">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Bagikan Foto</h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Photo Preview -->
        <div class="px-6 py-4">
            <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden mb-4">
                <img id="sharePhotoPreview" src="" alt="Preview" class="w-full h-full object-cover">
            </div>
            <p id="sharePhotoTitle" class="text-sm font-semibold text-gray-900 mb-1"></p>
            <p id="sharePhotoUrl" class="text-xs text-gray-500 truncate"></p>
        </div>

        <!-- Share Options -->
        <div class="px-6 py-4 border-t border-gray-200">
            <p class="text-sm font-semibold text-gray-700 mb-3">Bagikan ke:</p>
            <div class="grid grid-cols-4 gap-3">
                <!-- WhatsApp -->
                <button onclick="shareToWhatsApp()" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-green-50 transition-all group">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">WhatsApp</span>
                </button>

                <!-- Facebook -->
                <button onclick="shareToFacebook()" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-blue-50 transition-all group">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Facebook</span>
                </button>

                <!-- Twitter -->
                <button onclick="shareToTwitter()" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-sky-50 transition-all group">
                    <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Twitter</span>
                </button>

                <!-- Copy Link -->
                <button onclick="copyShareLink()" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-gray-100 transition-all group">
                    <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Copy Link</span>
                </button>
            </div>
        </div>

    </div>
</div>

<style>
#shareModal.show {
    display: flex !important;
    animation: fadeIn 0.2s ease-out;
}

#shareModal.show #shareModalContent {
    animation: slideUp 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>

<script>
let currentShareData = {
    url: '',
    title: '',
    photoUrl: ''
};

function openShareModal(photoUrl, photoTitle, galleryUrl) {
    const modal = document.getElementById('shareModal');
    const shareUrl = window.location.origin + galleryUrl;
    
    currentShareData = {
        url: shareUrl,
        title: photoTitle,
        photoUrl: photoUrl
    };
    
    // Update modal content
    document.getElementById('sharePhotoPreview').src = photoUrl;
    document.getElementById('sharePhotoTitle').textContent = photoTitle;
    document.getElementById('sharePhotoUrl').textContent = shareUrl;
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    
    // Reset QR code
    const qrContainer = document.getElementById('qrCodeContainer');
    if (qrContainer) {
        qrContainer.classList.add('hidden');
    }
}

function shareToWhatsApp() {
    const text = encodeURIComponent(`${currentShareData.title}\n${currentShareData.url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareToFacebook() {
    const url = encodeURIComponent(currentShareData.url);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareToTwitter() {
    const text = encodeURIComponent(currentShareData.title);
    const url = encodeURIComponent(currentShareData.url);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
}

function copyShareLink() {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(currentShareData.url).then(() => {
            showToast('✓ Link berhasil disalin!');
        }).catch(err => {
            fallbackCopyText(currentShareData.url);
        });
    } else {
        fallbackCopyText(currentShareData.url);
    }
}

function fallbackCopyText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showToast('✓ Link berhasil disalin!');
    } catch (err) {
        showToast('✗ Gagal menyalin link');
    }
    document.body.removeChild(textArea);
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        z-index: 99999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideUp 0.3s ease;
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 2000);
}

// Close modal when clicking outside
document.getElementById('shareModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeShareModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('shareModal')?.classList.contains('show')) {
        closeShareModal();
    }
});
</script>
