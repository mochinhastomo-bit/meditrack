# MediTrack Kurir — Android App

Aplikasi Android untuk kurir pengiriman obat MediTrack.

## Cara Membuka di Android Studio

1. Buka Android Studio
2. Pilih **File → Open**
3. Navigasi ke folder `meditrack/android-app/`
4. Klik **OK** dan tunggu Gradle sync selesai

## Konfigurasi Base URL

Edit file `app/src/main/java/com/meditrack/kurir/utils/Constants.kt`:

```kotlin
// Emulator Android (localhost):
const val BASE_URL = "http://10.0.2.2:8000/api/"

// HP fisik di jaringan WiFi yang sama:
const val BASE_URL = "http://192.168.1.XXX:8000/api/"
// Ganti XXX dengan IP komputer Anda (cek dengan: ipconfig / ifconfig)
```

## Fitur

- **Login** — autentikasi kurir via email & password
- **Dashboard** — daftar order siap kirim & dalam pengiriman
- **Proses Pengiriman** — klik untuk ubah status ke `dalam_pengiriman`
- **GPS Real-time** — lokasi kurir dikirim ke server setiap 15 detik
- **Google Maps** — tampilkan posisi kurir & tujuan di peta
- **Tombol Selesai** — aktif otomatis jika kurir berada ≤ 100 meter dari lokasi pasien
- **Background Service** — GPS tetap berjalan saat aplikasi diminimize

## Requirements

- Android Studio Hedgehog (2023.1.1) atau lebih baru
- Android SDK 26+ (Android 8.0)
- Google Maps API Key (sudah dikonfigurasi)
- Server Laravel MediTrack harus berjalan
