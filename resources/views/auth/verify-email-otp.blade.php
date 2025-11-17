<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - SMKN 4 Kota Bogor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #023859 0%, #26658C 50%, #54ACBF 100%);">
    <div class="max-w-md w-full">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 class="login-title">Verifikasi Email</h2>
                <p class="login-subtitle">
                    Masukkan kode OTP 6 digit yang telah dikirim ke email Anda
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    Kode OTP baru telah dikirim ke email Anda!
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="verify-info">
                <div class="verify-step">
                    <i class="fas fa-envelope"></i>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                </div>
            </div>

            <!-- OTP Form -->
            <form method="POST" action="{{ route('verification.verify.otp') }}">
                @csrf
                <div class="form-group">
                    <label class="login-label">Kode OTP (6 Digit)</label>
                    <input type="text" 
                           name="otp" 
                           class="login-input otp-input" 
                           placeholder="000000" 
                           maxlength="6" 
                           pattern="[0-9]{6}"
                           inputmode="numeric"
                           required 
                           autofocus>
                    <small class="text-muted">Kode valid selama 10 menit</small>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-check-circle"></i>
                    Verifikasi Sekarang
                </button>
            </form>

            <div class="verify-actions">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="resend-btn">
                        <i class="fas fa-redo"></i>
                        Kirim Ulang Kode OTP
                    </button>
                </form>

                <div class="verify-divider">Atau</div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .login-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .login-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: white;
    }

    .login-title {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .login-subtitle {
        font-size: 15px;
        color: #6b7280;
        line-height: 1.6;
    }

    .login-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .login-input {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        outline: none;
    }

    .login-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .login-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 8px;
    }

    .login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    .otp-input {
        text-align: center !important;
        font-size: 28px !important;
        font-weight: bold !important;
        letter-spacing: 10px !important;
        font-family: 'Courier New', monospace !important;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .text-muted {
        color: #6b7280;
        font-size: 13px;
        display: block;
        margin-top: 8px;
        text-align: center;
    }

    .verify-info {
        background: #f3f4f6;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        text-align: center;
    }

    .verify-step i {
        color: #3b82f6;
        font-size: 24px;
        margin-bottom: 8px;
    }

    .resend-btn {
        width: 100%;
        padding: 14px 24px;
        background: transparent;
        border: 2px solid #3b82f6;
        color: #3b82f6;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .resend-btn:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .verify-actions {
        margin-top: 24px;
    }

    .verify-divider {
        text-align: center;
        margin: 16px 0;
        color: #9ca3af;
        font-size: 14px;
    }

    .logout-btn {
        width: 100%;
        padding: 14px 24px;
        background: transparent;
        border: 2px solid #ef4444;
        color: #ef4444;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .logout-btn:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }

    .alert-success {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        color: #065f46;
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
</style>
</body>
</html>
