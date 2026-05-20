<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Google Sans', 'Roboto', sans-serif; }
    </style>
</head>
<body class="antialiased" style="background:#f8f9fa;">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">

        {{-- Logo --}}
        <div class="mb-8 text-center">
            <div class="flex items-center justify-center mb-3">
                <img src="{{ asset('logo.png') }}" alt="MediTrack Logo"
                    style="height:72px; width:auto; object-fit:contain;">
            </div>
            <p style="font-size:13px; color:#5f6368; margin-top:4px;">Sistem Lacak Lokasi Pengiriman Obat</p>
        </div>

        {{-- Card --}}
        <div style="background:#fff; border:1px solid #e0e0e0; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); width:100%; max-width:420px; padding:32px 40px;">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <p style="font-size:12px; color:#80868b; margin-top:24px;">
            &copy; {{ date('Y') }} MediTrack. All rights reserved.
        </p>
    </div>
</body>
</html>
