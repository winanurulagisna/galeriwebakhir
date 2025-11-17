<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostComment extends Model
{
    use HasFactory;

    // Gunakan tabel photo_comments yang sudah ada
    protected $table = 'photo_comments';

    protected $fillable = [
        'photo_id', // Ini akan digunakan untuk menyimpan post_id
        'comment_type',
        'user_id',
        'body',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'photo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
