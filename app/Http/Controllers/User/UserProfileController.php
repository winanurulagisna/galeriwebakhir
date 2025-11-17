<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get statistics
        // Likes are tracked by user_id for logged in users
        // Hitung likes unik (tanpa duplikat berita)
        $allLikes = \App\Models\PhotoLike::where('user_id', $user->id)->get();
        $seenItems = [];
        $likesCount = $allLikes->filter(function($like) use (&$seenItems) {
            $photo = \App\Models\Photo::find($like->photo_id);
            
            // If not a photo, it's a post (berita)
            if (!$photo) {
                $uniqueKey = 'berita_' . $like->photo_id;
            } else {
                // For berita photos, use berita ID as unique key
                if (($photo->related_type ?? null) === 'berita' && $photo->related_id) {
                    $uniqueKey = 'berita_' . $photo->related_id;
                } else {
                    $uniqueKey = 'photo_' . $photo->id;
                }
            }
            
            if (isset($seenItems[$uniqueKey])) {
                return false; // Skip duplicate
            }
            $seenItems[$uniqueKey] = true;
            return true;
        })->count();
            
        // Comments are tracked in both database and JSON file
        $commentsCount = 0;
        
        // Count from database - komentar untuk foto yang masih ada (tanpa filter gallery status)
        try {
            if (\Schema::hasTable('photo_comments')) {
                // Count photo comments - semua foto yang masih ada
                $photoCommentsCount = \DB::table('photo_comments')
                    ->join('photos', 'photo_comments.photo_id', '=', 'photos.id')
                    ->where('photo_comments.user_id', $user->id)
                    ->where('photo_comments.comment_type', 'photo') // Only photo comments
                    ->where('photo_comments.status', 'approved')
                    ->count();
                    
                // Count komentar untuk berita (PostComment menggunakan photo_comments table dengan photo_id = post_id)
                $postCommentsCount = \DB::table('photo_comments')
                    ->join('posts_new', 'photo_comments.photo_id', '=', 'posts_new.id')
                    ->where('photo_comments.user_id', $user->id)
                    ->where('photo_comments.comment_type', 'post') // Only post comments
                    ->where('photo_comments.status', 'approved')
                    ->where('posts_new.status', 'published')
                    ->count();
                    
                $commentsCount += $photoCommentsCount + $postCommentsCount;
            }
        } catch (\Exception $e) {
            // Ignore database errors
        }
        
        // Count from JSON file (approved comments) - hanya untuk foto yang masih ada
        try {
            $path = storage_path('app/komentar_temp.json');
            if (File::exists($path)) {
                $raw = File::get($path);
                $comments = json_decode($raw, true) ?: [];
                $jsonCommentsCount = collect($comments)->filter(function($comment) use ($user) {
                    if (!isset($comment['user_id']) || 
                        $comment['user_id'] != $user->id || 
                        ($comment['status'] ?? '') !== 'Disetujui') {
                        return false;
                    }
                    
                    // Pastikan foto masih ada (tanpa filter gallery status)
                    $photoExists = \DB::table('photos')
                        ->where('photos.id', $comment['photo_id'])
                        ->exists();
                    
                    return $photoExists;
                })->count();
                $commentsCount += $jsonCommentsCount;
            }
        } catch (\Exception $e) {
            // Ignore JSON errors
        }
            
        // Downloads are tracked in database
        $downloadsCount = 0;
        try {
            if (\Schema::hasTable('photo_downloads')) {
                // Count all download records for this user
                $downloadsCount = \App\Models\PhotoDownload::where('user_id', $user->id)
                    ->whereHas('photo') // Only count if photo still exists
                    ->count();
            }
        } catch (\Exception $e) {
            $downloadsCount = 0;
        }
        
        // Get recent activity photos (preview)
        $recentLikesRaw = \App\Models\PhotoLike::where('user_id', $user->id)
            ->with(['photo.gallery'])
            ->whereHas('photo') // Just check photo exists
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Filter duplicates: group berita photos by related_id
        $seenLikes = [];
        $recentLikes = $recentLikesRaw->filter(function($like) use (&$seenLikes) {
            if (!$like->photo) return false;
            
            // For berita photos, use related_id as unique key
            if ($like->photo->related_type === 'berita' && $like->photo->related_id) {
                $uniqueKey = 'berita_' . $like->photo->related_id;
            } else {
                $uniqueKey = 'photo_' . $like->photo->id;
            }
            
            if (isset($seenLikes[$uniqueKey])) return false;
            $seenLikes[$uniqueKey] = true;
            return true;
        })->take(6)->values();
        
        // Get recent downloads (preview) - show latest 6 downloads
        $recentDownloads = collect([]);
        if (\Illuminate\Support\Facades\Schema::hasTable('photo_downloads')) {
            $recentDownloads = \App\Models\PhotoDownload::where('user_id', $user->id)
                ->with(['photo.gallery'])
                ->whereHas('photo') // Just check photo exists
                ->orderBy('downloaded_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }
        
        // Get liked posts (berita) - hanya kategori "Berita Terkini" (kategori_id = 1)
        $likedPosts = \App\Models\PostLike::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function($like) {
                $post = \App\Models\Post::find($like->photo_id);
                // Filter hanya Berita Terkini (kategori_id = 1)
                return ($post && $post->kategori_id == 1) ? $post : null;
            })
            ->filter(); // Remove nulls
        
        // Get commented posts (berita) - hanya kategori "Berita Terkini"
        $commentedPosts = \App\Models\PostComment::where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function($comment) {
                $post = \App\Models\Post::find($comment->photo_id);
                return ($post && $post->kategori_id == 1) ? $post : null;
            })
            ->filter()
            ->unique('id')
            ->values();
        
        // Get downloaded posts (berita) - hanya kategori "Berita Terkini"
        $downloadedPosts = collect([]);
        if (\Illuminate\Support\Facades\Schema::hasTable('photo_downloads')) {
            $downloadedPosts = \App\Models\PhotoDownload::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($download) {
                    $post = \App\Models\Post::find($download->photo_id);
                    return ($post && $post->kategori_id == 1) ? $post : null;
                })
                ->filter()
                ->unique('id')
                ->values();
        }

        return view('user.profile.show', [
            'user' => $user,
            'likesCount' => $likesCount,
            'commentsCount' => $commentsCount,
            'downloadsCount' => $downloadsCount,
            'recentLikes' => $recentLikes,
            'recentDownloads' => $recentDownloads,
            'likedPosts' => $likedPosts,
            'commentedPosts' => $commentedPosts,
            'downloadedPosts' => $downloadedPosts,
        ]);
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        return view('user.profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'in:male,female,other'],
            // allow +, digits, spaces, dashes, parentheses; 7-20 chars total
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[+0-9][0-9\s\-()]{6,19}$/'],
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('user.profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword()
    {
        return view('user.profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profile.show')->with('success', 'Password berhasil diubah!');
    }

    /**
     * Show notification settings.
     */
    public function notification()
    {
        return view('user.profile.notification', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Show shipping address.
     */
    public function shippingAddress()
    {
        return view('user.profile.shipping-address', [
            'user' => Auth::user()
        ]);
    }
}
