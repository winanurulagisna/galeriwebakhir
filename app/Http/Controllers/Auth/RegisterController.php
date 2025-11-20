<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Display the registration form.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users', function ($attribute, $value, $fail) {
                // Check if username exists in petugas table
                if ($value && DB::table('petugas')->where('username', $value)->exists()) {
                    $fail('Username ini sudah digunakan oleh admin. Silakan pilih username lain.');
                }
            }],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Create user with OTP
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10), // OTP valid 10 menit
        ];
        
        // Add username if provided
        if ($request->username) {
            $userData['username'] = $request->username;
        }
        
        $user = User::create($userData);

        // Fire Registered event (triggers email with OTP)
        event(new Registered($user));

        // Auto login (tapi belum verified)
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Kode OTP telah dikirim ke email Anda!');
    }
}