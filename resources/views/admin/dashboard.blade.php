@extends('layouts.admin')
@section('title', 'Dashboard')

@push('styles')
<style>
    .admin-stat-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; margin-bottom:20px; }
    .admin-status-grid { display:grid; grid-template-columns:1fr 320px; gap:16px; margin-bottom:20px; }
    .admin-status-inner { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
    @media (max-width: 900px) {
        .admin-status-grid { grid-template-columns:1fr; }
    }
    @media (max-width: 768px) {
        .admin-stat-grid { grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:14px; }
        .admin-status-inner { grid-template-columns:repeat(3,1fr); }
    }
    @media (max-width: 480px) {
        .admin-stat-grid { grid-template-columns:1fr 1fr; gap:8px; }
        .admin-status-inner { grid-template-columns:repeat(2,1fr); }
    }
</style>
@endpush

@section('content')

{{-- ── Stat Cards Row 1 ──────────────────────────────────────────────── --}}
<div class="admin-stat-grid">

    <div class="card" style="border-top:3px solid #1a73e8; padding:16px 20px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <div style="width:40px;height:40px;background:#e8f0fe;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <span class="material-icons" style="color:#1a73e8;font-size:20px;">receipt_long</span>
            </div>
            <div style="font-size:12px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Total Resep</div>
        </div>
        <div style="font-size:28px;font-weight:700;color:#202124;">{{ number_format($stats['total_resep']) }}</div>
        <div style="font-size:12px;color:#1a73e8;margin-top:4px;">
            <span class="material-icons" style="font-size:13px;vertical-align:-2px;">today</span>
            {{ $stats['resep_hari_ini'] }} hari ini
        </div>
    </div>

    <div class="card" style="border-top:3px solid #137333; padding:16px 20px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <div style="width:40px;height:40px;background:#e6f4ea;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <span class="material-icons" style="color:#137333;font-size:20px;">personal_injury</span>
            </div>
            <div style="font-size:12px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Total Pasien</div>
        </div>
        <div style="font-size:28px;font-weight:700;color:#202124;">{{ number_format($stats['total_pasien']) }}</div>
        <div style="font-size:12px;color:#5f6368;margin-top:4px;">Terdaftar di sistem</div>
    </div>

    <div class="card" style="border-top:3px solid #b06000; padding:16px 20px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <div style="width:40px;height:40px;background:#fef3e2;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <span class="material-icons" style="color:#b06000;font-size:20px;">delivery_dining</span>
            </div>
            <div style="font-size:12px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Kurir Aktif</div>
        </div>
        <div style="font-size:28px;font-weight:700;color:#202124;">{{ number_format($stats['total_kurir']) }}</div>
        <div style="font-size:12px;color:#5f6368;margin-top:4px;">Siap bertugas</div>
    </div>

    <div class="card" style="border-top:3px solid #0d9488; padding:16px 20px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <div style="width:40px;height:40px;background:#ccfbf1;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <span class="material-icons" style="color:#0d9488;font-size:20px;">inventory</span>
            </div>
            <div style="font-size:12px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Dibawa Kurir</div>
        </div>
        <div style="font-size:28px;font-weight:700;color:#202124;">{{ $stats['dibawa'] }}</div>
        <div style="font-size:12px;color:#5f6368;margin-top:4px;">Diambil, antri antar</div>
    </div>

    <div class="card" style="border-top:3px solid #7627bb; padding:16px 20px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <div style="width:40px;height:40px;background:#f3e8fd;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <span class="material-icons" style="color:#7627bb;font-size:20px;">local_shipping</span>
            </div>
            <div style="font-size:12px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Dalam Pengiriman</div>
        </div>
        <div style="font-size:28px;font-weight:700;color:#202124;">{{ $stats['dalam_pengiriman'] }}</div>
        <div style="font-size:12px;color:#5f6368;margin-top:4px;">Sedang dikirim sekarang</div>
    </div>
</div>

{{-- ── Status Resep + Shortcut ──────────────────────────────────────── --}}
<div class="admin-status-grid">

    {{-- Status Cards --}}
    <div class="card">
        <div style="font-size:14px;font-weight:500;color:#202124;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span class="material-icons" style="color:#1a73e8;font-size:18px;">bar_chart</span>
            Distribusi Status Resep
        </div>
        <div class="admin-status-inner">
            @foreach($statusData as $key => $sd)
            <a href="{{ route('admin.prescriptions.index') }}" style="text-decoration:none;">
                <div style="text-align:center; padding:12px 8px; border-radius:8px; border:1px solid #e0e0e0; transition:background 0.15s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
                    <div style="font-size:24px;font-weight:700;color:#202124;">{{ $sd['count'] }}</div>
                    <span class="badge badge-{{ $sd['color'] }}" style="margin-top:6px;font-size:11px;">{{ $sd['label'] }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="card">
        <div style="font-size:14px;font-weight:500;color:#202124;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <span class="material-icons" style="color:#1a73e8;font-size:18px;">bolt</span>
            Akses Cepat
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <a href="{{ route('admin.prescriptions.index') }}" class="nav-item" style="border-radius:8px;margin:0;">
                <span class="material-icons" style="color:#1a73e8;">receipt_long</span>
                <div>
                    <div style="font-size:13px;font-weight:500;color:#202124;">Catatan Resep</div>
                    <div style="font-size:11px;color:#5f6368;">Kelola pengiriman obat</div>
                </div>
            </a>
            <a href="{{ route('admin.couriers.index') }}" class="nav-item" style="border-radius:8px;margin:0;">
                <span class="material-icons" style="color:#b06000;">delivery_dining</span>
                <div>
                    <div style="font-size:13px;font-weight:500;color:#202124;">Data Kurir</div>
                    <div style="font-size:11px;color:#5f6368;">Kelola kurir pengiriman</div>
                </div>
            </a>
            <a href="{{ route('admin.patients.index') }}" class="nav-item" style="border-radius:8px;margin:0;">
                <span class="material-icons" style="color:#137333;">personal_injury</span>
                <div>
                    <div style="font-size:13px;font-weight:500;color:#202124;">Data Pasien</div>
                    <div style="font-size:11px;color:#5f6368;">Data & alamat pasien</div>
                </div>
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item" style="border-radius:8px;margin:0;">
                <span class="material-icons" style="color:#7627bb;">manage_accounts</span>
                <div>
                    <div style="font-size:13px;font-weight:500;color:#202124;">Manajemen User</div>
                    <div style="font-size:11px;color:#5f6368;">{{ $stats['total_user'] }} user terdaftar</div>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- ── Resep Terbaru ─────────────────────────────────────────────────── --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div style="font-size:14px;font-weight:500;color:#202124;display:flex;align-items:center;gap:8px;">
            <span class="material-icons" style="color:#1a73e8;font-size:18px;">history</span>
            Resep Terbaru
        </div>
        <a href="{{ route('admin.prescriptions.index') }}" style="font-size:13px;color:#1a73e8;text-decoration:none;font-weight:500;">
            Lihat semua <span class="material-icons" style="font-size:14px;vertical-align:-3px;">arrow_forward</span>
        </a>
    </div>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:1px solid #e0e0e0;">
                <th style="text-align:left;font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;padding:8px 12px;">Nomor Resep</th>
                <th style="text-align:left;font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;padding:8px 12px;">Tanggal</th>
                <th style="text-align:left;font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;padding:8px 12px;">Pasien</th>
                <th style="text-align:left;font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;padding:8px 12px;">Kurir</th>
                <th style="text-align:left;font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;padding:8px 12px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentPrescriptions as $p)
            <tr style="border-bottom:1px solid #f1f3f4;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background=''">
                <td style="padding:10px 12px;font-size:13px;font-weight:500;color:#1a73e8;">{{ $p->nomor_resep }}</td>
                <td style="padding:10px 12px;font-size:13px;color:#5f6368;">{{ $p->tanggal->format('d/m/Y') }}</td>
                <td style="padding:10px 12px;font-size:13px;color:#202124;">{{ $p->patient->name ?? '-' }}</td>
                <td style="padding:10px 12px;font-size:13px;color:#202124;">
                    @if($p->courier)
                        <span style="display:flex;align-items:center;gap:4px;">
                            <span class="material-icons" style="font-size:14px;color:#b06000;">delivery_dining</span>
                            {{ $p->courier->name }}
                        </span>
                    @else
                        <span style="color:#9aa0a6;font-style:italic;">Belum ditugaskan</span>
                    @endif
                </td>
                <td style="padding:10px 12px;">
                    <span class="badge badge-{{ $p->status_color }}">{{ $p->status_label }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:24px;text-align:center;color:#9aa0a6;font-size:13px;">
                    <span class="material-icons" style="font-size:32px;display:block;margin-bottom:6px;">inbox</span>
                    Belum ada data resep
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
