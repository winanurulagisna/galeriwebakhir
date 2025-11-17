<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageHelper;

class Photo extends Model
{
    use HasFactory;

    protected $table = 'photos';

    protected $fillable = [
        'gallery_id',
        'file_path',
        'caption',
        'related_type',
        'related_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id');
    }

    public function likes()
    {
        return $this->hasMany(PhotoLike::class, 'photo_id');
    }

    public function comments()
    {
        return $this->hasMany(PhotoComment::class, 'photo_id');
    }

    public function downloads()
    {
        return $this->hasMany(PhotoDownload::class, 'photo_id');
    }

    /**
     * Check if photo is liked by specific user
     */
    public function isLikedBy($userId = null)
    {
        if (!$userId && auth()->check()) {
            $userId = auth()->id();
        }
        
        if (!$userId) {
            return false;
        }
        
        return $this->likes()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get total likes count
     */
    public function likesCount()
    {
        return $this->likes()->count();
    }

    /**
     * Get total approved comments count
     */
    public function commentsCount()
    {
        return $this->comments()->where('status', 'approved')->count();
    }

    /**
     * Get total downloads count
     */
    public function downloadsCount()
    {
        return $this->downloads()->count();
    }

    /**
     * Accessor untuk mendapatkan URL foto yang optimal
     * Menangani konversi HEIC dan fallback untuk browser yang tidak mendukung
     */
    public function getOptimalUrlAttribute()
    {
        $optimalUrl = ImageHelper::getOptimalPhotoUrl($this->file_path);
        return \App\Helpers\ImageUrlHelper::encodeImageUrl($optimalUrl);
    }

    /**
     * Accessor untuk mendapatkan URL file yang sudah di-encode
     */
    public function getSafeFileUrlAttribute()
    {
        return \App\Helpers\ImageUrlHelper::getSafeImageUrl($this->file_path);
    }

    /**
     * Accessor untuk cek apakah foto adalah format HEIC/HEIF
     */
    public function getIsHeicAttribute()
    {
        if (empty($this->file_path)) {
            return false;
        }
        
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($extension, ['heic', 'heif']);
    }

    /**
     * Accessor untuk mendapatkan URL thumbnail
     */
    public function getThumbnailUrlAttribute()
    {
        if (empty($this->file_path)) {
            return '/images/placeholder.svg';
        }
        
        // Generate path thumbnail
        $pathInfo = pathinfo($this->file_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbs/' . $pathInfo['filename'] . '_thumb.jpg';
        $fullThumbnailPath = public_path($thumbnailPath);
        
        // Jika thumbnail sudah ada, return URL-nya
        if (file_exists($fullThumbnailPath)) {
            return $thumbnailPath;
        }
        
        // Jika belum ada, coba buat thumbnail
        $sourcePath = public_path($this->optimal_url);
        if (file_exists($sourcePath)) {
            // Pastikan folder thumbs ada
            $thumbDir = dirname($fullThumbnailPath);
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            
            // Buat thumbnail
            if (ImageHelper::createThumbnail($sourcePath, $fullThumbnailPath, 300, 300)) {
                return $thumbnailPath;
            }
        }
        
        // Fallback ke foto asli atau placeholder
        return $this->optimal_url ?: '/images/placeholder.svg';
    }

    /**
     * Accessor untuk backward compatibility
     * Agar kode lama yang menggunakan $photo->file tetap berfungsi
     */
    public function getFileAttribute()
    {
        return $this->file_path;
    }

    /**
     * Boot method untuk menambahkan event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika foto dihapus, hapus juga semua likes, comments, dan downloads terkait
        static::deleting(function ($photo) {
            // Hapus likes
            DB::table('photo_likes')->where('photo_id', $photo->id)->delete();
            
            // Hapus comments dari database
            DB::table('photo_comments')->where('photo_id', $photo->id)->delete();
            
            // Hapus downloads
            DB::table('photo_downloads')->where('photo_id', $photo->id)->delete();
            
            // Hapus comments dari JSON file
            try {
                $path = storage_path('app/komentar_temp.json');
                if (File::exists($path)) {
                    $raw = File::get($path);
                    $comments = json_decode($raw, true) ?: [];
                    
                    // Filter out comments untuk foto yang dihapus
                    $filteredComments = array_filter($comments, function($comment) use ($photo) {
                        return ($comment['photo_id'] ?? null) != $photo->id;
                    });
                    
                    // Simpan kembali ke file
                    File::put($path, json_encode(array_values($filteredComments), JSON_PRETTY_PRINT));
                }
            } catch (\Exception $e) {
                // Ignore JSON errors
            }
        });
    }

    /**
     * Method untuk membersihkan data orphan (likes, comments, downloads untuk foto yang sudah tidak ada)
     */
    public static function cleanOrphanData()
    {
        try {
            // Hapus likes untuk foto yang sudah tidak ada
            DB::statement('
                DELETE FROM photo_likes 
                WHERE photo_id NOT IN (SELECT id FROM photos)
            ');
            
            // Hapus comments untuk foto yang sudah tidak ada
            DB::statement('
                DELETE FROM photo_comments 
                WHERE photo_id NOT IN (SELECT id FROM photos)
            ');
            
            // Hapus downloads untuk foto yang sudah tidak ada
            DB::statement('
                DELETE FROM photo_downloads 
                WHERE photo_id NOT IN (SELECT id FROM photos)
            ');
            
            // Bersihkan JSON comments
            $path = storage_path('app/komentar_temp.json');
            if (File::exists($path)) {
                $raw = File::get($path);
                $comments = json_decode($raw, true) ?: [];
                
                $validPhotoIds = DB::table('photos')->pluck('id')->toArray();
                
                $filteredComments = array_filter($comments, function($comment) use ($validPhotoIds) {
                    return in_array($comment['photo_id'] ?? null, $validPhotoIds);
                });
                
                File::put($path, json_encode(array_values($filteredComments), JSON_PRETTY_PRINT));
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}


