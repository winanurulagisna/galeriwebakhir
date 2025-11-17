<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleHeicImages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Jika response adalah JSON dan mengandung data foto
        if ($response->headers->get('Content-Type') === 'application/json') {
            $content = $response->getContent();
            $data = json_decode($content, true);
            
            if (is_array($data) && $this->containsPhotoData($data)) {
                $data = $this->processHeicInData($data);
                $response->setContent(json_encode($data));
            }
        }
        
        return $response;
    }
    
    /**
     * Cek apakah data mengandung informasi foto
     */
    private function containsPhotoData($data): bool
    {
        if (isset($data['photos']) || isset($data['data']['photos'])) {
            return true;
        }
        
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $item) {
                if (isset($item['file']) || isset($item['photo'])) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Proses data HEIC dalam response
     */
    private function processHeicInData($data): array
    {
        if (isset($data['photos'])) {
            $data['photos'] = $this->processPhotosArray($data['photos']);
        }
        
        if (isset($data['data']['photos'])) {
            $data['data']['photos'] = $this->processPhotosArray($data['data']['photos']);
        }
        
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $key => $item) {
                if (isset($item['file'])) {
                    $data['data'][$key] = $this->processPhotoItem($item);
                } elseif (isset($item['photo'])) {
                    $data['data'][$key]['photo'] = $this->processPhotoItem($item['photo']);
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Proses array foto
     */
    private function processPhotosArray($photos): array
    {
        foreach ($photos as $key => $photo) {
            if (isset($photo['file'])) {
                $photos[$key] = $this->processPhotoItem($photo);
            } elseif (isset($photo['photo'])) {
                $photos[$key]['photo'] = $this->processPhotoItem($photo['photo']);
            }
        }
        
        return $photos;
    }
    
    /**
     * Proses item foto individual
     */
    private function processPhotoItem($photo): array
    {
        if (!isset($photo['file'])) {
            return $photo;
        }
        
        $fileExtension = strtolower(pathinfo($photo['file'], PATHINFO_EXTENSION));
        
        // Jika bukan HEIC/HEIF, return as is
        if (!in_array($fileExtension, ['heic', 'heif'])) {
            $photo['optimal_url'] = $photo['file'];
            $photo['is_heic'] = false;
            return $photo;
        }
        
        // Tandai sebagai HEIC
        $photo['is_heic'] = true;
        
        // Cek apakah ada versi JPEG
        $jpegPath = preg_replace('/\.(heic|heif)$/i', '.jpg', $photo['file']);
        $fullJpegPath = public_path($jpegPath);
        
        if (file_exists($fullJpegPath)) {
            $photo['optimal_url'] = $jpegPath;
        } else {
            // Cek dukungan browser untuk HEIC
            $userAgent = request()->header('User-Agent', '');
            $browserSupportsHeic = $this->browserSupportsHeic($userAgent);
            
            if ($browserSupportsHeic) {
                $photo['optimal_url'] = $photo['file'];
            } else {
                $photo['optimal_url'] = '/images/placeholder.svg';
            }
        }
        
        return $photo;
    }
    
    /**
     * Cek dukungan browser untuk HEIC
     */
    private function browserSupportsHeic($userAgent): bool
    {
        // Safari 11+ dan iOS Safari mendukung HEIC
        if (preg_match('/Safari/i', $userAgent) && preg_match('/Version\/(\d+)/i', $userAgent, $matches)) {
            $version = intval($matches[1]);
            return $version >= 11;
        }
        
        // Chrome dan Firefox belum mendukung HEIC secara native
        return false;
    }
}
