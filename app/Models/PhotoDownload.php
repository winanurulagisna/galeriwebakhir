<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoDownload extends Model
{
    protected $table = 'photo_downloads';
    
    protected $fillable = [
        'photo_id',
        'user_id',
        'session_id',
        'ip',
        'user_agent',
        'downloaded_at'
    ];

    public $timestamps = true;
    
    protected $dates = [
        'downloaded_at',
        'created_at',
        'updated_at'
    ];
    
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
