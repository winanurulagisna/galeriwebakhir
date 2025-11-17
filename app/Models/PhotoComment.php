<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoComment extends Model
{
    use HasFactory;

    protected $table = 'photo_comments';

    protected $fillable = [
        'photo_id',
        'comment_type',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'body',
        'status',
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function user()
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

    public function scopeForPhotos($query)
    {
        return $query->where('comment_type', 'photo');
    }

    public function scopeForPosts($query)
    {
        return $query->where('comment_type', 'post');
    }
}
