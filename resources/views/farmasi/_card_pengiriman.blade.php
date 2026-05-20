@php
    $hasGps  = $p->courier && $p->courier->last_latitude;
    $hasDest = $p->address && $p->address->latitude;
@endphp

<div
    onclick="openTrackModal(this)"
    data-id="{{ $p->id }}"
    data-nomor="{{ $p->nomor_resep }}"
    data-pasien="{{ $p->patient->name ?? '-' }}"
    data-kurir="{{ $p->courier->name ?? '-' }}"
    data-plat="{{ $p->courier->plate_number ?? '-' }}"
    data-phone="{{ $p->courier->phone ?? '' }}"
    data-courier-lat="{{ $p->courier->last_latitude ?? '' }}"
    data-courier-lng="{{ $p->courier->last_longitude ?? '' }}"
    data-dest-lat="{{ $p->address->latitude ?? '' }}"
    data-dest-lng="{{ $p->address->longitude ?? '' }}"
    data-dest-label="{{ $p->address->label ?? '' }}"
    data-dest-address="{{ $p->address->address ?? '' }}"
    style="cursor:pointer;"
>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
        <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:500;color:#202124;">{{ $p->nomor_resep }}</div>
            <div style="font-size:12px;color:#5f6368;margin-top:2px;">
                <span class="material-icons" style="font-size:12px;vertical-align:-2px;">person</span>
                {{ $p->patient->name ?? '-' }}
            </div>
            @if($p->courier)
            <div style="font-size:12px;color:#b06000;margin-top:2px;">
                <span class="material-icons" style="font-size:12px;vertical-align:-2px;">delivery_dining</span>
                {{ $p->courier->name }} · {{ $p->courier->plate_number }}
            </div>
            @endif
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
            <span class="badge badge-purple">Dikirim</span>
            <span style="font-size:11px;color:#1a73e8;font-weight:500;display:flex;align-items:center;gap:2px;">
                <span class="material-icons" style="font-size:12px;">location_on</span> Lihat Peta
            </span>
        </div>
    </div>

    <div data-role="gps-indicator" data-state="{{ $hasGps ? 'active' : 'waiting' }}"
        style="margin-top:8px;padding:5px 8px;border-radius:5px;font-size:11px;display:flex;align-items:center;gap:4px;
               background:{{ $hasGps ? '#f3e8fd' : '#f1f3f4' }};color:{{ $hasGps ? '#7627bb' : '#9aa0a6' }};">
        <span class="material-icons" style="font-size:12px;">{{ $hasGps ? 'gps_fixed' : 'gps_off' }}</span>
        {{ $hasGps ? 'GPS aktif · ' . $p->courier->last_seen_at?->diffForHumans() : 'Menunggu sinyal GPS kurir' }}
    </div>
</div>
