<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #202124; }
        .header { background: #1a73e8; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .header p  { font-size: 10px; opacity: .85; }
        .filter-bar {
            display: flex; gap: 16px; flex-wrap: wrap;
            background: #f8f9fa; border-radius: 6px;
            padding: 10px 14px; margin: 0 20px 14px; font-size: 10px; color: #5f6368;
        }
        .filter-item strong { color: #202124; }
        table { width: calc(100% - 40px); margin: 0 20px; border-collapse: collapse; }
        th {
            background: #1a73e8; color: #fff; padding: 8px 10px;
            text-align: left; font-size: 10px; font-weight: 600;
        }
        td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
        tr:nth-child(even) td { background: #f8f9fa; }
        .badge {
            display: inline-block; padding: 2px 6px; border-radius: 8px;
            font-size: 9px; font-weight: 600;
        }
        .badge-penyiapan        { background: #fef7e0; color: #b06000; }
        .badge-siap_kirim       { background: #e8f0fe; color: #1557b0; }
        .badge-dibawa           { background: #ccfbf1; color: #0d6e64; }
        .badge-dalam_pengiriman { background: #f3e8fd; color: #7627bb; }
        .badge-terkirim         { background: #e6f4ea; color: #137333; }
        .badge-dibatalkan       { background: #fce8e6; color: #c5221f; }
        .footer { margin-top: 16px; text-align: center; font-size: 9px; color: #9aa0a6; }
        .no-data { text-align: center; padding: 30px; color: #9aa0a6; font-size: 13px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Pengiriman Obat — MediTrack</h1>
    <p>RS Petrokimia Gresik &nbsp;·&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</div>

<div class="filter-bar">
    <div>Kurir: <strong>{{ $filters['kurir'] }}</strong></div>
    <div>Status: <strong>{{ $filters['status'] }}</strong></div>
    <div>Periode: <strong>{{ $filters['dari'] }} — {{ $filters['sampai'] }}</strong></div>
    <div>Total: <strong>{{ $prescriptions->count() }} data</strong></div>
</div>

@if($prescriptions->isEmpty())
<div class="no-data">Tidak ada data untuk filter yang dipilih.</div>
@else
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>No. Resep</th>
            <th>Tanggal</th>
            <th>Pasien</th>
            <th>Alamat Tujuan</th>
            <th>Kurir</th>
            <th>Kendaraan</th>
            <th>Status</th>
            <th>Terakhir Update</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prescriptions as $i => $p)
        <tr>
            <td style="color:#9aa0a6;">{{ $i + 1 }}</td>
            <td style="font-weight:600;">{{ $p->nomor_resep }}</td>
            <td>{{ $p->tanggal->format('d/m/Y') }}</td>
            <td>{{ $p->patient->name ?? '-' }}</td>
            <td style="font-size:10px;">
                @if($p->address)
                    {{ $p->address->label }}<br>
                    <span style="color:#5f6368;">{{ $p->address->address }}</span>
                @else -
                @endif
            </td>
            <td>{{ $p->courier->name ?? '-' }}</td>
            <td>{{ $p->courier->plate_number ?? '-' }}</td>
            <td><span class="badge badge-{{ $p->status }}">{{ $p->status_label }}</span></td>
            <td style="color:#5f6368;font-size:10px;">{{ $p->updated_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">MediTrack &copy; {{ date('Y') }} — RS Petrokimia Gresik</div>
</body>
</html>
