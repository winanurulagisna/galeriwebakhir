<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // fix: tampilkan foto unduhan dengan relasi yang benar
        $downloads = $user->photoDownloads()
            ->with(['photo.gallery'])
            ->orderByDesc('created_at')
            ->paginate(9);

        // fix: gunakan relasi yang benar untuk like & komentar
        $likeCount = $user->photoLikes()->count() ?? 0;
        $commentCount = $user->photoComments()->count() ?? 0;

        return view('user.downloads', compact('downloads', 'likeCount', 'commentCount'));
    }
}
