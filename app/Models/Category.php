<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kategori_new';
    
    protected $fillable = [
        'judul'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Timestamps are now enabled after migration
    public $timestamps = true;

    // Relationship: a category has many posts
    public function posts()
    {
        return $this->hasMany(Post::class, 'kategori_id');
    }
}
