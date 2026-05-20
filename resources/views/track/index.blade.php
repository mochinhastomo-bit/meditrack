<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pengiriman — MediTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-wrap img {
            height: 72px;
            width: auto;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .logo-sub { font-size: 14px; color: #5f6368; margin-top: 4px; }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 36px 32px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .card h2 {
            font-size: 20px;
            font-weight: 700;
            color: #202124;
            margin-bottom: 6px;
        }
        .card p {
            font-size: 14px;
            color: #5f6368;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 16px;
        }
        .input-wrap .material-icons {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa0a6;
            font-size: 20px;
        }
        .input-wrap input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #202124;
            outline: none;
            transition: border-color .2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-wrap input:focus { border-color: #1a73e8; }
        .input-wrap input::placeholder { color: #9aa0a6; text-transform: none; letter-spacing: 0; }

        .btn {
            width: 100%;
            padding: 14px;
            background: #1a73e8;
            color: #fff;
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
        .btn:hover  { background: #1557b0; }
        .btn:active { transform: scale(0.98); }

        @if(session('error'))
        .alert-error {
            background: #fce8e6;
            color: #c5221f;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        @endif

        .hint {
            text-align: center;
            font-size: 12px;
            color: #9aa0a6;
            margin-top: 20px;
        }
        .hint span { color: #1a73e8; font-weight: 500; }

        .steps {
            display: flex;
            gap: 10px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #f1f3f4;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 12px 8px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .step .material-icons { font-size: 22px; color: #1a73e8; margin-bottom: 6px; display: block; }
        .step-label { font-size: 11px; color: #5f6368; font-weight: 500; line-height: 1.3; }
    </style>
</head>
<body>

<div class="logo-wrap">
    <img src="{{ asset('logo.png') }}" alt="MediTrack">
    <div class="logo-sub">Sistem Lacak Lokasi Pengiriman Obat</div>
</div>

<div class="card">
    <h2>Cek Status Pengiriman</h2>
    <p>Masukkan kode resep yang tertera pada struk atau notifikasi yang Anda terima untuk melacak pengiriman obat Anda.</p>

    @if(session('error'))
    <div class="alert-error">
        <span class="material-icons" style="font-size:18px;">error_outline</span>
        {{ session('error') }}
    </div>
    @endif

    <form onsubmit="handleSubmit(event, this)" autocomplete="off">
        <div class="input-wrap">
            <span class="material-icons">receipt_long</span>
            <input
                type="text"
                name="_kode"
                id="kodeInput"
                placeholder="Contoh: RES-20260506-0001"
                autocomplete="off"
                autofocus
                required
            >
        </div>
        <button type="submit" class="btn">
            <span class="material-icons" style="font-size:20px;">search</span>
            Lacak Pengiriman
        </button>
    </form>

    <div class="hint">Kode resep dikirimkan bersama struk pembelian Anda.</div>

    <div class="steps">
        <div class="step">
            <span class="material-icons">science</span>
            <div class="step-label">Penyiapan Obat</div>
        </div>
        <div class="step">
            <span class="material-icons">inventory_2</span>
            <div class="step-label">Siap Kirim</div>
        </div>
        <div class="step">
            <span class="material-icons">delivery_dining</span>
            <div class="step-label">Dalam Pengiriman</div>
        </div>
        <div class="step">
            <span class="material-icons">check_circle</span>
            <div class="step-label">Terkirim</div>
        </div>
    </div>
</div>

<script>
function handleSubmit(e, form) {
    e.preventDefault();
    const kode = document.getElementById('kodeInput').value.trim().toUpperCase();
    if (!kode) return;
    window.location.href = '{{ url("track") }}/' + encodeURIComponent(kode);
}
</script>
</body>
</html>
