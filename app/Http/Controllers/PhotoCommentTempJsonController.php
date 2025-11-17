<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoComment;
use Illuminate\Http\Request;

class PhotoCommentTempJsonController extends Controller
{
    public function store(Photo $photo, Request $request)
    {
        $request->validate([
            'body' => ['required','string','min:3','max:2000'],
        ]);

        // Get user info from authenticated user
        $user = auth()->user();
        
        // Simpan ke database photo_comments
        $comment = PhotoComment::create([
            'photo_id'   => $photo->id,
            'user_id'    => $user->id,
            'body'       => (string) $request->string('body'),
            'status'     => 'pending', // Status pending untuk approval admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dikirim dan menunggu persetujuan admin.',
            'comment' => [
                'name' => $user->name,
                'body' => $request->body,
                'created_at' => $comment->created_at->diffForHumans(),
            ]
        ]);
    }
}
