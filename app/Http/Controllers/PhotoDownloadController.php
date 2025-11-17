<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PhotoDownloadController extends Controller
{
    // POST /photos/{photo}/request-download
    public function requestDownload(Photo $photo, Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:150'],
            'password' => ['required','string','min:3','max:100'],
            'not_robot' => ['accepted'],
        ]);

        $key = $this->sessionKey($photo->id, 'auth');
        // mark as authorized for this photo for this session
        session([$key => now()->addMinutes(5)->toIso8601String()]);

        // log submission (authorized) to JSON
        try {
            $rel = 'photo_download_submissions.json';
            $abs = storage_path('app/'.$rel);
            if (!is_dir(dirname($abs))) { @mkdir(dirname($abs), 0775, true); }
            $raw = is_file($abs) ? @file_get_contents($abs) : '';
            $arr = $raw ? json_decode($raw, true) : [];
            if (!is_array($arr)) { $arr = []; }
            $id = (string) Str::uuid();
            $entry = [
                'id' => $id,
                'photo_id' => $photo->id,
                'name' => (string) $request->string('name'),
                'email' => (string) $request->string('email'),
                'created_at' => now()->toIso8601String(),
                'ip' => $request->ip(),
                'ua' => (string) $request->header('User-Agent'),
                'status' => 'authorized',
            ];
            $arr[] = $entry;
            $json = json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            $ok = @file_put_contents($abs, $json, LOCK_EX) !== false;
            if (!$ok) { \Log::error('Failed to write photo_download_submissions.json to '.$abs); }
            // save latest submission id to session to mark downloaded later
            session([$this->sessionKey($photo->id, 'sub') => $id]);
        } catch (\Throwable $e) {
            \Log::error('Submission log write error: '.$e->getMessage());
        }

        return response()->json([
            'ok' => true,
            'downloadUrl' => route('photo.download', $photo),
        ]);
    }

    // GET /photos/{photo}/download
    public function download(Photo $photo, Request $request)
    {
        // Check if user is authenticated and verified
        if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
            // User is logged in and verified - allow direct download
            $authorized = true;
            
            // Log download to database
            try {
                PhotoDownload::create([
                    'photo_id' => $photo->id,
                    'user_id' => auth()->id(),
                    'session_id' => $request->session()->getId(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'downloaded_at' => now(),
                ]);
            } catch (\Throwable $e) {
                \Log::error('Download log error: '.$e->getMessage());
            }
        } else {
            // Check session-based authorization (for guest users with form)
            $key = $this->sessionKey($photo->id, 'auth');
            $until = session($key);
            $authorized = false;
            if (is_string($until) && strtotime($until) !== false) {
                try {
                    $authorized = now()->lte(\Carbon\Carbon::parse($until));
                } catch (\Throwable $e) {
                    $authorized = false;
                }
            }
            if (!$authorized) {
                abort(403, 'Unauthorized - Please login or fill the download form');
            }
            
            // Log download for guest user
            try {
                PhotoDownload::create([
                    'photo_id' => $photo->id,
                    'user_id' => null,
                    'session_id' => $request->session()->getId(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'downloaded_at' => now(),
                ]);
            } catch (\Throwable $e) {
                \Log::error('Guest download log error: '.$e->getMessage());
            }
            
            // one-time use
            session()->forget($key);
        }

        // Download counter is now handled by database records

        // update submission status to downloaded
        try {
            $subKey = $this->sessionKey($photo->id, 'sub');
            $subId = session($subKey);
            session()->forget($subKey);
            $rel = 'photo_download_submissions.json';
            $abs = storage_path('app/'.$rel);
            if (is_file($abs)) {
                $raw = @file_get_contents($abs);
                $arr = $raw ? json_decode($raw, true) : [];
                if (is_array($arr)) {
                    foreach ($arr as &$it) {
                        if (($it['id'] ?? '') === (string)$subId) {
                            $it['status'] = 'downloaded';
                            $it['downloaded_at'] = now()->toIso8601String();
                            break;
                        }
                    }
                    $json = json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                    @file_put_contents($abs, $json, LOCK_EX);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Submission status update error: '.$e->getMessage());
        }

        $file = (string) $photo->file;
        // If remote URL, allow redirect after auth
        if (Str::startsWith($file, ['http://','https://'])) {
            return redirect()->away($file);
        }
        // If it is a storage path (e.g., photos/abc.jpg), prefer public disk
        if (!Str::startsWith($file, ['/'])) {
            if (Storage::disk('public')->exists($file)) {
                $name = basename($file);
                return Storage::disk('public')->download($file, $name);
            }
        }
        // If absolute path under public
        $publicPath = public_path(ltrim($file, '/'));
        if (is_file($publicPath)) {
            return response()->download($publicPath, basename($publicPath));
        }

        abort(404, 'File tidak ditemukan');
    }

    protected function sessionKey($photoId, string $suffix = 'auth'): string
    {
        return 'photo_dl_'.$suffix.'_'.$photoId;
    }
}
