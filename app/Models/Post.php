<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts_new';
    
    protected $fillable = [
        'judul',
        'isi',
        'url',
        'kategori_id',
        'petugas_id',
        'status',
        'views'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    // Relationship with Petugas
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    // Relationship dengan photos menggunakan related_id
    public function photos()
    {
        return $this->hasMany(Photo::class, 'related_id')
            ->where('related_type', 'berita')
            ->orderBy('created_at', 'desc');
    }

    // Get first photo for featured image
    public function getImageAttribute()
    {
        // Ambil foto pertama dari berita ini
        $firstPhoto = $this->photos()->first();
        return $firstPhoto ? $firstPhoto->file_path : '/images/default-news.jpg';
    }

    // Relationship with Likes
    public function likes()
    {
        return $this->hasMany(PostLike::class, 'photo_id');
    }

    // Relationship with Comments
    public function comments()
    {
        return $this->hasMany(PostComment::class, 'photo_id');
    }
}
