<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * Konversi file HEIC ke JPEG jika diperlukan
     * 
     * @param string $filePath Path ke file asli
     * @param string $fileName Nama file
     * @return string Path ke file yang sudah dikonversi atau file asli
     */
    public static function processHeicImage($filePath, $fileName)
    {
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Jika bukan HEIC/HEIF, return path asli
        if (!in_array($fileExtension, ['heic', 'heif'])) {
            return $filePath;
        }
        
        try {
            // Cek apakah server mendukung konversi HEIC
            if (!extension_loaded('imagick')) {
                // Jika tidak ada ImageMagick, coba dengan GD atau return path asli
                return $filePath;
            }
            
            // Generate nama file baru dengan ekstensi .jpg
            $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.jpg';
            $newFilePath = dirname($filePath) . '/' . $newFileName;
            
            // Konversi HEIC ke JPEG menggunakan ImageMagick
            $imagick = new \Imagick($filePath);
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(85);
            $imagick->writeImage($newFilePath);
            $imagick->clear();
            $imagick->destroy();
            
            // Hapus file HEIC asli untuk menghemat space
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            return $newFilePath;
            
        } catch (\Exception $e) {
            // Jika konversi gagal, return path asli
            error_log("HEIC conversion failed: " . $e->getMessage());
            return $filePath;
        }
    }
    
    /**
     * Konversi HEIC ke JPEG (opsional, hanya jika diperlukan)
     * Sekarang tidak digunakan secara default karena HEIC ditampilkan langsung
     */
    public static function convertHeicToJpeg($heicPath, $jpegPath, $quality = 85)
    {
        if (!extension_loaded('imagick')) {
            return false;
        }
        
        try {
            $imagick = new \Imagick($heicPath);
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality($quality);
            $imagick->writeImage($jpegPath);
            $imagick->clear();
            $imagick->destroy();
            
            return true;
        } catch (\Exception $e) {
            error_log('HEIC conversion failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cek apakah browser mendukung HEIC
     * Diperluas untuk mendeteksi lebih banyak browser yang support HEIC
     */
    public static function browserSupportsHeic()
    {
        $userAgent = request()->header('User-Agent', '');
        
        // Safari 11+ dan iOS Safari mendukung HEIC
        if (preg_match('/Safari/i', $userAgent) && preg_match('/Version\/(\d+)/i', $userAgent, $matches)) {
            $version = intval($matches[1]);
            return $version >= 11;
        }
        
        // iOS Safari (tanpa version string)
        if (preg_match('/iPhone|iPad/i', $userAgent) && preg_match('/Safari/i', $userAgent)) {
            return true;
        }
        
        // macOS Safari
        if (preg_match('/Macintosh/i', $userAgent) && preg_match('/Safari/i', $userAgent)) {
            return true;
        }
        
        // Chrome 116+ mulai support HEIC (experimental)
        if (preg_match('/Chrome\/(\d+)/i', $userAgent, $matches)) {
            $version = intval($matches[1]);
            return $version >= 116;
        }
        
        // Edge 116+ (berbasis Chromium)
        if (preg_match('/Edg\/(\d+)/i', $userAgent, $matches)) {
            $version = intval($matches[1]);
            return $version >= 116;
        }
        
        // Default: assume tidak support untuk browser lain
        return false;
    }
    
    /**
     * Mendapatkan URL foto yang optimal berdasarkan format dan dukungan browser
     * HEIC akan ditampilkan langsung tanpa konversi
     */
    public static function getOptimalPhotoUrl($filePath)
    {
        if (empty($filePath)) {
            return '/images/placeholder.svg';
        }
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Jika bukan HEIC/HEIF, return path asli
        if (!in_array($extension, ['heic', 'heif'])) {
            return $filePath;
        }
        
        // Untuk HEIC/HEIF, return path asli (tampilkan langsung)
        // Browser modern akan menangani HEIC, browser lama akan fallback ke onerror
        return $filePath;
    }
    
    /**
     * Buat thumbnail untuk foto
     * 
     * @param string $sourcePath Path ke foto asli
     * @param string $thumbnailPath Path untuk thumbnail
     * @param int $width Lebar thumbnail
     * @param int $height Tinggi thumbnail
     * @return bool Success status
     */
    public static function createThumbnail($sourcePath, $thumbnailPath, $width = 300, $height = 300)
    {
        try {
            if (!file_exists($sourcePath)) {
                return false;
            }
            
            // Gunakan Intervention Image jika tersedia
            if (class_exists('Intervention\Image\Facades\Image')) {
                $image = Image::make($sourcePath);
                $image->fit($width, $height, function ($constraint) {
                    $constraint->upsize();
                });
                $image->save($thumbnailPath, 80);
                return true;
            }
            
            // Fallback menggunakan GD
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                return false;
            }
            
            $sourceWidth = $imageInfo[0];
            $sourceHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Buat resource image berdasarkan tipe
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                default:
                    return false;
            }
            
            if (!$sourceImage) {
                return false;
            }
            
            // Hitung dimensi thumbnail dengan mempertahankan aspect ratio
            $ratio = min($width / $sourceWidth, $height / $sourceHeight);
            $newWidth = intval($sourceWidth * $ratio);
            $newHeight = intval($sourceHeight * $ratio);
            
            // Buat thumbnail
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
            
            // Simpan thumbnail
            imagejpeg($thumbnail, $thumbnailPath, 80);
            
            // Bersihkan memory
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);
            
            return true;
            
        } catch (\Exception $e) {
            error_log("Thumbnail creation failed: " . $e->getMessage());
            return false;
        }
    }
}
