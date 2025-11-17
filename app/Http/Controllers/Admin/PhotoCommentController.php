<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoComment;
use Illuminate\Http\Request;

class PhotoCommentController extends Controller
{
    public function index()
    {
        $comments = PhotoComment::with(['photo', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.comments.index', compact('comments'));
    }
    
    public function approve(PhotoComment $comment)
    {
        $comment->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil disetujui'
        ]);
    }
    
    public function reject(PhotoComment $comment)
    {
        $comment->update([
            'status' => 'rejected'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Komentar ditolak'
        ]);
    }
    
    public function destroy(PhotoComment $comment)
    {
        $comment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dihapus'
        ]);
    }
}
