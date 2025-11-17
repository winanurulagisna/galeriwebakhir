<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $otp = $notifiable->otp_code;

        return (new MailMessage)
            ->subject('Kode OTP Verifikasi Email - SMKN 4 Kota Bogor')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftar di website SMKN 4 Kota Bogor.')
            ->line('Gunakan kode OTP berikut untuk memverifikasi email Anda:')
            ->line('**Kode OTP: ' . $otp . '**')
            ->line('Kode ini akan kedaluwarsa dalam 10 menit.')
            ->line('Masukkan kode OTP di halaman verifikasi untuk melanjutkan.')
            ->line('Setelah verifikasi, Anda dapat:')
            ->line('✓ Menulis komentar pada foto')
            ->line('✓ Mengunduh foto galeri')
            ->line('✓ Berbagi foto ke media sosial')
            ->line('Jika Anda tidak mendaftar, abaikan email ini.')
            ->salutation('Salam,')
            ->salutation('Tim SMKN 4 Kota Bogor');
    }
}
