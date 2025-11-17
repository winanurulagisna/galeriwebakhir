# 📚 Dokumentasi Sistem Like, Comment & Download
## Web Galeri Sekolah SMKN 4 Bogor

---

## 🎯 Overview

Sistem ini memungkinkan user untuk:
- ❤️ **Like** foto galeri
- 💬 **Comment** pada foto
- 📥 **Download** foto

Semua aktivitas tersimpan dan bisa ditampilkan di **profil user**.

---

## 📊 Struktur Database

### 1. Tabel `users`
```sql
- id (PK)
- name
- email
- password
- username
- gender
- phone
- email_verified_at
- created_at
- updated_at
```

### 2. Tabel `photos`
```sql
- id (PK)
- gallery_id (FK -> galleries)
- file_path
- caption
- related_type (enum: 'photo', 'acara', 'berita')
- related_id
- created_at
- updated_at
```

### 3. Tabel `photo_likes` (Pivot Table)
```sql
- id (PK)
- photo_id (FK -> photos)
- user_id (FK -> users, nullable)
- session_id (untuk guest)
- ip
- user_agent
- created_at
- updated_at
```

### 4. Tabel `photo_comments`
```sql
- id (PK)
- photo_id (FK -> photos)
- comment_type (enum: 'photo', 'post')
- user_id (FK -> users, nullable)
- first_name (untuk guest)
- last_name (untuk guest)
- email (untuk guest)
- body (text komentar)
- status (enum: 'pending', 'approved', 'rejected')
- created_at
- updated_at
```

### 5. Tabel `photo_downloads`
```sql
- id (PK)
- photo_id (FK -> photos)
- user_id (FK -> users, nullable)
- session_id (untuk guest)
- ip
- user_agent
- downloaded_at
- created_at
- updated_at
```

---

## 🔗 Relasi Eloquent Model

### Model `User`
```php
class User extends Authenticatable
{
    // Relasi One-to-Many
    public function photoLikes()
    {
        return $this->hasMany(PhotoLike::class);
    }

    public function photoComments()
    {
        return $this->hasMany(PhotoComment::class);
    }

    public function photoDownloads()
    {
        return $this->hasMany(PhotoDownload::class);
    }
    
    // Helper Methods untuk Profil
    public function likedPhotos()
    {
        return $this->belongsToMany(Photo::class, 'photo_likes')
            ->withTimestamps()
            ->orderBy('photo_likes.created_at', 'desc');
    }
    
    public function commentedPhotos()
    {
        return $this->belongsToMany(Photo::class, 'photo_comments')
            ->withTimestamps()
            ->orderBy('photo_comments.created_at', 'desc');
    }
    
    public function downloadedPhotos()
    {
        return $this->belongsToMany(Photo::class, 'photo_downloads')
            ->withTimestamps()
            ->orderBy('photo_downloads.created_at', 'desc');
    }
}
```

### Model `Photo`
```php
class Photo extends Model
{
    // Relasi One-to-Many
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
    
    // Relasi Many-to-One
    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id');
    }
    
    // Helper Methods
    public function isLikedBy($userId = null)
    {
        if (!$userId && auth()->check()) {
            $userId = auth()->id();
        }
        
        return $this->likes()
            ->where('user_id', $userId)
            ->exists();
    }
    
    public function likesCount()
    {
        return $this->likes()->count();
    }
    
    public function commentsCount()
    {
        return $this->comments()->where('status', 'approved')->count();
    }
    
    public function downloadsCount()
    {
        return $this->downloads()->count();
    }
}
```

### Model `PhotoLike`
```php
class PhotoLike extends Model
{
    protected $fillable = [
        'photo_id',
        'user_id',
        'session_id',
        'ip',
        'user_agent',
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
```

### Model `PhotoComment`
```php
class PhotoComment extends Model
{
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
    
    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
```

### Model `PhotoDownload`
```php
class PhotoDownload extends Model
{
    protected $fillable = [
        'photo_id',
        'user_id',
        'session_id',
        'ip',
        'user_agent',
        'downloaded_at'
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
```

---

## 🔄 Alur Kerja Sistem

### 1. 👍 LIKE FOTO

#### Flow:
```
User klik tombol Like
    ↓
Check: User sudah login?
    ↓ Yes                    ↓ No
Simpan dengan user_id    Simpan dengan session_id
    ↓
Insert/Delete ke photo_likes
    ↓
Update counter di UI (AJAX)
```

#### Controller Example:
```php
public function toggleLike(Photo $photo)
{
    $userId = auth()->id();
    $sessionId = session()->getId();
    
    // Cek apakah sudah like
    $like = PhotoLike::where('photo_id', $photo->id)
        ->where(function($q) use ($userId, $sessionId) {
            if ($userId) {
                $q->where('user_id', $userId);
            } else {
                $q->where('session_id', $sessionId);
            }
        })
        ->first();
    
    if ($like) {
        // Unlike
        $like->delete();
        $liked = false;
    } else {
        // Like
        PhotoLike::create([
            'photo_id' => $photo->id,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        $liked = true;
    }
    
    return response()->json([
        'success' => true,
        'liked' => $liked,
        'likes_count' => $photo->likes()->count()
    ]);
}
```

---

### 2. 💬 COMMENT FOTO

#### Flow:
```
User submit form komentar
    ↓
Validasi input
    ↓
Check: User sudah login?
    ↓ Yes                           ↓ No
Simpan dengan user_id          Simpan dengan email/nama
    ↓
Status = 'pending' (moderasi)
    ↓
Admin approve/reject
    ↓
Tampilkan di halaman foto (jika approved)
```

#### Controller Example:
```php
public function storeComment(Request $request, Photo $photo)
{
    $validated = $request->validate([
        'body' => 'required|string|max:1000',
        'first_name' => 'required_without:user_id|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'email' => 'required_without:user_id|email',
    ]);
    
    $comment = PhotoComment::create([
        'photo_id' => $photo->id,
        'comment_type' => 'photo',
        'user_id' => auth()->id(),
        'first_name' => $validated['first_name'] ?? auth()->user()->name,
        'last_name' => $validated['last_name'] ?? null,
        'email' => $validated['email'] ?? auth()->user()->email,
        'body' => $validated['body'],
        'status' => 'pending', // Perlu moderasi admin
    ]);
    
    return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu persetujuan admin.');
}
```

---

### 3. 📥 DOWNLOAD FOTO

#### Flow:
```
User klik tombol Download
    ↓
Increment download counter
    ↓
Check: User sudah login?
    ↓ Yes                    ↓ No
Simpan dengan user_id    Simpan dengan session_id
    ↓
Insert ke photo_downloads
    ↓
Return file download
```

#### Controller Example:
```php
public function downloadPhoto(Photo $photo)
{
    // Increment counter
    $photo->increment('downloads_count');
    
    // Track download
    PhotoDownload::updateOrCreate(
        [
            'photo_id' => $photo->id,
            'user_id' => auth()->id(),
        ],
        [
            'session_id' => session()->getId(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now(),
        ]
    );
    
    // Download file
    $filePath = public_path($photo->file_path);
    $filename = 'photo-' . $photo->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
    
    return response()->download($filePath, $filename);
}
```

---

## 👤 Menampilkan di Profil User

### Controller: `ProfileController.php`
```php
public function show()
{
    $user = auth()->user();
    
    // Foto yang disukai (dengan eager loading)
    $likedPhotos = $user->photoLikes()
        ->with(['photo.gallery'])
        ->latest()
        ->paginate(12, ['*'], 'likes_page');
    
    // Foto yang dikomentari (distinct, karena bisa komen berkali-kali)
    $commentedPhotos = $user->photoComments()
        ->with(['photo.gallery'])
        ->select('photo_id')
        ->distinct()
        ->latest()
        ->paginate(12, ['*'], 'comments_page');
    
    // Foto yang diunduh
    $downloadedPhotos = $user->photoDownloads()
        ->with(['photo.gallery'])
        ->latest()
        ->paginate(12, ['*'], 'downloads_page');
    
    // Statistik
    $stats = [
        'total_likes' => $user->photoLikes()->count(),
        'total_comments' => $user->photoComments()->count(),
        'total_downloads' => $user->photoDownloads()->count(),
    ];
    
    return view('profile.show', compact(
        'user',
        'likedPhotos',
        'commentedPhotos',
        'downloadedPhotos',
        'stats'
    ));
}
```

### View: `profile/show.blade.php`
```blade
<div class="profile-container">
    <h1>Profil {{ $user->name }}</h1>
    
    <!-- Statistik -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-heart"></i>
            <span>{{ $stats['total_likes'] }}</span>
            <p>Foto Disukai</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-comment"></i>
            <span>{{ $stats['total_comments'] }}</span>
            <p>Komentar</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-download"></i>
            <span>{{ $stats['total_downloads'] }}</span>
            <p>Unduhan</p>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="tabs">
        <button class="tab active" data-tab="likes">Foto Disukai</button>
        <button class="tab" data-tab="comments">Foto Dikomentari</button>
        <button class="tab" data-tab="downloads">Foto Diunduh</button>
    </div>
    
    <!-- Foto Disukai -->
    <div class="tab-content active" id="likes">
        <div class="photo-grid">
            @foreach($likedPhotos as $like)
                <div class="photo-card">
                    <img src="{{ $like->photo->safe_file_url }}" alt="{{ $like->photo->caption }}">
                    <div class="photo-info">
                        <p>{{ $like->photo->caption }}</p>
                        <small>{{ $like->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $likedPhotos->links() }}
    </div>
    
    <!-- Foto Dikomentari -->
    <div class="tab-content" id="comments">
        <div class="photo-grid">
            @foreach($commentedPhotos as $comment)
                <div class="photo-card">
                    <img src="{{ $comment->photo->safe_file_url }}" alt="{{ $comment->photo->caption }}">
                    <div class="photo-info">
                        <p>{{ $comment->photo->caption }}</p>
                        <small>{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $commentedPhotos->links() }}
    </div>
    
    <!-- Foto Diunduh -->
    <div class="tab-content" id="downloads">
        <div class="photo-grid">
            @foreach($downloadedPhotos as $download)
                <div class="photo-card">
                    <img src="{{ $download->photo->safe_file_url }}" alt="{{ $download->photo->caption }}">
                    <div class="photo-info">
                        <p>{{ $download->photo->caption }}</p>
                        <small>{{ $download->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $downloadedPhotos->links() }}
    </div>
</div>
```

---

## 🔐 Keamanan & Best Practices

### 1. **Rate Limiting**
```php
// routes/web.php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/photos/{photo}/like', [PhotoController::class, 'toggleLike']);
    Route::post('/photos/{photo}/comment', [PhotoController::class, 'storeComment']);
});
```

### 2. **Validasi Input**
```php
$request->validate([
    'body' => 'required|string|max:1000|profanity_filter',
    'email' => 'required|email|max:255',
]);
```

### 3. **Moderasi Komentar**
- Semua komentar status = `'pending'` by default
- Admin harus approve sebelum tampil di public
- Gunakan queue untuk notifikasi admin

### 4. **Prevent Spam**
```php
// Cek apakah user sudah komen dalam 1 menit terakhir
$recentComment = PhotoComment::where('user_id', auth()->id())
    ->where('created_at', '>', now()->subMinute())
    ->exists();

if ($recentComment) {
    return back()->with('error', 'Tunggu sebentar sebelum komentar lagi.');
}
```

---

## 📈 Query Optimization

### 1. **Eager Loading**
```php
// ❌ N+1 Problem
$likes = $user->photoLikes;
foreach ($likes as $like) {
    echo $like->photo->caption; // Query untuk setiap foto
}

// ✅ Eager Loading
$likes = $user->photoLikes()->with('photo.gallery')->get();
foreach ($likes as $like) {
    echo $like->photo->caption; // Hanya 1 query
}
```

### 2. **Indexing Database**
```sql
-- Migration
Schema::table('photo_likes', function (Blueprint $table) {
    $table->index(['photo_id', 'user_id']);
    $table->index('session_id');
});

Schema::table('photo_comments', function (Blueprint $table) {
    $table->index(['photo_id', 'status']);
    $table->index('user_id');
});

Schema::table('photo_downloads', function (Blueprint $table) {
    $table->index(['photo_id', 'user_id']);
});
```

### 3. **Caching**
```php
// Cache likes count
$likesCount = Cache::remember("photo.{$photo->id}.likes", 60, function () use ($photo) {
    return $photo->likes()->count();
});
```

---

## 🎨 Frontend (AJAX Like Button)

```javascript
// public/js/photo-interactions.js
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        const photoId = this.dataset.photoId;
        const icon = this.querySelector('i');
        const counter = this.querySelector('.like-count');
        
        try {
            const response = await fetch(`/photos/${photoId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Toggle icon
                if (data.liked) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                }
                
                // Update counter
                counter.textContent = data.likes_count;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
});
```

---

## 📝 Summary

### Relasi Utama:
```
User ←→ PhotoLike ←→ Photo
User ←→ PhotoComment ←→ Photo
User ←→ PhotoDownload ←→ Photo
```

### Key Points:
1. ✅ Semua interaksi (like, comment, download) tersimpan di tabel pivot
2. ✅ Support untuk **guest users** (via session_id)
3. ✅ Support untuk **registered users** (via user_id)
4. ✅ Komentar perlu **moderasi admin** (status: pending/approved)
5. ✅ Data bisa ditampilkan di **profil user**
6. ✅ Menggunakan **Eloquent relationships** untuk query efisien

---

**Dibuat untuk:** Web Galeri Sekolah SMKN 4 Bogor  
**Tanggal:** 10 November 2025  
**Framework:** Laravel 12
