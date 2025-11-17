<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        // Require authentication for likes
        if (!auth()->check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu untuk menyukai berita'
                ], 401);
            }
            return redirect()->route('login')->with('message', 'Silakan login terlebih dahulu');
        }

        $userId = auth()->id();
        $sessionId = session()->getId();
        
        // Use PostLike model with photo_id storing post_id (existing structure)
        $like = PostLike::where('photo_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            // Unlike the post
            $like->delete();
            $liked = false;
        } else {
            // Like the post
            PostLike::create([
                'photo_id' => $post->id, // Store post_id in photo_id column (existing structure)
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            $liked = true;
        }

        // Get total likes count for the post
        $totalLikes = PostLike::where('photo_id', $post->id)->count();

        // Check if request is AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'total_likes' => $totalLikes,
                'message' => $liked ? 'Berita berhasil disukai' : 'Like berhasil dihapus'
            ]);
        }

        // Redirect back for regular form submission with success message
        $message = $liked ? 'Berita berhasil disukai!' : 'Like berhasil dihapus!';
        return redirect()->back()->with('success', $message);
    }

    public function count(Post $post)
    {
        $totalLikes = PostLike::where('photo_id', $post->id)->count();
        $userId = auth()->id();
        $isLiked = $userId ? PostLike::where('photo_id', $post->id)
            ->where('user_id', $userId)
            ->exists() : false;

        return response()->json([
            'total_likes' => $totalLikes,
            'is_liked' => $isLiked,
        ]);
    }
}
