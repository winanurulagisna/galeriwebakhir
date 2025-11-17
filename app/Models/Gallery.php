<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';
    
    protected $fillable = [
        'title',
        'description',
        'post_id',
        'position',
        'status',
        'category',
    ];


    /**
     * Scope for active galleries
     */
    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Enable timestamps (columns exist in migration)
    public $timestamps = true;

    // Relationship with Post
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    // Relationship with Photo (multi upload support)
    public function photos()
    {
        return $this->hasMany(Photo::class, 'gallery_id');
    }
}
