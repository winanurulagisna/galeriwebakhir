<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Display email verification notice.
     */
    public function notice()
    {
        return view('auth.verify-email-otp');
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit.',
        ]);

        $user = $request->user();

        // Check if OTP matches
        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
        }

        // Check if OTP expired
        if ($user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang.']);
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        
        // Clear OTP
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return redirect()->intended(route('home'))
            ->with('success', '🎉 Email berhasil diverifikasi! Sekarang Anda bisa komentar dan download foto.');
    }

    /**
     * Handle email verification.
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'))
                ->with('info', 'Email Anda sudah terverifikasi sebelumnya.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('home'))
            ->with('success', '🎉 Email berhasil diverifikasi! Sekarang Anda bisa komentar dan download foto.');
    }

    /**
     * Resend verification email with new OTP.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        $user = $request->user();

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
