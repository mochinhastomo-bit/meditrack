<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack — Obat dari Apotek RS, Langsung ke Pintu Anda</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:    #1a73e8;
            --blue-dk: #1557b0;
            --blue-lt: #e8f0fe;
            --green:   #137333;
            --green-lt:#e6f4ea;
            --gray-1:  #202124;
            --gray-2:  #3c4043;
            --gray-3:  #5f6368;
            --gray-4:  #9aa0a6;
            --gray-5:  #f1f3f4;
            --white:   #ffffff;
            --radius:  14px;
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--white); color: var(--gray-1); line-height: 1.6; }

        /* ── NAVBAR ─────────────────────────────────────── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,0.93);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e8eaed;
            padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between;
            height: 52px;
        }
        .navbar-logo img { height: 28px; width: auto; }
        .navbar-login {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 13px; font-weight: 600; color: var(--blue);
            text-decoration: none; padding: 6px 14px;
            border: 1.5px solid var(--blue); border-radius: 8px;
            transition: background .2s, color .2s;
        }
        .navbar-login:hover { background: var(--blue); color: var(--white); }

        /* ── HERO ───────────────────────────────────────── */
        .hero {
            padding: 52px 20px 48px;
            text-align: center;
            max-width: 640px;
            margin: 0 auto;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--green-lt); color: var(--green);
            font-size: 12px; font-weight: 600;
            padding: 4px 12px; border-radius: 99px;
            margin-bottom: 18px;
        }
        .hero-badge .material-icons-round { font-size: 14px; }
        .hero-title {
            font-size: clamp(24px, 4.5vw, 38px);
            font-weight: 800; line-height: 1.2;
            color: var(--gray-1); margin-bottom: 14px;
            letter-spacing: -0.5px;
        }
        .hero-title span { color: var(--blue); }
        .hero-sub {
            font-size: 15px; color: var(--gray-3);
            margin-bottom: 28px;
            max-width: 480px; margin-left: auto; margin-right: auto;
        }

        /* ── FORM TRACKING ──────────────────────────────── */
        .track-form {
            background: var(--white);
            border: 1.5px solid #e0e0e0;
            border-radius: var(--radius);
            padding: 20px 18px;
            box-shadow: 0 6px 24px rgba(26,115,232,0.10);
            max-width: 440px; margin: 0 auto; text-align: left;
        }
        .track-form-label {
            display: block; font-size: 12px; font-weight: 600;
            color: var(--gray-2); margin-bottom: 8px;
        }
        .input-wrap { position: relative; margin-bottom: 10px; }
        .input-wrap .material-icons-round {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--gray-4); font-size: 18px; pointer-events: none;
        }
        .input-wrap input {
            width: 100%; padding: 11px 12px 11px 38px;
            border: 1.5px solid #e0e0e0; border-radius: 10px;
            font-size: 14px; font-family: 'Inter', sans-serif;
            color: var(--gray-1); outline: none;
            transition: border-color .2s;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .input-wrap input:focus { border-color: var(--blue); }
        .input-wrap input::placeholder { color: var(--gray-4); text-transform: none; letter-spacing: 0; }
        .btn-track {
            width: 100%; padding: 11px;
            background: var(--blue); color: var(--white);
            border: none; border-radius: 10px;
            font-size: 14px; font-weight: 600; font-family: 'Inter', sans-serif;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: background .2s, transform .1s;
        }
        .btn-track:hover { background: var(--blue-dk); }
        .btn-track:active { transform: scale(0.98); }
        .btn-track .material-icons-round { font-size: 18px; }

        /* ── VALUE PROPS (stats) ────────────────────────── */
        .stats-section { padding: 0 20px 40px; }
        .stats-inner {
            max-width: 700px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .stat-card {
            text-align: center; padding: 20px 12px;
            border-radius: var(--radius);
            border: 1.5px solid #e8eaed;
            background: var(--white);
        }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 10px;
        }
        .stat-icon.blue  { background: var(--blue-lt); }
        .stat-icon.green { background: var(--green-lt); }
        .stat-icon.orange { background: #fef3e2; }
        .stat-icon .material-icons-round { font-size: 22px; }
        .stat-icon.blue .material-icons-round  { color: var(--blue); }
        .stat-icon.green .material-icons-round { color: var(--green); }
        .stat-icon.orange .material-icons-round { color: #b06000; }
        .stat-number { font-size: 22px; font-weight: 800; color: var(--gray-1); line-height: 1; margin-bottom: 4px; }
        .stat-desc { font-size: 11px; color: var(--gray-3); font-weight: 500; }

        /* ── SECTION SHARED ─────────────────────────────── */
        .section { padding: 40px 20px; }
        .section-inner { max-width: 960px; margin: 0 auto; }
        .section-label {
            text-align: center; font-size: 11px; font-weight: 600;
            color: var(--blue); letter-spacing: 1px;
            text-transform: uppercase; margin-bottom: 6px;
        }
        .section-title {
            text-align: center; font-size: clamp(18px, 3vw, 24px);
            font-weight: 700; color: var(--gray-1); margin-bottom: 28px;
        }

        /* ── CARA KERJA ─────────────────────────────────── */
        .steps-wrap {
            display: grid;
            grid-template-columns: 1fr 24px 1fr 24px 1fr 24px 1fr;
            align-items: center;
            gap: 0;
        }
        .step-card {
            text-align: center; padding: 22px 14px;
            border-radius: var(--radius); background: var(--gray-5);
        }
        .step-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--blue); color: var(--white);
            font-size: 13px; font-weight: 700; margin-bottom: 10px;
        }
        .step-icon {
            display: block; font-size: 28px;
            color: var(--blue); margin-bottom: 8px;
        }
        .step-card h3 { font-size: 13px; font-weight: 700; color: var(--gray-1); margin-bottom: 4px; }
        .step-card p  { font-size: 11px; color: var(--gray-3); line-height: 1.5; }
        .step-arrow {
            text-align: center; color: var(--gray-4);
            font-size: 20px; line-height: 1;
        }
        .step-arrow .material-icons-round { font-size: 20px; color: var(--blue-dk); opacity: 0.4; }

        /* ── FITUR ──────────────────────────────────────── */
        .features-bg { background: var(--blue-lt); }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        .feature-card {
            background: var(--white); border-radius: var(--radius);
            padding: 16px; display: flex; gap: 12px; align-items: flex-start;
            box-shadow: 0 1px 6px rgba(0,0,0,0.05);
        }
        .feature-icon {
            flex-shrink: 0; width: 38px; height: 38px; border-radius: 10px;
            background: var(--blue-lt);
            display: flex; align-items: center; justify-content: center;
        }
        .feature-icon .material-icons-round { color: var(--blue); font-size: 20px; }
        .feature-card h3 { font-size: 13px; font-weight: 700; color: var(--gray-1); margin-bottom: 4px; }
        .feature-card p  { font-size: 12px; color: var(--gray-3); line-height: 1.5; }

        /* ── TESTIMONI ──────────────────────────────────── */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
        }
        .testimonial-card {
            background: var(--white);
            border: 1.5px solid #e8eaed;
            border-radius: var(--radius);
            padding: 20px 18px;
        }
        .testimonial-stars { color: #f9ab00; font-size: 14px; margin-bottom: 10px; letter-spacing: 2px; }
        .testimonial-text { font-size: 13px; color: var(--gray-2); line-height: 1.6; margin-bottom: 14px; font-style: italic; }
        .testimonial-author { display: flex; align-items: center; gap: 10px; }
        .testimonial-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--blue); color: var(--white);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; flex-shrink: 0;
        }
        .testimonial-name { font-size: 13px; font-weight: 600; color: var(--gray-1); }
        .testimonial-role { font-size: 11px; color: var(--gray-4); }

        /* ── CTA BOTTOM ─────────────────────────────────── */
        .cta-section { padding: 40px 20px; text-align: center; }
        .cta-box {
            max-width: 520px; margin: 0 auto;
            background: var(--blue); border-radius: 18px;
            padding: 36px 28px; color: var(--white);
        }
        .cta-box h2 { font-size: clamp(18px, 3vw, 24px); font-weight: 700; margin-bottom: 8px; }
        .cta-box p  { font-size: 13px; opacity: 0.85; margin-bottom: 22px; }
        .btn-cta {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--white); color: var(--blue);
            font-size: 14px; font-weight: 700; font-family: 'Inter', sans-serif;
            padding: 11px 24px; border-radius: 10px;
            border: none; cursor: pointer; text-decoration: none;
            transition: opacity .2s, transform .1s;
        }
        .btn-cta:hover { opacity: 0.92; }
        .btn-cta:active { transform: scale(0.98); }
        .btn-cta .material-icons-round { font-size: 18px; }

        /* ── FOOTER ─────────────────────────────────────── */
        .footer {
            background: var(--gray-1); color: rgba(255,255,255,0.55);
            text-align: center; padding: 20px; font-size: 12px;
        }
        .footer strong { color: var(--white); }
        .footer-divider { margin: 4px 0; }

        /* ── RESPONSIVE ─────────────────────────────────── */
        @media (max-width: 700px) {
            .steps-wrap {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            .step-arrow { transform: rotate(90deg); }
        }
        @media (max-width: 600px) {
            .hero { padding: 32px 16px 36px; }
            .stats-inner { grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
            .stat-card { padding: 14px 8px; }
            .stat-number { font-size: 18px; }
            .section { padding: 28px 16px; }
            .cta-box { padding: 28px 18px; }
        }
        @media (max-width: 400px) {
            .stats-inner { grid-template-columns: 1fr; }
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
        <span class="material-icons-round">verified</span>
        Resmi dari Apotek RS Petrokimia Gresik
    </div>
    <h1 class="hero-title">
        Obat dari Apotek RS,<br>
        <span>Langsung ke Depan Pintu</span> Anda
    </h1>
    <p class="hero-sub">Tidak perlu antri. Masukkan kode resep Anda dan pantau kurir secara langsung hingga obat tiba.</p>

    <div class="track-form">
        <label class="track-form-label" for="kodeResep">Lacak Pengiriman dengan Kode Resep</label>
        <div class="input-wrap">
            <span class="material-icons-round">receipt_long</span>
            <input type="text" id="kodeResep" placeholder="Contoh: RES-20260506-0001" autocomplete="off" autofocus>
        </div>
        <button class="btn-track" onclick="cekResep()">
            <span class="material-icons-round">search</span>
            Lacak Sekarang
        </button>
    </div>
</section>

{{-- VALUE PROPS --}}
<div class="stats-section">
    <div class="stats-inner">
        <div class="stat-card">
            <div class="stat-icon green">
                <span class="material-icons-round">verified_user</span>
            </div>
            <div class="stat-number">100%</div>
            <div class="stat-desc">Obat asli langsung dari apotek RS</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <span class="material-icons-round">my_location</span>
            </div>
            <div class="stat-number">Live</div>
            <div class="stat-desc">Lacak posisi kurir secara real-time</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <span class="material-icons-round">no_accounts</span>
            </div>
            <div class="stat-number">0</div>
            <div class="stat-desc">Tidak perlu daftar akun apapun</div>
        </div>
    </div>
</div>

{{-- CARA KERJA --}}
<section class="section">
    <div class="section-inner">
        <div class="section-label">Alur Pengiriman</div>
        <h2 class="section-title">Bagaimana Cara Kerjanya?</h2>
        <div class="steps-wrap">
            <div class="step-card">
                <div class="step-num">1</div>
                <span class="material-icons-round step-icon">science</span>
                <h3>Resep Diterima</h3>
                <p>Apotek RS memverifikasi resep dari dokter Anda.</p>
            </div>
            <div class="step-arrow"><span class="material-icons-round">arrow_forward</span></div>
            <div class="step-card">
                <div class="step-num">2</div>
                <span class="material-icons-round step-icon">inventory_2</span>
                <h3>Obat Disiapkan</h3>
                <p>Farmasi menyiapkan dan mengemas obat dengan aman.</p>
            </div>
            <div class="step-arrow"><span class="material-icons-round">arrow_forward</span></div>
            <div class="step-card">
                <div class="step-num">3</div>
                <span class="material-icons-round step-icon">delivery_dining</span>
                <h3>Kurir Berangkat</h3>
                <p>Pantau posisi kurir secara langsung di peta.</p>
            </div>
            <div class="step-arrow"><span class="material-icons-round">arrow_forward</span></div>
            <div class="step-card">
                <div class="step-num">4</div>
                <span class="material-icons-round step-icon">home</span>
                <h3>Obat Tiba</h3>
                <p>Obat diantar langsung ke alamat Anda.</p>
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
                <div class="feature-icon"><span class="material-icons-round">my_location</span></div>
                <div>
                    <h3>Lacak Real-Time</h3>
                    <p>Pantau posisi kurir di peta — tahu persis kapan obat akan tiba.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><span class="material-icons-round">receipt_long</span></div>
                <div>
                    <h3>Kode Resep Unik</h3>
                    <p>Setiap resep punya kode unik — lacak tanpa perlu login.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><span class="material-icons-round">timeline</span></div>
                <div>
                    <h3>Riwayat Status</h3>
                    <p>Lihat tiap tahapan dari penyiapan hingga obat sampai tangan Anda.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><span class="material-icons-round">verified_user</span></div>
                <div>
                    <h3>Aman & Terpercaya</h3>
                    <p>Data resep hanya bisa diakses dengan kode unik milik Anda.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><span class="material-icons-round">smartphone</span></div>
                <div>
                    <h3>Tanpa Aplikasi</h3>
                    <p>Cukup buka browser — tidak perlu install aplikasi apapun.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><span class="material-icons-round">local_pharmacy</span></div>
                <div>
                    <h3>Langsung dari Apotek RS</h3>
                    <p>Dikelola tim farmasi RS Petrokimia Gresik — obat terjamin keasliannya.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TESTIMONI --}}
<section class="section">
    <div class="section-inner">
        <div class="section-label">Testimoni</div>
        <h2 class="section-title">Kata Mereka yang Sudah Merasakan</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Sangat membantu. Obat mama yang biasanya harus saya ambil sendiri sekarang diantar langsung. Bisa lihat kurirnya dari HP, jadi tidak perlu khawatir."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">S</div>
                    <div>
                        <div class="testimonial-name">Siti Rahayu</div>
                        <div class="testimonial-role">Pasien Poli Dalam</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Tidak perlu antri lagi di apotek. Kode resep langsung dikirim, tinggal lacak dari rumah. Obatnya sampai dalam keadaan baik."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">B</div>
                    <div>
                        <div class="testimonial-name">Budi Santoso</div>
                        <div class="testimonial-role">Pasien Rawat Jalan</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Fitur lacak kurirnya benar-benar real-time. Obat untuk bapak saya tiba lebih cepat dari perkiraan. Pelayanannya sangat memuaskan."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">D</div>
                    <div>
                        <div class="testimonial-name">Dewi Anggraini</div>
                        <div class="testimonial-role">Keluarga Pasien</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA BOTTOM --}}
<section class="cta-section">
    <div class="cta-box">
        <h2>Pantau Status Pengiriman Obat Secara Real-Time</h2>
        <p>Cepat, mudah, tanpa perlu daftar akun.</p>
        <a href="{{ route('track.index') }}" class="btn-cta">
            <span class="material-icons-round">search</span>
            Lacak Pengiriman Sekarang
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
    if (!kode) { document.getElementById('kodeResep').focus(); return; }
    window.location.href = '{{ url("track") }}/' + encodeURIComponent(kode);
}
document.getElementById('kodeResep').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') cekResep();
});
</script>
</body>
</html>
