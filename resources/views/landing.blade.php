<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack — Lacak Pengiriman Obat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:     #1a73e8;
            --blue-dk:  #1557b0;
            --blue-lt:  #e8f0fe;
            --gray-1:   #202124;
            --gray-2:   #3c4043;
            --gray-3:   #5f6368;
            --gray-4:   #9aa0a6;
            --gray-5:   #f1f3f4;
            --white:    #ffffff;
            --radius:   16px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--gray-1);
            line-height: 1.6;
        }

        /* ── NAVBAR ──────────────────────────────── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e8eaed;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }
        .navbar-logo img { height: 36px; width: auto; }
        .navbar-login {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 600;
            color: var(--blue);
            text-decoration: none;
            padding: 8px 18px;
            border: 2px solid var(--blue);
            border-radius: 10px;
            transition: background .2s, color .2s;
        }
        .navbar-login:hover { background: var(--blue); color: var(--white); }

        /* ── HERO ────────────────────────────────── */
        .hero {
            padding: 72px 24px 80px;
            text-align: center;
            max-width: 680px;
            margin: 0 auto;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--blue-lt);
            color: var(--blue);
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 99px;
            margin-bottom: 28px;
        }
        .hero-badge .material-icons-round { font-size: 16px; }
        .hero h1 {
            font-size: clamp(28px, 5vw, 44px);
            font-weight: 800;
            line-height: 1.2;
            color: var(--gray-1);
            margin-bottom: 18px;
            letter-spacing: -0.5px;
        }
        .hero h1 span { color: var(--blue); }
        .hero p {
            font-size: 16px;
            color: var(--gray-3);
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── FORM TRACKING ───────────────────────── */
        .track-form {
            background: var(--white);
            border: 2px solid #e0e0e0;
            border-radius: 20px;
            padding: 28px 24px;
            box-shadow: 0 8px 32px rgba(26,115,232,0.10);
            max-width: 480px;
            margin: 0 auto;
            text-align: left;
        }
        .track-form label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-2);
            margin-bottom: 10px;
        }
        .input-wrap {
            position: relative;
            margin-bottom: 14px;
        }
        .input-wrap .material-icons-round {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-4);
            font-size: 20px;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--gray-1);
            outline: none;
            transition: border-color .2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-wrap input:focus { border-color: var(--blue); }
        .input-wrap input::placeholder {
            color: var(--gray-4);
            text-transform: none;
            letter-spacing: 0;
        }
        .btn-track {
            width: 100%;
            padding: 14px;
            background: var(--blue);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .2s, transform .1s;
        }
        .btn-track:hover  { background: var(--blue-dk); }
        .btn-track:active { transform: scale(0.98); }
        .btn-track .material-icons-round { font-size: 20px; }

        /* ── CARA KERJA ──────────────────────────── */
        .section { padding: 72px 24px; }
        .section-inner { max-width: 960px; margin: 0 auto; }

        .section-label {
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            color: var(--blue);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .section-title {
            text-align: center;
            font-size: clamp(22px, 3.5vw, 32px);
            font-weight: 700;
            color: var(--gray-1);
            margin-bottom: 48px;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            position: relative;
        }
        .step-card {
            text-align: center;
            padding: 32px 20px;
            border-radius: var(--radius);
            background: var(--gray-5);
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--blue);
            color: var(--white);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .step-icon {
            display: block;
            font-size: 36px;
            color: var(--blue);
            margin-bottom: 16px;
        }
        .step-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-1);
            margin-bottom: 8px;
        }
        .step-card p {
            font-size: 14px;
            color: var(--gray-3);
            line-height: 1.5;
        }

        /* ── FITUR ───────────────────────────────── */
        .features-bg { background: var(--blue-lt); }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        .feature-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 28px 24px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .feature-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--blue-lt);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .feature-icon .material-icons-round { color: var(--blue); font-size: 24px; }
        .feature-card h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-1);
            margin-bottom: 6px;
        }
        .feature-card p {
            font-size: 13px;
            color: var(--gray-3);
            line-height: 1.5;
        }

        /* ── CTA BOTTOM ──────────────────────────── */
        .cta-section {
            padding: 72px 24px;
            text-align: center;
        }
        .cta-box {
            max-width: 560px;
            margin: 0 auto;
            background: var(--blue);
            border-radius: 24px;
            padding: 48px 32px;
            color: var(--white);
        }
        .cta-box h2 {
            font-size: clamp(20px, 3vw, 28px);
            font-weight: 700;
            margin-bottom: 12px;
        }
        .cta-box p {
            font-size: 15px;
            opacity: 0.85;
            margin-bottom: 28px;
        }
        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            color: var(--blue);
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            padding: 14px 28px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: opacity .2s, transform .1s;
        }
        .btn-cta:hover  { opacity: 0.92; }
        .btn-cta:active { transform: scale(0.98); }
        .btn-cta .material-icons-round { font-size: 20px; }

        /* ── FOOTER ──────────────────────────────── */
        .footer {
            background: var(--gray-1);
            color: rgba(255,255,255,0.6);
            text-align: center;
            padding: 32px 24px;
            font-size: 13px;
        }
        .footer strong { color: var(--white); }
        .footer-divider { margin: 8px 0; }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 600px) {
            .hero { padding: 48px 20px 56px; }
            .section { padding: 52px 20px; }
            .cta-box { padding: 36px 20px; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <div class="navbar-logo">
        <img src="{{ asset('logo.png') }}" alt="MediTrack">
    </div>
    <a href="{{ route('login') }}" class="navbar-login">
        <span class="material-icons-round">login</span>
        Masuk
    </a>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="hero-badge">
        <span class="material-icons-round">local_shipping</span>
        Lacak Pengiriman Obat Anda
    </div>
    <h1>Pantau Status <span>Pengiriman Obat</span> Secara Real-Time</h1>
    <p>Masukkan kode resep dari struk pembelian Anda untuk mengetahui posisi kurir dan estimasi kedatangan obat.</p>

    <div class="track-form">
        <label for="kodeResep">Kode Resep</label>
        <div class="input-wrap">
            <span class="material-icons-round">receipt_long</span>
            <input
                type="text"
                id="kodeResep"
                placeholder="Contoh: RES-20260506-0001"
                autocomplete="off"
                autofocus
            >
        </div>
        <button class="btn-track" onclick="cekResep()">
            <span class="material-icons-round">search</span>
            Lacak Sekarang
        </button>
    </div>
</section>

{{-- CARA KERJA --}}
<section class="section">
    <div class="section-inner">
        <div class="section-label">Alur Pengiriman</div>
        <h2 class="section-title">Bagaimana Cara Kerjanya?</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <span class="material-icons-round step-icon">science</span>
                <h3>Resep Diterima</h3>
                <p>Apotek menerima dan memverifikasi resep dari dokter Anda.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <span class="material-icons-round step-icon">inventory_2</span>
                <h3>Obat Disiapkan</h3>
                <p>Farmasi menyiapkan obat sesuai resep dan mengemas dengan aman.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <span class="material-icons-round step-icon">delivery_dining</span>
                <h3>Kurir Berangkat</h3>
                <p>Kurir mengambil paket dan Anda bisa melacak posisinya secara langsung.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <span class="material-icons-round step-icon">check_circle</span>
                <h3>Obat Tiba</h3>
                <p>Obat diantarkan langsung ke alamat Anda dengan aman.</p>
            </div>
        </div>
    </div>
</section>

{{-- FITUR --}}
<section class="section features-bg">
    <div class="section-inner">
        <div class="section-label">Keunggulan</div>
        <h2 class="section-title">Mengapa MediTrack?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">my_location</span>
                </div>
                <div>
                    <h3>Lacak Real-Time</h3>
                    <p>Pantau posisi kurir secara langsung di peta — tahu persis kapan obat akan tiba.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">qr_code</span>
                </div>
                <div>
                    <h3>Kode Resep Unik</h3>
                    <p>Setiap resep memiliki kode unik yang mudah dicari tanpa perlu login.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">timeline</span>
                </div>
                <div>
                    <h3>Riwayat Status</h3>
                    <p>Lihat setiap tahapan pengiriman dari penyiapan hingga obat sampai di tangan Anda.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">verified_user</span>
                </div>
                <div>
                    <h3>Aman & Terpercaya</h3>
                    <p>Data resep hanya dapat diakses dengan kode unik yang diberikan kepada Anda.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">smartphone</span>
                </div>
                <div>
                    <h3>Tanpa Aplikasi</h3>
                    <p>Cukup buka browser dari HP atau komputer — tidak perlu install aplikasi apapun.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">support_agent</span>
                </div>
                <div>
                    <h3>Didukung Apotek RS</h3>
                    <p>Dikelola langsung oleh tim farmasi rumah sakit untuk keakuratan informasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA BOTTOM --}}
<section class="cta-section">
    <div class="cta-box">
        <h2>Punya Kode Resep?</h2>
        <p>Lacak pengiriman obat Anda sekarang — cepat, mudah, tanpa perlu daftar akun.</p>
        <a href="{{ route('track.index') }}" class="btn-cta">
            <span class="material-icons-round">search</span>
            Lacak Pengiriman
        </a>
    </div>
</section>

{{-- FOOTER --}}
<footer class="footer">
    <div><strong>MediTrack</strong> — Sistem Lacak Pengiriman Obat</div>
    <div class="footer-divider">RS Petrokimia Gresik</div>
    <div>&copy; {{ date('Y') }} Hak Cipta Dilindungi</div>
</footer>

<script>
function cekResep() {
    const kode = document.getElementById('kodeResep').value.trim().toUpperCase();
    if (!kode) {
        document.getElementById('kodeResep').focus();
        return;
    }
    window.location.href = '{{ url("track") }}/' + encodeURIComponent(kode);
}
document.getElementById('kodeResep').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') cekResep();
});
</script>
</body>
</html>
