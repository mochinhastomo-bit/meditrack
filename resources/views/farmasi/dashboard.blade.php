@extends('layouts.admin')
@section('title', 'Dashboard Farmasi')

@push('styles')
<style>
    .kanban-col {
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        min-height: 400px;
        max-height: calc(100vh - 260px);
    }
    .kanban-header {
        padding: 14px 16px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        border-radius: 10px 10px 0 0;
        position: sticky;
        top: 0;
    }
    .kanban-header-left { display: flex; align-items: center; gap: 8px; }
    .kanban-count {
        min-width: 22px; height: 22px;
        border-radius: 11px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
        padding: 0 6px;
    }
    .kanban-body {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .kanban-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        transition: box-shadow 0.2s, transform 0.2s, opacity 0.3s;
    }
    .kanban-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .kanban-card.moving {
        opacity: 0;
        transform: translateY(-12px);
    }
    .kanban-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 32px 16px;
        color: #9aa0a6;
    }
    .btn-serahkan {
        width: 100%; margin-top: 10px;
        padding: 8px 12px; border: none; border-radius: 6px;
        background: #1a73e8; color: #fff;
        font-size: 12px; font-weight: 500; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        font-family: 'Google Sans', sans-serif;
        transition: background 0.15s;
    }
    .btn-serahkan:hover { background: #1557b0; }
    .btn-serahkan:disabled { background: #9aa0a6; cursor: not-allowed; }
    .btn-batalkan {
        padding: 5px 10px; border-radius: 6px;
        border: 1px solid #dadce0; background: #fff; color: #5f6368;
        font-size: 11px; font-weight: 500; cursor: pointer;
        display: inline-flex; align-items: center; gap: 4px;
        font-family: 'Google Sans', sans-serif;
        transition: background 0.15s;
    }
    .btn-batalkan:hover { background: #fce8e6; border-color: #c5221f; color: #c5221f; }
    .dot-pulse { width:8px; height:8px; border-radius:50%; display:inline-block; }
    .dot-orange { background:#b06000; animation: blink 1.5s infinite; }
    .dot-blue   { background:#1a73e8; animation: blink 1.5s infinite; }
    .dot-purple { background:#7627bb; animation: blink 1.5s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

    /* Search bar di kolom penyiapan */
    .col-search-wrap {
        padding: 8px 12px;
        border-bottom: 1px solid #e0e0e0;
        background: #fff;
        position: relative;
    }
    .col-search {
        width: 100%; box-sizing: border-box;
        border: 1px solid #dadce0; border-radius: 20px;
        padding: 6px 32px 6px 32px;
        font-size: 12px; font-family: 'Google Sans', sans-serif;
        color: #202124; outline: none;
        transition: border 0.15s, box-shadow 0.15s;
        background: #f8f9fa;
    }
    .col-search:focus {
        border-color: #1a73e8;
        box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
        background: #fff;
    }
    .col-search-icon {
        position: absolute; left: 22px; top: 50%;
        transform: translateY(-50%);
        font-size: 16px; color: #9aa0a6; pointer-events: none;
    }
    .col-search-clear {
        position: absolute; right: 22px; top: 50%;
        transform: translateY(-50%);
        font-size: 16px; color: #9aa0a6; cursor: pointer;
        display: none;
        background: none; border: none; padding: 0;
        line-height: 1;
    }
    .col-search-clear:hover { color: #c5221f; }
    .search-result-info {
        padding: 4px 12px 0;
        font-size: 11px; color: #9aa0a6;
        display: none;
    }

    /* Kartu compact */
    .kanban-card-compact {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 7px;
        padding: 8px 10px;
        transition: box-shadow 0.2s, transform 0.2s, opacity 0.3s;
        cursor: pointer;
    }
    .kanban-card-compact:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
    .kanban-card-compact.moving { opacity: 0; transform: translateY(-12px); }
    .compact-body { display: none; margin-top: 8px; padding-top: 8px; border-top: 1px solid #f1f3f4; }
    .kanban-card-compact.expanded .compact-body { display: block; }
    .compact-chevron {
        transition: transform 0.2s;
        font-size: 16px; color: #9aa0a6;
    }
    .kanban-card-compact.expanded .compact-chevron { transform: rotate(180deg); }
    .card-hidden { display: none !important; }
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .kanban-board-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 768px) {
        .stat-cards-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }
        .kanban-board-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .kanban-col {
            max-height: none;
            min-height: 200px;
        }
    }
    @media (max-width: 480px) {
        .stat-cards-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')

{{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
<div class="stat-cards-grid">

    <div class="card" style="border-top:3px solid #1a73e8; padding:12px 16px;">
        <div style="font-size:11px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
            <span class="material-icons" style="font-size:13px;vertical-align:-2px;color:#1a73e8;">today</span> Hari Ini
        </div>
        <div style="font-size:24px;font-weight:700;color:#202124;" id="stat-hari-ini">{{ $stats['resep_hari_ini'] }}</div>
        <div style="font-size:11px;color:#5f6368;margin-top:2px;">Resep masuk</div>
    </div>

    <div class="card" style="border-top:3px solid #b06000; padding:12px 16px;">
        <div style="font-size:11px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
            <span class="material-icons" style="font-size:13px;vertical-align:-2px;color:#b06000;">hourglass_empty</span> Penyiapan
        </div>
        <div style="font-size:24px;font-weight:700;color:#202124;" id="stat-penyiapan">{{ $stats['penyiapan'] }}</div>
        <div style="font-size:11px;color:#5f6368;margin-top:2px;">Sedang disiapkan</div>
    </div>

    <div class="card" style="border-top:3px solid #1a73e8; padding:12px 16px;">
        <div style="font-size:11px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
            <span class="material-icons" style="font-size:13px;vertical-align:-2px;color:#1a73e8;">inventory_2</span> Siap Kirim
        </div>
        <div style="font-size:24px;font-weight:700;color:#202124;" id="stat-siap-kirim">{{ $stats['siap_kirim'] }}</div>
        <div style="font-size:11px;color:#5f6368;margin-top:2px;">Menunggu kurir</div>
    </div>

    <div class="card" style="border-top:3px solid #0d9488; padding:12px 16px;">
        <div style="font-size:11px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
            <span class="material-icons" style="font-size:13px;vertical-align:-2px;color:#0d9488;">inventory</span> Dibawa Kurir
        </div>
        <div style="font-size:24px;font-weight:700;color:#202124;" id="stat-dibawa">{{ $stats['dibawa'] }}</div>
        <div style="font-size:11px;color:#5f6368;margin-top:2px;">Diambil, antri antar</div>
    </div>

    <div class="card" style="border-top:3px solid #7627bb; padding:12px 16px;">
        <div style="font-size:11px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
            <span class="material-icons" style="font-size:13px;vertical-align:-2px;color:#7627bb;">local_shipping</span> Pengiriman
        </div>
        <div style="font-size:24px;font-weight:700;color:#202124;" id="stat-pengiriman">{{ $stats['dalam_pengiriman'] }}</div>
        <div style="font-size:11px;color:#5f6368;margin-top:2px;">Sedang dikirim</div>
    </div>

    <div class="card" style="border-top:3px solid #137333; padding:12px 16px;">
        <div style="font-size:11px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
            <span class="material-icons" style="font-size:13px;vertical-align:-2px;color:#137333;">check_circle</span> Terkirim
        </div>
        <div style="font-size:24px;font-weight:700;color:#202124;">{{ $stats['terkirim_hari_ini'] }}</div>
        <div style="font-size:11px;color:#5f6368;margin-top:2px;">Berhasil hari ini</div>
    </div>
</div>

{{-- ── Kanban Board ─────────────────────────────────────────────────────── --}}
<div class="kanban-board-grid">

    {{-- ════ KOLOM 1 : PENYIAPAN ════ --}}
    <div class="kanban-col">
        <div class="kanban-header">
            <div class="kanban-header-left">
                <span class="dot-pulse dot-orange"></span>
                <span style="font-size:13px;font-weight:500;color:#202124;">Proses Penyiapan</span>
            </div>
            <span class="kanban-count" style="background:#fef3e2;color:#b06000;" id="count-penyiapan">{{ $kolPenyiapan->count() }}</span>
        </div>

        {{-- Search bar --}}
        <div class="col-search-wrap">
            <span class="material-icons col-search-icon">search</span>
            <input
                type="text"
                id="search-penyiapan"
                class="col-search"
                placeholder="Cari kode resep atau nama pasien..."
                oninput="filterPenyiapan(this.value)"
                autocomplete="off">
            <button class="col-search-clear" id="btn-clear-search" onclick="clearSearch()" title="Hapus pencarian">
                <span class="material-icons" style="font-size:16px;">close</span>
            </button>
        </div>
        <div class="search-result-info" id="search-result-info"></div>

        <div class="kanban-body" id="col-penyiapan">
            @forelse($kolPenyiapan as $p)
            <div class="kanban-card-compact"
                id="card-{{ $p->id }}"
                data-id="{{ $p->id }}"
                data-nomor="{{ strtolower($p->nomor_resep) }}"
                data-pasien="{{ strtolower($p->patient->name ?? '') }}"
                data-nomor-display="{{ $p->nomor_resep }}"
                data-pasien-display="{{ $p->patient->name ?? '-' }}"
                data-tanggal="{{ $p->tanggal->format('d/m/Y') }}"
                onclick="toggleExpand(this)">
                @include('farmasi._card_penyiapan_compact', ['p' => $p])
            </div>
            @empty
            <div class="kanban-empty" id="empty-penyiapan">
                <span class="material-icons" style="font-size:36px;margin-bottom:6px;">done_all</span>
                <div style="font-size:13px;">Tidak ada antrian</div>
            </div>
            @endforelse

            {{-- State saat pencarian tidak menemukan hasil --}}
            <div id="no-search-result" style="display:none;text-align:center;padding:24px 12px;color:#9aa0a6;">
                <span class="material-icons" style="font-size:32px;display:block;margin-bottom:6px;">search_off</span>
                <div style="font-size:12px;">Tidak ditemukan</div>
            </div>
        </div>
    </div>

    {{-- ════ KOLOM 2 : SIAP KIRIM ════ --}}
    <div class="kanban-col">
        <div class="kanban-header">
            <div class="kanban-header-left">
                <span class="dot-pulse dot-blue"></span>
                <span style="font-size:13px;font-weight:500;color:#202124;">Siap Kirim</span>
            </div>
            <span class="kanban-count" style="background:#e8f0fe;color:#1a73e8;" id="count-siap-kirim">{{ $kolSiapKirim->count() }}</span>
        </div>
        <div class="kanban-body" id="col-siap-kirim">
            @forelse($kolSiapKirim as $p)
            <div class="kanban-card" id="card-{{ $p->id }}" data-id="{{ $p->id }}" data-nomor="{{ $p->nomor_resep }}" data-pasien="{{ $p->patient->name ?? '-' }}" data-tanggal="{{ $p->tanggal->format('d/m/Y') }}">
                @include('farmasi._card_siap_kirim', ['p' => $p])
            </div>
            @empty
            <div class="kanban-empty" id="empty-siap-kirim">
                <span class="material-icons" style="font-size:36px;margin-bottom:6px;">inventory_2</span>
                <div style="font-size:13px;">Belum ada yang siap kirim</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ════ KOLOM 3 : DIBAWA KURIR ════ --}}
    <div class="kanban-col">
        <div class="kanban-header">
            <div class="kanban-header-left">
                <span class="dot-pulse" style="background:#0d9488;"></span>
                <span style="font-size:13px;font-weight:500;color:#202124;">Dibawa Kurir</span>
            </div>
            <span class="kanban-count" style="background:#ccfbf1;color:#0d9488;" id="count-dibawa">{{ $kolDibawa->count() }}</span>
        </div>
        <div class="kanban-body" id="col-dibawa">
            @forelse($kolDibawa as $p)
            <div class="kanban-card" id="card-{{ $p->id }}" data-id="{{ $p->id }}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px;">
                    <div style="font-size:13px;font-weight:600;color:#202124;">{{ $p->nomor_resep }}</div>
                    <span style="font-size:10px;font-weight:600;background:#ccfbf1;color:#0d9488;padding:2px 8px;border-radius:10px;white-space:nowrap;">Dibawa</span>
                </div>
                <div style="font-size:12px;color:#5f6368;">👤 {{ $p->patient->name ?? '-' }}</div>
                @if($p->courier)
                <div style="font-size:12px;color:#5f6368;margin-top:4px;">🛵 {{ $p->courier->name }} · {{ $p->courier->plate_number }}</div>
                @endif
            </div>
            @empty
            <div class="kanban-empty" id="empty-dibawa">
                <span class="material-icons" style="font-size:36px;margin-bottom:6px;">inventory</span>
                <div style="font-size:13px;">Belum ada yang dibawa</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ════ KOLOM 4 : DALAM PENGIRIMAN ════ --}}
    <div class="kanban-col">
        <div class="kanban-header">
            <div class="kanban-header-left">
                <span class="dot-pulse dot-purple"></span>
                <span style="font-size:13px;font-weight:500;color:#202124;">Dalam Pengiriman</span>
            </div>
            <span class="kanban-count" style="background:#f3e8fd;color:#7627bb;" id="count-pengiriman">{{ $kolPengiriman->count() }}</span>
        </div>
        <div class="kanban-body" id="col-pengiriman">
            @forelse($kolPengiriman as $p)
            <div class="kanban-card" id="card-{{ $p->id }}" data-id="{{ $p->id }}">
                @include('farmasi._card_pengiriman', ['p' => $p])
            </div>
            @empty
            <div class="kanban-empty" id="empty-pengiriman">
                <span class="material-icons" style="font-size:36px;margin-bottom:6px;">local_shipping</span>
                <div style="font-size:13px;">Tidak ada pengiriman aktif</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Footer bar: link resep + indikator refresh --}}
<div style="margin-top:14px;display:flex;align-items:center;justify-content:space-between;">
    <div id="refresh-indicator" style="display:flex;align-items:center;gap:6px;font-size:12px;color:#5f6368;">
        <span id="refresh-dot" style="width:8px;height:8px;border-radius:50%;background:#137333;display:inline-block;flex-shrink:0;"></span>
        <span id="refresh-text">Live · refresh tiap 15 detik</span>
    </div>
    <a href="{{ route('farmasi.prescriptions.index') }}" style="font-size:13px;color:#1a73e8;text-decoration:none;font-weight:500;">
        <span class="material-icons" style="font-size:15px;vertical-align:-3px;">open_in_new</span>
        Kelola Semua Resep
    </a>
</div>

{{-- ══ MODAL TRACKING KURIR ══════════════════════════════════════════════ --}}
<div id="trackModal" class="fixed inset-0 z-50 hidden" style="background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;width:92%;max-width:860px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 12px 40px rgba(0,0,0,0.2);overflow:hidden;">

        {{-- Header --}}
        <div style="padding:16px 20px;border-bottom:1px solid #e0e0e0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div>
                <div style="font-size:16px;font-weight:500;color:#202124;display:flex;align-items:center;gap:8px;">
                    <span class="material-icons" style="color:#7627bb;font-size:20px;">local_shipping</span>
                    <span id="tm-nomor">—</span>
                </div>
                <div style="font-size:12px;color:#5f6368;margin-top:3px;" id="tm-sub">Tracking lokasi kurir real-time</div>
            </div>
            <button onclick="closeTrackModal()" style="background:none;border:none;cursor:pointer;color:#5f6368;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;transition:background 0.15s;" onmouseover="this.style.background='#f1f3f4'" onmouseout="this.style.background='none'">
                <span class="material-icons">close</span>
            </button>
        </div>

        {{-- Body --}}
        <div style="display:grid;grid-template-columns:1fr 260px;flex:1;overflow:hidden;">

            {{-- Peta --}}
            <div style="position:relative;">
                <div id="track-modal-map" style="width:100%;height:100%;min-height:420px;background:#f1f3f4;"></div>

                {{-- Badge live --}}
                <div id="tm-live-badge" style="position:absolute;top:12px;left:12px;background:#fff;border:1px solid #e0e0e0;border-radius:20px;padding:5px 12px;font-size:12px;color:#5f6368;display:flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                    <span id="tm-live-dot" style="width:8px;height:8px;border-radius:50%;background:#137333;flex-shrink:0;"></span>
                    <span id="tm-live-text">Menghubungkan...</span>
                </div>

                {{-- Tombol center --}}
                <button onclick="centerTrackMap()" style="position:absolute;bottom:14px;right:14px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:8px 12px;font-size:12px;color:#3c4043;display:flex;align-items:center;gap:6px;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.1);font-family:'Google Sans',sans-serif;transition:background 0.15s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
                    <span class="material-icons" style="font-size:16px;">my_location</span> Pusatkan
                </button>
            </div>

            {{-- Panel info --}}
            <div style="border-left:1px solid #e0e0e0;padding:16px;overflow-y:auto;display:flex;flex-direction:column;gap:14px;">

                {{-- Info kurir --}}
                <div>
                    <div style="font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Kurir</div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;background:#fef3e2;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#b06000;font-size:16px;">person</span>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:500;color:#202124;" id="tm-kurir-nama">—</div>
                                <div style="font-size:11px;color:#5f6368;" id="tm-kurir-plat">—</div>
                            </div>
                        </div>
                        <a id="tm-kurir-phone-link" href="#" style="display:flex;align-items:center;gap:6px;font-size:12px;color:#1a73e8;text-decoration:none;padding:6px 8px;border-radius:6px;background:#e8f0fe;">
                            <span class="material-icons" style="font-size:14px;">phone</span>
                            <span id="tm-kurir-phone">—</span>
                        </a>
                    </div>
                </div>

                <div style="border-top:1px solid #f1f3f4;"></div>

                {{-- Info tujuan --}}
                <div>
                    <div style="font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Tujuan Pengiriman</div>
                    <div style="display:flex;align-items:flex-start;gap:8px;">
                        <div style="width:32px;height:32px;background:#fce8e6;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <span class="material-icons" style="color:#c5221f;font-size:16px;">location_on</span>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:500;color:#202124;" id="tm-dest-label">—</div>
                            <div style="font-size:11px;color:#5f6368;line-height:1.4;margin-top:2px;" id="tm-dest-address">—</div>
                        </div>
                    </div>
                </div>

                <div style="border-top:1px solid #f1f3f4;"></div>

                {{-- Jarak & ETA dari Directions API --}}
                <div id="tm-route-box" style="display:none;gap:8px;">
                    <div style="flex:1;background:#e8f0fe;border-radius:8px;padding:10px 12px;text-align:center;">
                        <div style="font-size:10px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;">
                            <span class="material-icons" style="font-size:12px;vertical-align:-2px;">straighten</span> Jarak
                        </div>
                        <div style="font-size:16px;font-weight:700;color:#1a73e8;" id="tm-route-dist">—</div>
                    </div>
                    <div style="flex:1;background:#e6f4ea;border-radius:8px;padding:10px 12px;text-align:center;">
                        <div style="font-size:10px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;">
                            <span class="material-icons" style="font-size:12px;vertical-align:-2px;">schedule</span> Estimasi
                        </div>
                        <div style="font-size:16px;font-weight:700;color:#137333;" id="tm-route-eta">—</div>
                    </div>
                </div>

                <div style="border-top:1px solid #f1f3f4;"></div>

                {{-- Koordinat GPS kurir --}}
                <div>
                    <div style="font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Posisi GPS Kurir</div>
                    <div style="background:#f8f9fa;border-radius:6px;padding:8px 10px;">
                        <div style="font-size:11px;font-family:monospace;color:#202124;" id="tm-coords">Menunggu data GPS...</div>
                        <div style="font-size:11px;color:#9aa0a6;margin-top:3px;" id="tm-last-seen">—</div>
                    </div>
                </div>

                {{-- Status GPS --}}
                <div id="tm-gps-warning" style="display:none;padding:8px 10px;background:#fef3e2;border-radius:6px;font-size:11px;color:#b06000;display:flex;align-items:flex-start;gap:6px;line-height:1.4;">
                    <span class="material-icons" style="font-size:14px;flex-shrink:0;margin-top:1px;">info</span>
                    <span>Kurir belum mengirim data GPS. Pastikan aplikasi Android aktif.</span>
                </div>

                {{-- Tombol lihat di halaman penuh --}}
                <a id="tm-full-link" href="#" target="_blank" class="btn-secondary" style="justify-content:center;font-size:12px;text-decoration:none;">
                    <span class="material-icons" style="font-size:15px;">open_in_new</span>
                    Buka Halaman Tracking
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════════════════
//  KANBAN AUTO-REFRESH ENGINE
//  - 1 request setiap 15 detik untuk ketiga kolom sekaligus
//  - Smart diff: hanya kartu baru yang ditambah, yang hilang dihapus
//  - Kolom penyiapan: skip refresh jika ada kartu yang sedang di-expand
// ══════════════════════════════════════════════════════════════════════════

const REFRESH_INTERVAL = 15000; // 15 detik
let   refreshTimer     = null;
let   isPaused         = false;   // pause saat user sedang interaksi AJAX
let   lastRefresh      = null;

// ── Indikator refresh di pojok kanan bawah ────────────────────────────────
function setRefreshStatus(state) {
    // state: 'ok' | 'loading' | 'error'
    const dot = document.getElementById('refresh-dot');
    const txt = document.getElementById('refresh-text');
    if (!dot || !txt) return;
    if (state === 'loading') {
        dot.style.background = '#1a73e8'; txt.textContent = 'Memperbarui...';
    } else if (state === 'error') {
        dot.style.background = '#c5221f'; txt.textContent = 'Gagal refresh';
    } else {
        dot.style.background = '#137333';
        const now = new Date();
        txt.textContent = 'Diperbarui ' + now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
    }
}

// ── Animasi masuk kartu baru ──────────────────────────────────────────────
function animateIn(el) {
    el.style.opacity   = '0';
    el.style.transform = 'translateY(10px)';
    el.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        el.style.opacity   = '1';
        el.style.transform = 'translateY(0)';
    }));
}

function animateOut(el, cb) {
    el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
    el.style.opacity    = '0';
    el.style.transform  = 'translateY(-8px)';
    setTimeout(cb, 280);
}

// ── Sync satu kolom (smart diff) ──────────────────────────────────────────
function syncColumn(colId, emptyId, countId, statId, items, buildFn, skipIfExpanded, updateFn = null) {
    const col   = document.getElementById(colId);
    const empty = document.getElementById(emptyId);
    if (!col) return;

    // Jika kolom penyiapan dan ada kartu yang sedang expand → skip (user sedang baca)
    if (skipIfExpanded && col.querySelector('.kanban-card-compact.expanded')) return;

    const serverIds = new Set(items.map(p => p.id));
    const domCards  = col.querySelectorAll('[data-id]');

    // Hapus kartu yang sudah tidak ada di server
    domCards.forEach(card => {
        const id = parseInt(card.dataset.id);
        if (!serverIds.has(id)) {
            animateOut(card, () => card.remove());
        }
    });

    // Tambah kartu baru / update kartu yang sudah ada
    items.forEach(p => {
        const existing = document.getElementById('card-' + p.id);
        if (!existing) {
            const el = buildFn(p);
            el.id = 'card-' + p.id;
            el.dataset.id = p.id;
            col.appendChild(el);
            animateIn(el);
        } else if (typeof updateFn === 'function') {
            updateFn(existing, p);
        }
    });

    // Update counter
    const count = items.length;
    if (countId) { const el = document.getElementById(countId); if (el) el.textContent = count; }
    if (statId)  { const el = document.getElementById(statId);  if (el) el.textContent = count; }

    // Tampilkan empty state jika kosong
    if (empty) empty.style.display = count === 0 ? 'flex' : 'none';
}

// ── Builder: kartu Penyiapan (compact) ────────────────────────────────────
function buildPenyiapanCard(p) {
    const div = document.createElement('div');
    div.className = 'kanban-card-compact';
    div.dataset.nomor        = p.nomor_resep.toLowerCase();
    div.dataset.pasien       = p.patient_name.toLowerCase();
    div.dataset.nomorDisplay = p.nomor_resep;
    div.dataset.pasienDisplay= p.patient_name;
    div.dataset.tanggal      = p.tanggal;
    div.setAttribute('onclick', 'toggleExpand(this)');
    div.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
            <div style="min-width:0;flex:1;">
                <div data-role="nomor" style="font-size:12px;font-weight:600;color:#202124;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${p.nomor_resep}</div>
                <div style="font-size:11px;color:#5f6368;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <span class="material-icons" style="font-size:11px;vertical-align:-1px;">person</span>
                    ${p.patient_name} &nbsp;·&nbsp;
                    <span class="material-icons" style="font-size:11px;vertical-align:-1px;">calendar_today</span>
                    ${p.tanggal}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                <span class="badge badge-orange" style="font-size:10px;padding:2px 7px;">Penyiapan</span>
                <span class="material-icons compact-chevron">expand_more</span>
            </div>
        </div>
        <div class="compact-body">
            ${p.keterangan ? `<div style="font-size:11px;color:#5f6368;margin-bottom:8px;padding:6px 8px;background:#f8f9fa;border-radius:4px;line-height:1.4;">
                <span class="material-icons" style="font-size:12px;vertical-align:-2px;">notes</span> ${p.keterangan}
            </div>` : ''}
            <button class="btn-serahkan" onclick="event.stopPropagation(); serahkanKurir(${p.id})">
                <span class="material-icons" style="font-size:14px;">local_shipping</span>
                Siap Kirim
            </button>
        </div>`;
    return div;
}

// ── Updater: kartu Pengiriman yang sudah ada di DOM ───────────────────────
function updatePengirimanCard(card, p) {
    // Update dataset koordinat (agar openTrackModal selalu dapat data terbaru)
    card.dataset.courierLat  = p.courier_lat   || '';
    card.dataset.courierLng  = p.courier_lng   || '';
    card.dataset.destLat     = p.dest_lat      || '';
    card.dataset.destLng     = p.dest_lng      || '';
    card.dataset.destLabel   = p.dest_label    || '';
    card.dataset.destAddress = p.dest_address  || '';
    card.dataset.phone       = p.courier_phone || '';

    // Update indikator GPS (div terakhir di dalam kartu)
    const gpsEl = card.querySelector('[data-role="gps-indicator"]');
    if (!gpsEl) return;

    if (p.has_gps) {
        // Jika sebelumnya "menunggu" → flash hijau sebentar
        const wasWaiting = gpsEl.dataset.state !== 'active';
        gpsEl.dataset.state = 'active';
        gpsEl.style.background = '#f3e8fd';
        gpsEl.style.color      = '#7627bb';
        gpsEl.innerHTML = `<span class="material-icons" style="font-size:12px;">gps_fixed</span>
            GPS aktif · ${p.last_seen}`;
        if (wasWaiting) {
            gpsEl.style.transition = 'background 0.4s';
            gpsEl.style.background = '#e6f4ea';
            setTimeout(() => { gpsEl.style.background = '#f3e8fd'; }, 800);
        }
    } else {
        gpsEl.dataset.state = 'waiting';
        gpsEl.style.background = '#f1f3f4';
        gpsEl.style.color      = '#9aa0a6';
        gpsEl.innerHTML = `<span class="material-icons" style="font-size:12px;">gps_off</span>
            Menunggu sinyal GPS kurir`;
    }
}

// ── Builder: kartu Siap Kirim ─────────────────────────────────────────────
function buildSiapKirimCard(p) {
    const div = document.createElement('div');
    div.className = 'kanban-card';
    div.dataset.nomor  = p.nomor_resep;
    div.dataset.pasien = p.patient_name;
    div.dataset.tanggal= p.tanggal;
    div.innerHTML = `
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
            <div>
                <div style="font-size:13px;font-weight:500;color:#202124;">${p.nomor_resep}</div>
                <div style="font-size:12px;color:#5f6368;margin-top:3px;">
                    <span class="material-icons" style="font-size:12px;vertical-align:-2px;">person</span>
                    ${p.patient_name} &nbsp;·&nbsp;
                    <span class="material-icons" style="font-size:12px;vertical-align:-2px;">calendar_today</span>
                    ${p.tanggal}
                </div>
            </div>
            <span class="badge badge-blue" style="white-space:nowrap;flex-shrink:0;">Siap Kirim</span>
        </div>
        <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;gap:6px;">
            <div style="flex:1;padding:6px 10px;border-radius:6px;background:#e6f4ea;font-size:12px;color:#137333;font-weight:500;display:flex;align-items:center;gap:4px;">
                <span class="material-icons" style="font-size:14px;">check_circle</span> Sudah diserahkan
            </div>
            <button class="btn-batalkan" onclick="batalkan(${p.id})">
                <span class="material-icons" style="font-size:13px;">undo</span> Batalkan
            </button>
        </div>`;
    return div;
}

// ── Builder: kartu Dibawa Kurir ───────────────────────────────────────────
function buildDibawaCard(p) {
    const div = document.createElement('div');
    div.className = 'kanban-card';
    div.innerHTML = `
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px;">
            <div style="font-size:13px;font-weight:600;color:#202124;">${p.nomor_resep}</div>
            <span style="font-size:10px;font-weight:600;background:#ccfbf1;color:#0d9488;padding:2px 8px;border-radius:10px;white-space:nowrap;">Dibawa</span>
        </div>
        <div style="font-size:12px;color:#5f6368;">👤 ${p.patient_name}</div>
        ${p.courier_name ? `<div style="font-size:12px;color:#5f6368;margin-top:4px;">🛵 ${p.courier_name} · ${p.plate_number ?? ''}</div>` : ''}`;
    return div;
}

// ── Builder: kartu Dalam Pengiriman ──────────────────────────────────────
function buildPengirimanCard(p) {
    const div = document.createElement('div');
    div.className = 'kanban-card';
    div.style.cursor = 'pointer';
    // Simpan semua data sebagai dataset agar openTrackModal bisa membacanya
    div.dataset.nomor       = p.nomor_resep;
    div.dataset.pasien      = p.patient_name;
    div.dataset.kurir       = p.courier_name  || '-';
    div.dataset.plat        = p.plate_number  || '-';
    div.dataset.phone       = p.courier_phone || '';
    div.dataset.courierLat  = p.courier_lat   || '';
    div.dataset.courierLng  = p.courier_lng   || '';
    div.dataset.destLat     = p.dest_lat      || '';
    div.dataset.destLng     = p.dest_lng      || '';
    div.dataset.destLabel   = p.dest_label    || '';
    div.dataset.destAddress = p.dest_address  || '';
    div.setAttribute('onclick', 'openTrackModal(this)');

    div.innerHTML = `
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:500;color:#202124;">${p.nomor_resep}</div>
                <div style="font-size:12px;color:#5f6368;margin-top:2px;">
                    <span class="material-icons" style="font-size:12px;vertical-align:-2px;">person</span>
                    ${p.patient_name}
                </div>
                ${p.courier_name ? `<div style="font-size:12px;color:#b06000;margin-top:2px;">
                    <span class="material-icons" style="font-size:12px;vertical-align:-2px;">delivery_dining</span>
                    ${p.courier_name} · ${p.plate_number}
                </div>` : ''}
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                <span class="badge badge-purple">Dikirim</span>
                <span style="font-size:11px;color:#1a73e8;font-weight:500;display:flex;align-items:center;gap:2px;">
                    <span class="material-icons" style="font-size:12px;">location_on</span> Lihat Peta
                </span>
            </div>
        </div>
        <div data-role="gps-indicator" data-state="${p.has_gps ? 'active' : 'waiting'}"
            style="margin-top:8px;padding:5px 8px;border-radius:5px;font-size:11px;display:flex;align-items:center;gap:4px;
                   background:${p.has_gps ? '#f3e8fd' : '#f1f3f4'};color:${p.has_gps ? '#7627bb' : '#9aa0a6'};">
            <span class="material-icons" style="font-size:12px;">${p.has_gps ? 'gps_fixed' : 'gps_off'}</span>
            ${p.has_gps ? `GPS aktif · ${p.last_seen}` : 'Menunggu sinyal GPS kurir'}
        </div>`;
    return div;
}

// ── Fungsi utama refresh ──────────────────────────────────────────────────
function refreshKanban() {
    if (isPaused) return;
    setRefreshStatus('loading');

    $.get('/farmasi/dashboard/kanban-json')
        .done(function(res) {
            syncColumn('col-penyiapan',  'empty-penyiapan',  'count-penyiapan', 'stat-penyiapan',
                res.penyiapan,  buildPenyiapanCard,  true);   // true = skip jika ada yg expand

            syncColumn('col-siap-kirim', 'empty-siap-kirim', 'count-siap-kirim', 'stat-siap-kirim',
                res.siap_kirim, buildSiapKirimCard,  false);

            syncColumn('col-dibawa', 'empty-dibawa', 'count-dibawa', 'stat-dibawa',
                res.dibawa, buildDibawaCard, false);

            syncColumn('col-pengiriman', 'empty-pengiriman', 'count-pengiriman', 'stat-pengiriman',
                res.pengiriman, buildPengirimanCard,  false, updatePengirimanCard);

            // Update stat cards atas
            const s = res.stats;
            setText('stat-penyiapan',  s.penyiapan);
            setText('stat-siap-kirim', s.siap_kirim);
            setText('stat-dibawa',     s.dibawa);
            setText('stat-pengiriman', s.dalam_pengiriman);
            setText('stat-hari-ini',   s.resep_hari_ini);

            // Re-apply filter search jika aktif
            const q = document.getElementById('search-penyiapan')?.value || '';
            if (q.trim()) filterPenyiapan(q);

            setRefreshStatus('ok');
            lastRefresh = new Date();
        })
        .fail(function() {
            setRefreshStatus('error');
        });
}

function setText(id, val) {
    const el = document.getElementById(id); if (el) el.textContent = val;
}

// Mulai interval
refreshTimer = setInterval(refreshKanban, REFRESH_INTERVAL);

// ══════════════════════════════════════════════════════════════════════════
//  SEARCH & FILTER (kolom Penyiapan)
// ══════════════════════════════════════════════════════════════════════════
function filterPenyiapan(q) {
    const query    = q.trim().toLowerCase();
    const col      = document.getElementById('col-penyiapan');
    const cards    = col.querySelectorAll('.kanban-card-compact');
    const noResult = document.getElementById('no-search-result');
    const clearBtn = document.getElementById('btn-clear-search');
    const infoEl   = document.getElementById('search-result-info');

    clearBtn.style.display = query ? 'block' : 'none';

    let visible = 0;
    cards.forEach(card => {
        const match = !query
            || (card.dataset.nomor  || '').includes(query)
            || (card.dataset.pasien || '').includes(query);
        card.classList.toggle('card-hidden', !match);
        if (match) { highlightText(card, query); visible++; }
    });

    noResult.style.display = (query && visible === 0) ? 'block' : 'none';
    infoEl.style.display   = (query && cards.length > 0) ? 'block' : 'none';
    if (query && cards.length > 0) infoEl.textContent = `Menampilkan ${visible} dari ${cards.length} resep`;
}

function clearSearch() {
    const inp = document.getElementById('search-penyiapan');
    if (inp) { inp.value = ''; inp.focus(); }
    filterPenyiapan('');
}

function highlightText(card, query) {
    const el = card.querySelector('[data-role="nomor"]');
    if (!el) return;
    const orig = card.dataset.nomorDisplay || '';
    if (!query) { el.innerHTML = orig; return; }
    const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi');
    el.innerHTML = orig.replace(re, '<mark style="background:#fef3e2;color:#b06000;border-radius:2px;padding:0 1px;">$1</mark>');
}

// ══════════════════════════════════════════════════════════════════════════
//  TOGGLE EXPAND KARTU COMPACT
// ══════════════════════════════════════════════════════════════════════════
function toggleExpand(card) {
    if (!card || !card.classList.contains('kanban-card-compact')) return;
    const wasExpanded = card.classList.contains('expanded');
    document.querySelectorAll('.kanban-card-compact.expanded').forEach(c => c.classList.remove('expanded'));
    if (!wasExpanded) card.classList.add('expanded');
}

// ══════════════════════════════════════════════════════════════════════════
//  AKSI FARMASI
// ══════════════════════════════════════════════════════════════════════════
function serahkanKurir(id) {
    isPaused = true;
    const card = document.getElementById('card-' + id);
    const btn  = card?.querySelector('.btn-serahkan');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="material-icons" style="font-size:14px;">hourglass_empty</span> Memproses...'; }

    $.ajax({
        url: '/farmasi/prescriptions/' + id + '/quick-status',
        method: 'POST', data: { _method: 'PATCH' },
        success(res) {
            if (!res.success) { isPaused = false; return; }
            clearSearch();
            animateOut(card, () => {
                card.remove();
                // Refresh langsung ambil data terbaru
                isPaused = false;
                refreshKanban();
                toastr.success(res.message, 'Berhasil!');
            });
        },
        error(xhr) {
            isPaused = false;
            toastr.error(xhr.responseJSON?.message ?? 'Gagal.', 'Error');
            if (btn) { btn.disabled = false; btn.innerHTML = '<span class="material-icons" style="font-size:14px;">local_shipping</span> Siap Kirim'; }
        }
    });
}

function batalkan(id) {
    isPaused = true;
    const card = document.getElementById('card-' + id);

    $.ajax({
        url: '/farmasi/prescriptions/' + id + '/quick-status',
        method: 'POST', data: { _method: 'PATCH' },
        success(res) {
            if (!res.success) { isPaused = false; return; }
            animateOut(card, () => {
                card.remove();
                isPaused = false;
                refreshKanban();
                toastr.info(res.message, 'Dibatalkan');
            });
        },
        error(xhr) {
            isPaused = false;
            toastr.error(xhr.responseJSON?.message ?? 'Gagal.', 'Error');
        }
    });
}

// ══════════════════════════════════════════════════════════════════════════
//  MODAL TRACKING KURIR
// ══════════════════════════════════════════════════════════════════════════
const MAPS_KEY = '{{ config("services.google_maps.key") }}';
let trackMap            = null;
let courierMarker       = null;
let destMarker          = null;
let directionsService   = null;
let directionsRenderer  = null;
let trackPollTimer      = null;
let activePrescId       = null;
let mapsReady           = false;
let lastRouteLat        = null;   // throttle: posisi terakhir route dihitung
let lastRouteLng        = null;
let lastRouteTime       = 0;
const ROUTE_THROTTLE_MS = 20000; // hitung ulang rute maks tiap 20 detik
const ROUTE_MIN_MOVE_M  = 30;    // atau jika bergerak > 30 meter

// ── Buka modal dari kartu (server-rendered) ───────────────────────────────
function openTrackModal(el) {
    // el bisa .kanban-card (div wrapper) atau child-nya
    const card = el.closest ? el : el;
    const data = {
        id          : card.dataset.id,
        nomor       : card.dataset.nomor,
        pasien      : card.dataset.pasien,
        kurir       : card.dataset.kurir,
        plat        : card.dataset.plat,
        phone       : card.dataset.phone,
        courierLat  : parseFloat(card.dataset.courierLat) || null,
        courierLng  : parseFloat(card.dataset.courierLng) || null,
        destLat     : parseFloat(card.dataset.destLat)    || null,
        destLng     : parseFloat(card.dataset.destLng)    || null,
        destLabel   : card.dataset.destLabel   || '',
        destAddress : card.dataset.destAddress || '',
    };
    _showTrackModal(data);
}

// ── Buka modal dari kartu yang dibangun JS (auto-refresh) ─────────────────
function openTrackModalFromData(p) {
    _showTrackModal({
        id          : p.id,
        nomor       : p.nomor_resep,
        pasien      : p.patient_name,
        kurir       : p.courier_name  || '-',
        plat        : p.plate_number  || '-',
        phone       : p.courier_phone || '',
        courierLat  : p.courier_lat   ? parseFloat(p.courier_lat)  : null,
        courierLng  : p.courier_lng   ? parseFloat(p.courier_lng)  : null,
        destLat     : p.dest_lat      ? parseFloat(p.dest_lat)     : null,
        destLng     : p.dest_lng      ? parseFloat(p.dest_lng)     : null,
        destLabel   : p.dest_label    || '',
        destAddress : p.dest_address  || '',
    });
}

function _showTrackModal(data) {
    activePrescId = data.id;

    // Isi panel info
    document.getElementById('tm-nomor').textContent      = data.nomor;
    document.getElementById('tm-sub').textContent        = 'Pasien: ' + data.pasien;
    document.getElementById('tm-kurir-nama').textContent = data.kurir;
    document.getElementById('tm-kurir-plat').textContent = data.plat;
    document.getElementById('tm-kurir-phone').textContent= data.phone || '-';
    document.getElementById('tm-kurir-phone-link').href  = data.phone ? 'tel:' + data.phone : '#';
    document.getElementById('tm-dest-label').textContent   = data.destLabel   || 'Alamat belum ditentukan';
    document.getElementById('tm-dest-address').textContent = data.destAddress || '-';
    document.getElementById('tm-full-link').href = '/admin/prescriptions/' + data.id + '/track';

    // Tampilkan modal
    const modal = document.getElementById('trackModal');
    modal.style.display = 'flex';

    // Load Maps lalu init
    loadMapsIfNeeded(() => initTrackMap(data));

    // Mulai polling lokasi
    startTrackPoll(data.id);
}

function closeTrackModal() {
    document.getElementById('trackModal').style.display = 'none';
    stopTrackPoll();
    activePrescId = null;
    // Bersihkan marker
    if (courierMarker)      { courierMarker.setMap(null);      courierMarker     = null; }
    if (destMarker)         { destMarker.setMap(null);         destMarker        = null; }
    if (directionsRenderer) { directionsRenderer.setMap(null); directionsRenderer= null; }
    directionsService = null;
    trackMap  = null;
    mapsReady = false;
    lastRouteLat = null; lastRouteLng = null; lastRouteTime = 0;
    const rb = document.getElementById('tm-route-box');
    if (rb) rb.style.display = 'none';
}

// Modal hanya menutup via tombol X

// ── Init peta dalam modal ─────────────────────────────────────────────────
function initTrackMap(data) {
    const center = data.destLat
        ? { lat: data.destLat, lng: data.destLng }
        : (data.courierLat ? { lat: data.courierLat, lng: data.courierLng } : { lat: -7.25, lng: 112.75 });

    trackMap = new google.maps.Map(document.getElementById('track-modal-map'), {
        center, zoom: 14,
        mapTypeControl   : false,
        streetViewControl: false,
        fullscreenControlOptions: { position: google.maps.ControlPosition.TOP_RIGHT },
        styles: [{ featureType:'poi', stylers:[{visibility:'off'}] }],
    });

    // ── Directions Service + Renderer ─────────────────────────────────────
    directionsService  = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map               : trackMap,
        suppressMarkers   : true,          // marker kita buat sendiri
        polylineOptions   : {
            strokeColor   : '#1a73e8',
            strokeWeight  : 5,
            strokeOpacity : 0.85,
        },
    });

    // ── Marker tujuan (merah, drop animation) ────────────────────────────
    if (data.destLat) {
        destMarker = new google.maps.Marker({
            position : { lat: data.destLat, lng: data.destLng },
            map      : trackMap,
            title    : 'Tujuan: ' + data.destLabel,
            icon     : {
                path        : google.maps.SymbolPath.CIRCLE,
                scale       : 11,
                fillColor   : '#c5221f',
                fillOpacity : 1,
                strokeColor : '#fff',
                strokeWeight: 2.5,
            },
            animation: google.maps.Animation.DROP,
            zIndex   : 1,
        });
        const destInfo = new google.maps.InfoWindow({
            content: `<div style="font-family:'Google Sans',sans-serif;padding:4px 6px;">
                        <div style="font-size:13px;font-weight:500;color:#202124;">${data.destLabel}</div>
                        <div style="font-size:11px;color:#5f6368;margin-top:2px;">${data.destAddress}</div>
                      </div>`,
        });
        destMarker.addListener('click', () => destInfo.open(trackMap, destMarker));
    }

    // ── Marker kurir (biru, panah motor) ─────────────────────────────────
    courierMarker = new google.maps.Marker({
        position : center,
        map      : trackMap,
        title    : 'Posisi Kurir',
        visible  : false,
        icon     : {
            path        : google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
            scale       : 6,
            fillColor   : '#1a73e8',
            fillOpacity : 1,
            strokeColor : '#fff',
            strokeWeight: 2,
            rotation    : 0,
        },
        zIndex   : 2,
    });

    mapsReady = true;

    // Langsung render posisi awal jika sudah ada data GPS
    if (data.courierLat) updateCourierPosition(data.courierLat, data.courierLng, null);
}

// ── Update posisi marker + hitung rute jalan ──────────────────────────────
function updateCourierPosition(lat, lng, lastSeen) {
    if (!mapsReady || !courierMarker) return;

    lat = parseFloat(lat);
    lng = parseFloat(lng);
    const pos = { lat, lng };

    // Geser marker kurir
    courierMarker.setPosition(pos);
    courierMarker.setVisible(true);

    // Arahkan panah marker ke tujuan
    if (destMarker) {
        const dest = destMarker.getPosition();
        const heading = google.maps.geometry.spherical.computeHeading(
            new google.maps.LatLng(lat, lng), dest
        );
        courierMarker.setIcon({ ...courierMarker.getIcon(), rotation: heading });
    }

    // Update panel info
    document.getElementById('tm-coords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    if (lastSeen) document.getElementById('tm-last-seen').textContent = 'Update: ' + lastSeen;
    setTmLive('ok', 'GPS aktif');
    document.getElementById('tm-gps-warning').style.display = 'none';

    // ── Throttle: hitung ulang rute hanya jika perlu ──────────────────────
    const now        = Date.now();
    const movedFar   = lastRouteLat !== null && google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(lastRouteLat, lastRouteLng),
        new google.maps.LatLng(lat, lng)
    ) > ROUTE_MIN_MOVE_M;
    const timeElapsed = (now - lastRouteTime) > ROUTE_THROTTLE_MS;

    if (destMarker && (lastRouteLat === null || movedFar || timeElapsed)) {
        calculateRoute(pos, destMarker.getPosition().toJSON());
        lastRouteLat  = lat;
        lastRouteLng  = lng;
        lastRouteTime = now;
    }
}

// ── Hitung dan gambar rute jalan (Directions API) ─────────────────────────
function calculateRoute(origin, destination) {
    if (!directionsService || !directionsRenderer) return;

    directionsService.route({
        origin      : origin,
        destination : destination,
        travelMode  : google.maps.TravelMode.DRIVING,
        unitSystem  : google.maps.UnitSystem.METRIC,
        region      : 'ID',
    }, function(result, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);

            // Tampilkan jarak & estimasi waktu di panel
            const leg = result.routes[0].legs[0];
            const distEl = document.getElementById('tm-route-dist');
            const etaEl  = document.getElementById('tm-route-eta');
            if (distEl) distEl.textContent = leg.distance.text;
            if (etaEl)  etaEl.textContent  = leg.duration.text;
            const routeBox = document.getElementById('tm-route-box');
            if (routeBox) routeBox.style.display = 'flex';

        } else if (status === 'ZERO_RESULTS') {
            // Tidak ada rute jalan — fallback ke garis lurus
            directionsRenderer.setDirections({ routes: [] });
            drawFallbackLine(origin, destination);
        }
        // Status lain (OVER_QUERY_LIMIT, dll) dibiarkan — rute lama tetap tampil
    });
}

// ── Fallback: garis lurus jika tidak ada rute ─────────────────────────────
function drawFallbackLine(origin, dest) {
    if (!trackMap) return;
    new google.maps.Polyline({
        path         : [origin, dest],
        strokeColor  : '#1a73e8',
        strokeOpacity: 0.5,
        strokeWeight : 3,
        geodesic     : true,
        map          : trackMap,
    });
}

// ── Polling lokasi dari API publik ────────────────────────────────────────
function startTrackPoll(prescId) {
    stopTrackPoll();
    setTmLive('loading', 'Menghubungkan...');
    pollLocation(prescId);
    trackPollTimer = setInterval(() => pollLocation(prescId), 10000);
}

function stopTrackPoll() {
    if (trackPollTimer) { clearInterval(trackPollTimer); trackPollTimer = null; }
}

function pollLocation(prescId) {
    if (activePrescId !== prescId) return;

    $.get('/api/track/' + prescId)
        .done(function(res) {
            if (activePrescId !== prescId) return;

            if (!res.tracking || !res.location?.latitude) {
                setTmLive('warn', 'Menunggu GPS kurir...');
                document.getElementById('tm-gps-warning').style.display = 'flex';
                return;
            }

            updateCourierPosition(
                res.location.latitude,
                res.location.longitude,
                res.location.last_seen,
            );
        })
        .fail(function() {
            setTmLive('error', 'Gagal mengambil lokasi');
        });
}

function setTmLive(state, text) {
    const dot = document.getElementById('tm-live-dot');
    const txt = document.getElementById('tm-live-text');
    if (!dot) return;
    const colors = { ok:'#137333', warn:'#b06000', error:'#c5221f', loading:'#1a73e8' };
    dot.style.background = colors[state] || '#9aa0a6';
    if (txt) txt.textContent = text;
    dot.style.animation = state === 'loading' ? 'blink 1s infinite' : 'none';
}

function centerTrackMap() {
    if (!trackMap) return;
    if (courierMarker?.getVisible()) {
        trackMap.panTo(courierMarker.getPosition());
        trackMap.setZoom(15);
    } else if (destMarker) {
        trackMap.panTo(destMarker.getPosition());
        trackMap.setZoom(15);
    }
}

// ── Lazy load Google Maps ─────────────────────────────────────────────────
window._mapsCallbacks = [];
window.onGoogleMapsLoaded = function() {
    window._mapsLoaded = true;
    window._mapsCallbacks.forEach(cb => cb());
    window._mapsCallbacks = [];
};

function loadMapsIfNeeded(cb) {
    if (window._mapsLoaded) { cb(); return; }
    window._mapsCallbacks.push(cb);
    if (!document.getElementById('gmap-sdk')) {
        const s = document.createElement('script');
        s.id  = 'gmap-sdk';
        s.src = `https://maps.googleapis.com/maps/api/js?key=${MAPS_KEY}&callback=onGoogleMapsLoaded&libraries=geometry`;
        s.async = true;
        document.head.appendChild(s);
    }
}
</script>
@endpush
