<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class PhotoCommentController extends Controller
{
    public function store(Photo $photo, Request $request)
    {
        // Validate input (guest or auth)
        $rules = [
            'body' => ['required','string','min:3','max:2000'],
            'g-recaptcha-response' => ['required','string'],
        ];
        if (!auth()->check()) {
            $rules = array_merge($rules, [
                'first_name' => ['required','string','max:100'],
                'last_name'  => ['required','string','max:100'],
                'email'      => ['required','email','max:150'],
            ]);
        }
        $request->validate($rules, [
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required'  => 'Nama belakang wajib diisi.',
            'email.required'      => 'Email wajib diisi.',
            'body.required'       => 'Komentar tidak boleh kosong.',
            'g-recaptcha-response.required' => 'Silakan verifikasi reCAPTCHA.',
        ]);

        // Verify Google reCAPTCHA v2 (fallback to Google's test secret key if not configured)
        $secret = config('services.recaptcha.secret')
            ?: env('RECAPTCHA_SECRET_KEY')
            ?: '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

        $token = $request->input('g-recaptcha-response');
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);
            $ok = $response->ok() ? (bool) data_get($response->json(), 'success') : false;
        } catch (\Throwable $e) {
            $ok = false;
        }

        if (!$ok) {
            return redirect()->back()
                ->withInput()
                ->with('comment_error_photo', $photo->id)
                ->withErrors(['g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.']);
        }

        // Ensure table exists
        if (!Schema::hasTable('photo_comments')) {
            return redirect()->back()->with('error', 'Fitur komentar belum siap (tabel belum dibuat).');
        }

        PhotoComment::create([
            'photo_id'     => $photo->id,
            'comment_type' => 'photo', // Explicitly set as photo comment
            'user_id'      => auth()->id(),
            'first_name'   => $request->string('first_name')->trim() ?: null,
            'last_name'    => $request->string('last_name')->trim() ?: null,
            'email'        => $request->string('email')->trim() ?: null,
            'body'         => $request->string('body')->trim(),
            'status'       => 'pending',
        ]);

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dikirim dan menunggu persetujuan admin.'
            ]);
        } else {
            return redirect()->back()
                ->with('success', 'Komentar berhasil dikirim dan menunggu persetujuan admin.')
                ->with('comment_success_photo', $photo->id);
        }
    }
}
