<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostLike extends Model
{
    use HasFactory;

    // Gunakan tabel photo_likes yang sudah ada
    protected $table = 'photo_likes';

    protected $fillable = [
        'photo_id', // Ini akan digunakan untuk menyimpan post_id
        'user_id',
        'session_id',
        'ip',
        'user_agent',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'photo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
