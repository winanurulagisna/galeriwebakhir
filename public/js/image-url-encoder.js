/**
 * Image URL Encoder Utility
 * Fixes image URLs with special characters (spaces, parentheses, etc.)
 * by encoding the filename part using encodeURIComponent()
 */

(function() {
    'use strict';
    
    /**
     * Encode image URLs to handle special characters in filenames
     */
    function encodeImageUrls() {
        // Fix all img src attributes
        document.querySelectorAll('img[src]').forEach(img => {
            const originalSrc = img.getAttribute('src');
            if (originalSrc && originalSrc.includes('/images/')) {
                const encodedUrl = encodeImageUrl(originalSrc);
                if (encodedUrl !== originalSrc) {
                    img.src = encodedUrl;
                    console.log('Encoded image URL:', originalSrc, '->', encodedUrl);
                }
            }
        });
        
        // Fix all lightbox/gallery links
        document.querySelectorAll('a[href*="/images/"]').forEach(link => {
            const originalHref = link.getAttribute('href');
            if (originalHref) {
                const encodedUrl = encodeImageUrl(originalHref);
                if (encodedUrl !== originalHref) {
                    link.href = encodedUrl;
                    console.log('Encoded link URL:', originalHref, '->', encodedUrl);
                }
            }
        });
        
        // Fix background images in style attributes
        document.querySelectorAll('[style*="background-image"]').forEach(element => {
            const style = element.getAttribute('style');
            if (style && style.includes('/images/')) {
                const newStyle = style.replace(/url\(['"]?([^'"]*\/images\/[^'"]*?)['"]?\)/g, (match, url) => {
                    const encodedUrl = encodeImageUrl(url);
                    return `url('${encodedUrl}')`;
                });
                if (newStyle !== style) {
                    element.setAttribute('style', newStyle);
                    console.log('Encoded background image URL in style');
                }
            }
        });
    }
    
    /**
     * Encode a single image URL
     * @param {string} url - The original URL
     * @returns {string} - The encoded URL
     */
    function encodeImageUrl(url) {
        if (!url || !url.includes('/images/')) {
            return url;
        }
        
        const lastSlashIndex = url.lastIndexOf('/');
        if (lastSlashIndex === -1) {
            return url;
        }
        
        const path = url.substring(0, lastSlashIndex + 1);
        const filename = url.substring(lastSlashIndex + 1);
        
        // Only encode if filename contains special characters
        if (/[^a-zA-Z0-9._-]/.test(filename)) {
            return path + encodeURIComponent(filename);
        }
        
        return url;
    }
    
    /**
     * Add error handling for images that fail to load
     */
    function addImageErrorHandling() {
        document.querySelectorAll('img:not([onerror])').forEach(img => {
            img.addEventListener('error', function() {
                console.warn('Image failed to load:', this.src);
                this.src = '/images/placeholder.svg';
            }, { once: true });
        });
    }
    
    /**
     * Initialize the image URL encoder
     */
    function init() {
        encodeImageUrls();
        addImageErrorHandling();
        
        // Re-run encoding when new images are added dynamically
        const observer = new MutationObserver(function(mutations) {
            let shouldReEncode = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.tagName === 'IMG' || node.querySelector('img')) {
                                shouldReEncode = true;
                            }
                        }
                    });
                }
            });
            
            if (shouldReEncode) {
                setTimeout(encodeImageUrls, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose utility functions globally
    window.ImageUrlEncoder = {
        encode: encodeImageUrl,
        encodeAll: encodeImageUrls,
        init: init
    };
})();
