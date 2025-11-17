<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\PhotoLike;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class PhotoLikeController extends Controller
{
    public function toggle(Photo $photo, Request $request)
    {
        try {
            $user = Auth::user();
            $sessionId = $request->session()->getId();
            $ip = $request->ip();
            $ua = $request->userAgent();

            \Log::info('Like toggle request received', [
                'photo_id' => $photo->id,
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'ip' => $ip,
                'user_agent' => substr($ua, 0, 100),
                'request_method' => $request->method(),
                'request_url' => $request->url(),
                'has_csrf_token' => $request->hasHeader('X-CSRF-TOKEN'),
                'csrf_token' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing'
            ]);

            // Mulai database transaction
            \DB::beginTransaction();

            // Cari like yang sudah ada
            $query = PhotoLike::where('photo_id', $photo->id);

            if ($user) {
                // Jika user login, cari berdasarkan user_id
                $query->where('user_id', $user->id);
            } else {
                // Jika guest, cari berdasarkan session_id
                $query->where('session_id', $sessionId);
            }

            $existing = $query->first();

            if ($existing) {
                // Hapus like yang ada
                $existing->delete();
                $liked = false;
            } else {
                // Buat like baru
                $newLike = PhotoLike::create([
                    'photo_id' => $photo->id,
                    'user_id' => $user ? $user->id : null,
                    'session_id' => $sessionId,
                    'ip' => $ip,
                    'user_agent' => $ua,
                ]);
                $liked = true;
            }

            // Hitung total like untuk foto ini
            $count = PhotoLike::where('photo_id', $photo->id)->count();
            
            // Commit transaction
            \DB::commit();

            \Log::info('Like toggle successful', [
                'photo_id' => $photo->id,
                'liked' => $liked,
                'count' => $count,
                'user_type' => $user ? 'authenticated' : 'guest'
            ]);

            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'liked' => $liked,
                    'count' => $count,
                    'photo_id' => $photo->id,
                    'message' => $liked ? 'Foto berhasil disukai' : 'Like berhasil dihapus'
                ]);
            } else {
                // For regular form submission, redirect back with success message
                $message = $liked ? 'Foto berhasil disukai!' : 'Like berhasil dihapus!';
                return redirect()->back()->with('success', $message);
            }
            
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            \DB::rollBack();
            \Log::error('Like error: ' . $e->getMessage(), [
                'photo_id' => $photo->id,
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses like',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ], 500);
            } else {
                // For regular form submission, redirect back with error message
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses like. Silakan coba lagi.');
            }
        }
    }
}
