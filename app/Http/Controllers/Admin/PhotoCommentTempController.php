<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoComment;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Http\Request;

class PhotoCommentTempController extends Controller
{
    public function index()
    {
        return view('admin.komentar.index');
    }

    public function list(Request $request)
    {
        try {
            // Ambil semua komentar dari database (foto dan berita)
            $comments = PhotoComment::with(['user'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($comment) {
                    // Cek apakah ini komentar foto atau berita
                    $photo = Photo::find($comment->photo_id);
                    $post = Post::find($comment->photo_id);
                    
                    // Jika keduanya ada (ID sama), cek dari created_at mana yang lebih baru
                    if ($photo && $post) {
                        // Bandingkan waktu komentar dengan waktu post/photo dibuat
                        // Asumsi: komentar dibuat setelah item dibuat
                        $photoDiff = abs(strtotime($comment->created_at) - strtotime($photo->created_at));
                        $postDiff = abs(strtotime($comment->created_at) - strtotime($post->created_at));
                        
                        // Yang lebih dekat waktunya kemungkinan yang benar
                        if ($postDiff < $photoDiff) {
                            $type = 'Berita';
                            $title = $post->judul;
                        } else {
                            $type = 'Foto';
                            $title = $photo->judul ?? 'Foto #'.$photo->id;
                        }
                    } else {
                        $type = $photo ? 'Foto' : ($post ? 'Berita' : 'Unknown');
                        $title = $photo ? ($photo->judul ?? 'Foto #'.$photo->id) : ($post ? $post->judul : 'Item #'.$comment->photo_id);
                    }
                    
                    return [
                        'id' => $comment->id,
                        'name' => $comment->user->name ?? 'Anonymous',
                        'email' => $comment->user->email ?? '-',
                        'body' => $comment->body,
                        'type' => $type,
                        'title' => $title,
                        'photo_id' => $comment->photo_id,
                        'created_at' => $comment->created_at->toIso8601String(),
                        'status' => $comment->status === 'approved' ? 'Disetujui' : ($comment->status === 'rejected' ? 'Tidak Disetujui' : 'Menunggu'),
                    ];
                });
            
            return response()->json(['data' => $comments->values()]);
        } catch (\Exception $e) {
            \Log::error('Error in PhotoCommentTempController@list: ' . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    public function approve(string $id)
    {
        $comment = PhotoComment::findOrFail($id);
        $comment->update(['status' => 'approved']);
        return response()->json(['ok' => true]);
    }

    public function reject(string $id)
    {
        $comment = PhotoComment::findOrFail($id);
        $comment->update(['status' => 'rejected']);
        return response()->json(['ok' => true]);
    }

    public function destroy(string $id)
    {
        $comment = PhotoComment::findOrFail($id);
        $comment->delete();
        return response()->json(['ok' => true]);
    }
}
