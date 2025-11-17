<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'users';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp_code',
        'otp_expires_at',
        'username',
        'gender',
        'phone',
    ];
    public $timestamps = true;
    protected $primaryKey = 'id'; // menggunakan id default Laravel
    public $incrementing = true; // memastikan auto-increment

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Get the user's photo likes.
     */
    public function photoLikes()
    {
        return $this->hasMany(PhotoLike::class);
    }

    /**
     * Get the user's photo comments.
     */
    public function photoComments()
    {
        return $this->hasMany(PhotoComment::class);
    }

    /**
     * Get the user's photo downloads.
     */
    public function photoDownloads()
    {
        return $this->hasMany(PhotoDownload::class);
    }

    /**
     * Get photos that user has liked (Many-to-Many through photo_likes)
     */
    public function likedPhotos()
    {
        return $this->belongsToMany(Photo::class, 'photo_likes')
            ->withTimestamps()
            ->orderBy('photo_likes.created_at', 'desc');
    }

    /**
     * Get photos that user has commented on (Many-to-Many through photo_comments)
     */
    public function commentedPhotos()
    {
        return $this->belongsToMany(Photo::class, 'photo_comments')
            ->distinct()
            ->withTimestamps()
            ->orderBy('photo_comments.created_at', 'desc');
    }

    /**
     * Get photos that user has downloaded (Many-to-Many through photo_downloads)
     */
    public function downloadedPhotos()
    {
        return $this->belongsToMany(Photo::class, 'photo_downloads')
            ->withTimestamps()
            ->orderBy('photo_downloads.created_at', 'desc');
    }
}
