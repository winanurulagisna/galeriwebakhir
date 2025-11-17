<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = PostComment::create([
            'photo_id' => $post->id, // Menggunakan photo_id untuk menyimpan post_id
            'comment_type' => 'post', // Explicitly set as post comment
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'status' => 'pending', // Menunggu approval admin
        ]);

        // Check if request is AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan!',
                'comment' => [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'name' => $comment->user->name ?? 'Anonymous',
                    'created_at' => $comment->created_at->diffForHumans(),
                ],
            ]);
        }

        // Redirect back for regular form submission with success message
        return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu persetujuan admin!');
    }

    public function index(Post $post)
    {
        $comments = PostComment::where('photo_id', $post->id)
            ->where('comment_type', 'post') // Only get post comments
            ->approved()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'name' => $comment->user->name ?? 'Anonymous',
                    'created_at' => $comment->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'comments' => $comments,
            'total' => $comments->count(),
        ]);
    }
}
