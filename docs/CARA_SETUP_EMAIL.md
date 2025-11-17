# 📧 Cara Setup Email Gmail - LENGKAP

## ⚠️ PENTING: Anda HARUS punya App Password dari Google!

---

## 🔐 Step 1: Dapatkan App Password (WAJIB!)

### A. Aktifkan 2-Step Verification
1. Buka: https://myaccount.google.com/security
2. Scroll ke **"2-Step Verification"**
3. Klik **"Get Started"**
4. Ikuti instruksi (verifikasi dengan nomor HP)
5. Tunggu sampai aktif

### B. Buat App Password
1. Buka: https://myaccount.google.com/apppasswords
2. Login dengan: **gisnawina8@gmail.com**
3. **Select app:** Pilih **"Mail"**
4. **Select device:** Pilih **"Other (Custom name)"**
5. Ketik: **Laravel SMKN4**
6. Klik **"Generate"**
7. **COPY password** (16 karakter, contoh: `abcd efgh ijkl mnop`)
8. **HAPUS SEMUA SPASI** → jadi: `abcdefghijklmnop`

**SIMPAN password ini!** Anda akan pakai di step berikutnya.

---

## 📝 Step 2: Update File .env

### Cara 1: Edit Manual (Recommended)

1. **Buka file `.env`** di folder project:
   ```
   c:\xampp\htdocs\updateweb-main\.env
   ```

2. **Cari baris** yang dimulai dengan `MAIL_`

3. **Ubah/Tambahkan** baris berikut:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=gisnawina8@gmail.com
   MAIL_PASSWORD=abcdefghijklmnop
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=gisnawina8@gmail.com
   MAIL_FROM_NAME="SMKN 4 Kota Bogor"
   ```

4. **Ganti `abcdefghijklmnop`** dengan App Password Anda (tanpa spasi!)

5. **Save file** (Ctrl + S)

### Cara 2: Pakai Script Otomatis

1. **Double-click file:** `setup-gmail.bat`
2. **Masukkan App Password** saat diminta
3. **Enter**
4. **Done!**

---

## 🔄 Step 3: Clear Config Cache

Buka Command Prompt/Terminal di folder project, jalankan:

```bash
php artisan config:clear
```

---

## ✅ Step 4: Test Email

### A. Logout & Register Baru
1. Logout dari akun sekarang
2. Buka: http://127.0.0.1:8000/register
3. Isi form register
4. Submit

### B. Cek Gmail
1. Buka Gmail: https://mail.google.com
2. Login dengan: gisnawina8@gmail.com
3. Cek **Inbox** atau **Spam/Junk**
4. Cari email dari **SMKN 4 Kota Bogor**
5. Buka email → Lihat kode OTP

### C. Masukkan OTP
1. Copy kode OTP dari email
2. Paste di halaman verifikasi
3. Klik **"Verifikasi Sekarang"**
4. **Done!** ✅

---

## 🔍 Troubleshooting

### Email tidak masuk?
- ✅ Cek folder **Spam/Junk**
- ✅ Tunggu 1-2 menit
- ✅ Pastikan App Password benar (tanpa spasi)
- ✅ Jalankan: `php artisan config:clear`

### Error "Invalid credentials"?
- ❌ App Password salah
- ✅ Generate ulang App Password
- ✅ Pastikan tidak ada spasi
- ✅ Copy-paste langsung dari Google

### Error "Less secure app"?
- ❌ Jangan pakai password Gmail biasa
- ✅ HARUS pakai App Password
- ✅ Aktifkan 2-Step Verification dulu

### Email masuk tapi tidak ada OTP?
- ✅ Cek isi email
- ✅ Kode OTP ada di body email
- ✅ Format: **Kode OTP: 123456**

---

## 📋 Checklist

- [ ] Aktifkan 2-Step Verification
- [ ] Buat App Password di Google
- [ ] Copy App Password (hapus spasi)
- [ ] Buka file `.env`
- [ ] Update konfigurasi MAIL_*
- [ ] Save file `.env`
- [ ] Jalankan `php artisan config:clear`
- [ ] Test register user baru
- [ ] Cek Gmail inbox
- [ ] Masukkan OTP
- [ ] Verifikasi berhasil ✅

---

## 🎉 Setelah Setup Berhasil

Email OTP akan terkirim ke **gisnawina8@gmail.com** dengan format:

```
From: SMKN 4 Kota Bogor <gisnawina8@gmail.com>
Subject: Kode OTP Verifikasi Email - SMKN 4 Kota Bogor

Halo [Nama]!

Terima kasih telah mendaftar di website SMKN 4 Kota Bogor.

Gunakan kode OTP berikut untuk memverifikasi email Anda:

Kode OTP: 123456

Kode ini akan kedaluwarsa dalam 10 menit.

Masukkan kode OTP di halaman verifikasi untuk melanjutkan.

Setelah verifikasi, Anda dapat:
✓ Menulis komentar pada foto
✓ Mengunduh foto galeri
✓ Berbagi foto ke media sosial

Jika Anda tidak mendaftar, abaikan email ini.

Salam,
Tim SMKN 4 Kota Bogor
```

---

## 💡 Tips

1. **Simpan App Password** di tempat aman
2. **Jangan share** App Password ke orang lain
3. **Jangan commit** file `.env` ke Git
4. **Test email** sebelum production
5. **Cek Spam** jika email tidak masuk

---

**Need Help?** Baca file ini lagi atau tanya saya! 🚀
