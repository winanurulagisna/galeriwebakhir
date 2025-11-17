# 🚀 Quick Start Guide - Auth System

## Setup dalam 5 Menit!

### Step 1: Setup Email (Pilih salah satu)

#### Option A: Gmail (Recommended)
1. Buka `.env`
2. Copy-paste konfigurasi ini:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SMKN 4 Kota Bogor"
```

3. Dapatkan App Password:
   - Buka: https://myaccount.google.com/apppasswords
   - Login dengan akun Gmail
   - Pilih "Mail" → "Other (Custom name)"
   - Nama: "Laravel SMKN4"
   - Copy password (16 karakter)
   - Paste ke `MAIL_PASSWORD`

#### Option B: Log (Testing Cepat)
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@smkn4bogor.sch.id
MAIL_FROM_NAME="SMKN 4 Kota Bogor"
```
Email akan disimpan di `storage/logs/laravel.log`

---

### Step 2: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

---

### Step 3: Test System

1. **Start Server**
```bash
php artisan serve
```

2. **Buka Browser**
```
http://127.0.0.1:8000/register
```

3. **Register User Baru**
   - Nama: Test User
   - Email: test@example.com
   - Password: password123
   - Confirm Password: password123

4. **Cek Email**
   - **Gmail**: Check inbox
   - **Log**: Buka `storage/logs/laravel.log`
   - Copy link verifikasi

5. **Klik Link Verifikasi**
   - Paste link di browser
   - Atau klik langsung dari email

6. **Test Features**
   - ✅ Komentar pada foto
   - ✅ Download foto
   - ✅ Share foto

---

## ✅ Checklist

- [ ] Update `.env` dengan email config
- [ ] Run `php artisan config:clear`
- [ ] Run `php artisan serve`
- [ ] Register user baru
- [ ] Cek email terkirim
- [ ] Klik link verifikasi
- [ ] Login berhasil
- [ ] Test komentar
- [ ] Test download
- [ ] Test share

---

## 🎯 Fitur yang Bisa Digunakan

### Sebelum Login (Guest)
- ✅ Lihat galeri foto
- ✅ Like foto
- ✅ Share foto
- ❌ Komentar (redirect ke login)
- ❌ Download (redirect ke login)

### Setelah Login (Unverified)
- ✅ Lihat galeri foto
- ✅ Like foto
- ✅ Share foto
- ⚠️ Komentar (redirect ke verify email)
- ⚠️ Download (redirect ke verify email)

### Setelah Verified ✅
- ✅ Lihat galeri foto
- ✅ Like foto
- ✅ Share foto
- ✅ Komentar
- ✅ Download

---

## 🚨 Troubleshooting

### Email tidak terkirim?
```bash
# 1. Clear cache
php artisan config:clear

# 2. Check .env
cat .env | grep MAIL

# 3. Check logs
tail -f storage/logs/laravel.log
```

### Link verification expired?
- Link berlaku 60 menit
- Klik "Kirim Ulang Email Verifikasi" di halaman verify

### Tidak bisa komentar/download?
- Pastikan sudah login
- Pastikan email sudah terverifikasi
- Check badge di user dropdown (harus hijau ✅)

---

## 📱 Test di Mobile

1. Buka `http://your-ip:8000` di HP
2. Test responsive design
3. Test mobile menu
4. Test user dropdown
5. Test share modal

---

## 🎨 Customize

### Ubah Warna Tema
File: `resources/views/auth/register.blade.php`
```css
background: linear-gradient(135deg, #YOUR_COLOR_1, #YOUR_COLOR_2);
```

### Ubah Logo
File: `resources/views/public/layouts/app.blade.php`
```html
<img src="{{ asset('images/YOUR_LOGO.png') }}" alt="Logo">
```

### Ubah Email Template
File: `app/Notifications/CustomVerifyEmail.php`
```php
->subject('Your Custom Subject')
->line('Your custom message')
```

---

## 📊 Production Checklist

Sebelum deploy ke production:

- [ ] Ganti `APP_ENV=production` di `.env`
- [ ] Ganti `APP_DEBUG=false` di `.env`
- [ ] Setup email service (SendGrid/Mailgun)
- [ ] Setup HTTPS/SSL
- [ ] Test email deliverability
- [ ] Backup database
- [ ] Setup monitoring

---

## 🎉 Selesai!

Sistem auth sudah siap digunakan!

**Next Steps:**
1. Customize email template
2. Add more features (profile edit, password reset)
3. Deploy to production

**Need Help?**
- Check `AUTH_SYSTEM_README.md`
- Check `EMAIL_SETUP_GUIDE.md`
- Check Laravel logs

**Happy Coding! 🚀**
