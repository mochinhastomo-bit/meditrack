# Panduan Deployment MediTrack ke Jago Shared Hosting

## Persiapan Lokal (di Mac Anda)

```bash
cd /Users/mochinhastomo/Laravel/meditrack

# 1. Install dependencies production
composer install --no-dev --optimize-autoloader

# 2. Build aset frontend
npm install && npm run build

# 3. Buat .env production (salin dari template)
cp .env.production.example .env.production
# Edit .env.production dengan data hosting Anda
```

---

## Setup di cPanel Jago Hosting

### A. Buat Database MySQL
1. Login cPanel → **MySQL Databases**
2. Buat database baru, misal: `username_meditrack`
3. Buat user baru dengan password kuat
4. Assign user ke database dengan **All Privileges**
5. Catat: host (biasanya `localhost`), nama DB, user, password

### B. Upload File

**Struktur target di server:**
```
/home/username/
├── public_html/          ← isi dari folder public/ Laravel
│   ├── index.php         ← pakai file dari deploy/public_html_index.php
│   ├── .htaccess
│   ├── favicon.ico
│   ├── logo.png
│   ├── robots.txt
│   ├── build/            ← hasil npm run build
│   └── artisan_runner.php  ← sementara, untuk migrasi
└── meditrack/            ← semua file Laravel KECUALI folder public/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env              ← pakai .env.production yang sudah diedit
    └── ...
```

**Cara upload via cPanel File Manager:**
1. Upload folder `meditrack/` (semua kecuali folder `public/`) ke `/home/username/meditrack/`
2. Upload isi folder `public/` ke `/home/username/public_html/`
3. **Ganti** `/home/username/public_html/index.php` dengan isi dari `deploy/public_html_index.php`
   - Sesuaikan path `LARAVEL_ROOT`: ganti `dirname(__DIR__) . '/meditrack'`
     menjadi path absolut, contoh: `/home/username/meditrack`
4. Upload `.env.production` ke `/home/username/meditrack/` dan rename menjadi `.env`
5. Upload `deploy/artisan_runner.php` ke `/home/username/public_html/`
   - **Edit dulu**: ganti `SECRET_KEY` dengan string acak panjang Anda

### C. Set Permission Storage
Via cPanel File Manager, set permission folder berikut ke **755**:
- `meditrack/storage/`
- `meditrack/storage/app/`
- `meditrack/storage/framework/`
- `meditrack/storage/logs/`
- `meditrack/bootstrap/cache/`

### D. Jalankan Migrasi via Browser
Akses URL berikut di browser:
```
https://domainanda.com/artisan_runner.php?secret=STRING_ACAK_ANDA&cmd=migrate
```

Perintah lain yang perlu dijalankan (satu per satu):
```
?secret=XXX&cmd=key:generate     # Generate APP_KEY (jika belum)
?secret=XXX&cmd=optimize         # Cache config, route, view
?secret=XXX&cmd=storage:link     # Link storage publik
```

### E. HAPUS artisan_runner.php
Setelah semua selesai, hapus file tersebut dari `public_html/`!

---

## Verifikasi
- [ ] Buka `https://domainanda.com` — halaman muncul
- [ ] Coba login
- [ ] Test API: `POST https://domainanda.com/api/kurir/login`
- [ ] Hapus `artisan_runner.php`

---

## Catatan Penting
- PHP yang dibutuhkan: **8.3+** — cek di cPanel → **Select PHP Version**
- Ekstensi PHP yang harus aktif: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- Pastikan `APP_DEBUG=false` di production!
