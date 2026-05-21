<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking {{ $prescription->nomor_resep }} — MediTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; color: #202124; }

        /* ── TOP BAR ── */
        .topbar {
            background: #1a73e8;
            color: #fff;
            padding: 0 20px;
            height: 56px;
            display: flex;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
        .topbar .material-icons { font-size: 22px; }
        .topbar-title { font-size: 16px; font-weight: 600; flex: 1; }
        .topbar a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .topbar a:hover { color: #fff; }

        /* ── LAYOUT ── */
        .layout {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 56px);
        }
        @media (min-width: 768px) {
            .layout { flex-direction: row; }
        }

        /* ── MAP ── */
        #map {
            flex: 1;
            min-height: 300px;
            background: #e8f0fe;
        }

        /* ── PANEL ── */
        .panel {
            width: 100%;
            background: #fff;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        @media (min-width: 768px) {
            .panel { width: 360px; min-width: 360px; }
        }

        /* ── STATUS STEPPER ── */
        .stepper {
            display: flex;
            align-items: flex-start;
            gap: 0;
        }
        .step-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 14px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        .step-item.done:not(:last-child)::after  { background: #34a853; }
        .step-item.active:not(:last-child)::after { background: #e0e0e0; }
        .step-dot {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #9aa0a6;
            display: flex; align-items: center; justify-content: center;
            z-index: 1;
            position: relative;
        }
        .step-dot .material-icons { font-size: 14px; }
        .step-item.done   .step-dot { background: #34a853; color: #fff; }
        .step-item.active .step-dot { background: #1a73e8; color: #fff; box-shadow: 0 0 0 4px rgba(26,115,232,.2); }
        .step-label-text {
            font-size: 9px;
            color: #9aa0a6;
            text-align: center;
            margin-top: 5px;
            font-weight: 500;
            line-height: 1.3;
        }
        .step-item.done   .step-label-text { color: #34a853; }
        .step-item.active .step-label-text { color: #1a73e8; }

        /* ── INFO CARD ── */
        .info-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 14px 16px;
        }
        .info-card-title {
            font-size: 11px;
            font-weight: 600;
            color: #9aa0a6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-row .material-icons { font-size: 16px; color: #9aa0a6; margin-top: 2px; flex-shrink: 0; }
        .info-label { font-size: 11px; color: #9aa0a6; }
        .info-value { font-size: 13px; font-weight: 500; color: #202124; margin-top: 1px; }

        /* ── STATUS BADGE ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-penyiapan        { background: #fef7e0; color: #b06000; }
        .status-siap_kirim       { background: #e8f0fe; color: #1557b0; }
        .status-dibawa           { background: #ccfbf1; color: #0d6e64; }
        .status-dalam_pengiriman { background: #f3e8fd; color: #7627bb; }
        .status-terkirim         { background: #e6f4ea; color: #137333; }
        .status-dibatalkan       { background: #fce8e6; color: #c5221f; }

        /* ── ROUTE CARD ── */
        .route-card {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            border-radius: 12px;
            padding: 14px 16px;
            color: #fff;
            display: none;
        }
        .route-card.visible { display: block; }
        .route-card-title { font-size: 11px; font-weight: 600; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .route-boxes { display: flex; gap: 12px; }
        .route-box { flex: 1; background: rgba(255,255,255,0.15); border-radius: 8px; padding: 10px; text-align: center; }
        .route-box-label { font-size: 10px; opacity: 0.75; margin-bottom: 4px; }
        .route-box-value { font-size: 18px; font-weight: 700; }
        .route-box-unit  { font-size: 10px; opacity: 0.75; }

        /* ── GPS INDICATOR ── */
        .gps-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
        }
        .gps-bar.active   { background: #f3e8fd; color: #7627bb; }
        .gps-bar.waiting  { background: #f1f3f4; color: #9aa0a6; }
        .gps-bar .material-icons { font-size: 16px; }

        /* ── REFRESH BAR ── */
        .refresh-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #9aa0a6;
            padding: 0 4px;
        }
        .refresh-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #34a853;
            display: inline-block;
            margin-right: 5px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.3; }
        }

        /* ── NOT ACTIVE BANNER ── */
        .not-active-banner {
            background: #fef7e0;
            border: 1px solid #fdd663;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            color: #7b5800;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .not-active-banner .material-icons { color: #f9a825; margin-top: 1px; flex-shrink: 0; }

        /* ── PHONE BUTTON ── */
        .phone-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: #e6f4ea;
            color: #137333;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }
        .phone-btn:hover { background: #ceead6; }
        .phone-btn .material-icons { font-size: 18px; }

        /* ── FOOTER ── */
        .panel-footer {
            margin-top: auto;
            text-align: center;
            font-size: 11px;
            color: #c0c4c9;
            padding-top: 8px;
        }
    </style>
</head>
<body>

@php
    $statusOrder = ['penyiapan', 'siap_kirim', 'dibawa', 'dalam_pengiriman', 'terkirim'];
    $currentIdx  = array_search($prescription->status, $statusOrder);
    if ($currentIdx === false) $currentIdx = -1;

    $stepIcons   = ['science', 'inventory_2', 'inventory', 'delivery_dining', 'check_circle'];
    $stepLabels  = ['Penyiapan', 'Siap Kirim', 'Dibawa', 'Dikirim', 'Terkirim'];

    $isDelivering = $prescription->status === 'dalam_pengiriman';
    $isDibawa     = $prescription->status === 'dibawa';
    $isDelivered  = $prescription->status === 'terkirim';
    $isCancelled  = $prescription->status === 'dibatalkan';

    $courier = $prescription->courier;
    $address = $prescription->address;
@endphp

{{-- TOP BAR --}}
<div class="topbar">
    <img src="{{ asset('logo.png') }}" alt="MediTrack" style="height:34px;width:auto;object-fit:contain;filter:brightness(0) invert(1);">
    <div class="topbar-title">Tracking Pengiriman</div>
    <a href="{{ route('track.index') }}">
        <span class="material-icons" style="font-size:18px;">search</span> Cari Lain
    </a>
</div>

<div class="layout">

    {{-- MAP --}}
    <div id="map">
        @if(! $isDelivering)
        <div style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;color:#9aa0a6;">
            <span class="material-icons" style="font-size:56px;color:#dadce0;">
                {{ $isCancelled ? 'cancel' : ($isDelivered ? 'check_circle' : 'schedule') }}
            </span>
            <div style="font-size:14px;font-weight:500;">
                @if($isCancelled)  Pengiriman dibatalkan
                @elseif($isDelivered) Obat sudah terkirim
                @else Kurir belum diberangkatkan
                @endif
            </div>
            <div style="font-size:12px;">Peta aktif saat status <strong>Dalam Pengiriman</strong></div>
        </div>
        @endif
    </div>

    {{-- PANEL --}}
    <div class="panel">

        {{-- Nomor Resep --}}
        <div>
            <div style="font-size:12px;color:#9aa0a6;font-weight:500;margin-bottom:4px;">KODE RESEP</div>
            <div style="font-size:22px;font-weight:700;letter-spacing:0.5px;">{{ $prescription->nomor_resep }}</div>
            <div style="margin-top:6px;">
                <span id="statusBadge" class="status-badge status-{{ $prescription->status }}">
                    <span class="material-icons" style="font-size:13px;">
                        {{ $isCancelled ? 'cancel' : ($isDelivered ? 'check_circle' : 'radio_button_checked') }}
                    </span>
                    {{ $prescription->status_label }}
                </span>
            </div>
        </div>

        {{-- Stepper --}}
        @if(! $isCancelled)
        <div class="stepper">
            @foreach($stepIcons as $i => $icon)
            <div class="step-item {{ $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : '') }}">
                <div class="step-dot">
                    <span class="material-icons">{{ $i < $currentIdx ? 'check' : $icon }}</span>
                </div>
                <div class="step-label-text">{{ $stepLabels[$i] }}</div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Route info (muncul saat dalam_pengiriman) --}}
        <div id="routeCard" class="route-card {{ $isDelivering ? 'visible' : '' }}">
            <div class="route-card-title">Estimasi Pengiriman</div>
            <div class="route-boxes">
                <div class="route-box">
                    <div class="route-box-label">Jarak</div>
                    <div class="route-box-value" id="routeDistance">—</div>
                    <div class="route-box-unit">km</div>
                </div>
                <div class="route-box">
                    <div class="route-box-label">Estimasi Tiba</div>
                    <div class="route-box-value" id="routeDuration">—</div>
                    <div class="route-box-unit">menit</div>
                </div>
            </div>
        </div>

        {{-- GPS Bar --}}
        @if($isDelivering)
        <div id="gpsBar" class="gps-bar {{ ($courier && $courier->last_latitude) ? 'active' : 'waiting' }}">
            <span class="material-icons">{{ ($courier && $courier->last_latitude) ? 'gps_fixed' : 'gps_off' }}</span>
            <span id="gpsText">
                {{ ($courier && $courier->last_latitude)
                    ? 'GPS aktif · ' . ($courier->last_seen_at?->diffForHumans() ?? '')
                    : 'Menunggu sinyal GPS kurir' }}
            </span>
        </div>
        @endif

        {{-- Dibawa banner --}}
        @if($isDibawa)
        <div class="not-active-banner" style="background:#f0fdfa;border-color:#99f6e4;">
            <span class="material-icons" style="color:#0d9488;">inventory</span>
            <div style="color:#0d6e64;">Obat sudah diambil kurir dari RS dan akan segera diantarkan.</div>
        </div>
        @endif

        {{-- Not active warning --}}
        @if(! $isDelivering && ! $isDelivered && ! $isCancelled && ! $isDibawa)
        <div class="not-active-banner">
            <span class="material-icons">info</span>
            <div>Peta lokasi kurir akan aktif secara otomatis ketika kurir memulai pengiriman.</div>
        </div>
        @endif

        {{-- Info Resep --}}
        <div class="info-card">
            <div class="info-card-title">Informasi Pasien</div>
            <div class="info-row">
                <span class="material-icons">person</span>
                <div>
                    <div class="info-label">Nama Pasien</div>
                    <div class="info-value">{{ $prescription->patient->name ?? '-' }}</div>
                </div>
            </div>
            <div class="info-row">
                <span class="material-icons">calendar_today</span>
                <div>
                    <div class="info-label">Tanggal Resep</div>
                    <div class="info-value">{{ $prescription->tanggal->translatedFormat('d F Y') }}</div>
                </div>
            </div>
            @if($address)
            <div class="info-row">
                <span class="material-icons">location_on</span>
                <div>
                    <div class="info-label">Alamat Pengiriman</div>
                    <div class="info-value">{{ $address->label }} — {{ $address->address }}</div>
                </div>
            </div>
            @endif
        </div>

        {{-- Info Kurir --}}
        @if($courier && ($isDibawa || $isDelivering || $isDelivered))
        <div class="info-card">
            <div class="info-card-title">Informasi Kurir</div>
            <div class="info-row">
                <span class="material-icons">delivery_dining</span>
                <div>
                    <div class="info-label">Nama Kurir</div>
                    <div class="info-value">{{ $courier->name }}</div>
                </div>
            </div>
            <div class="info-row">
                <span class="material-icons">directions_car</span>
                <div>
                    <div class="info-label">Nomor Kendaraan</div>
                    <div class="info-value">{{ $courier->plate_number }}</div>
                </div>
            </div>
        </div>

        @if($courier->phone && $isDelivering)
        <a href="tel:{{ $courier->phone }}" class="phone-btn">
            <span class="material-icons">phone</span>
            Hubungi Kurir · {{ $courier->phone }}
        </a>
        @endif
        @endif

        {{-- Refresh info --}}
        @if($isDelivering)
        <div class="refresh-bar">
            <div><span class="refresh-dot"></span> Auto-refresh setiap 15 detik</div>
            <div id="lastRefresh">—</div>
        </div>
        @endif

        <div class="panel-footer">MediTrack &copy; {{ date('Y') }}</div>

    </div>{{-- end panel --}}
</div>{{-- end layout --}}

@php
    $courierData = null;
    if ($courier && $isDelivering) {
        $courierData = [
            'lat'      => $courier->last_latitude  ? (float) $courier->last_latitude  : null,
            'lng'      => $courier->last_longitude ? (float) $courier->last_longitude : null,
            'lastSeen' => $courier->last_seen_at?->diffForHumans() ?? null,
        ];
    }
    $destData2 = $address ? [
        'lat'   => (float) $address->latitude,
        'lng'   => (float) $address->longitude,
        'label' => $address->label,
    ] : null;
@endphp

<script>
const IS_DELIVERING = @json($isDelivering);
const NOMOR_RESEP   = @json($prescription->nomor_resep);
const POLL_URL      = @json(route('track.poll', $prescription->nomor_resep));
const MAPS_KEY      = @json($mapsKey);

const INIT_COURIER  = @json($courierData);
const DESTINATION   = @json($destData2);

let map, courierMarker, destMarker, directionsService, directionsRenderer;
let routePolyline = null;
let lastRouteLat = null, lastRouteLng = null, lastRouteTime = 0;
const ROUTE_THROTTLE_MS  = 20000;
const ROUTE_MIN_MOVE_M   = 30;

// ── INIT MAP ──────────────────────────────────────────────────────────────
function initMap() {
    if (!IS_DELIVERING) return;

    const center = (INIT_COURIER && INIT_COURIER.lat)
        ? { lat: INIT_COURIER.lat, lng: INIT_COURIER.lng }
        : (DESTINATION ? { lat: DESTINATION.lat, lng: DESTINATION.lng } : { lat: -6.2, lng: 106.8 });

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
    });

    directionsService  = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: true,
        polylineOptions: { strokeColor: '#1a73e8', strokeWeight: 5, strokeOpacity: 0.85 },
    });
    directionsRenderer.setMap(map);

    // Courier marker (arrow icon)
    if (INIT_COURIER && INIT_COURIER.lat) {
        courierMarker = new google.maps.Marker({
            position: { lat: INIT_COURIER.lat, lng: INIT_COURIER.lng },
            map,
            title: 'Kurir',
            icon: {
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 5,
                fillColor: '#1a73e8',
                fillOpacity: 1,
                strokeColor: '#fff',
                strokeWeight: 2,
            },
            zIndex: 10,
        });
    }

    // Destination marker
    if (DESTINATION) {
        destMarker = new google.maps.Marker({
            position: { lat: DESTINATION.lat, lng: DESTINATION.lng },
            map,
            title: DESTINATION.label,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 9,
                fillColor: '#34a853',
                fillOpacity: 1,
                strokeColor: '#fff',
                strokeWeight: 2.5,
            },
            zIndex: 9,
        });

        new google.maps.InfoWindow({ content: `<b>${DESTINATION.label}</b>` })
            .open(map, destMarker);
    }

    // Initial route
    if (INIT_COURIER && INIT_COURIER.lat && DESTINATION) {
        calculateRoute(
            { lat: INIT_COURIER.lat, lng: INIT_COURIER.lng },
            { lat: DESTINATION.lat,  lng: DESTINATION.lng  }
        );
        updateGpsBar(true, INIT_COURIER.lastSeen);
    }
}

// ── UPDATE COURIER POSITION ───────────────────────────────────────────────
function updateCourierPosition(lat, lng, lastSeen) {
    if (!map) return;

    const pos = { lat, lng };

    if (!courierMarker) {
        courierMarker = new google.maps.Marker({
            position: pos, map,
            icon: {
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 5, fillColor: '#1a73e8', fillOpacity: 1,
                strokeColor: '#fff', strokeWeight: 2,
            },
            zIndex: 10,
        });
    } else {
        // Compute heading
        if (lastRouteLat !== null) {
            const from = new google.maps.LatLng(lastRouteLat, lastRouteLng);
            const to   = new google.maps.LatLng(lat, lng);
            const heading = google.maps.geometry.spherical.computeHeading(from, to);
            courierMarker.setIcon({
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 5, fillColor: '#1a73e8', fillOpacity: 1,
                strokeColor: '#fff', strokeWeight: 2,
                rotation: heading,
            });
        }
        courierMarker.setPosition(pos);
    }

    // Throttled route recalculation
    const now = Date.now();
    let shouldRecalculate = false;
    if (lastRouteLat === null) {
        shouldRecalculate = true;
    } else {
        const from = new google.maps.LatLng(lastRouteLat, lastRouteLng);
        const to   = new google.maps.LatLng(lat, lng);
        const moved = google.maps.geometry.spherical.computeDistanceBetween(from, to);
        if (moved >= ROUTE_MIN_MOVE_M || (now - lastRouteTime) >= ROUTE_THROTTLE_MS) {
            shouldRecalculate = true;
        }
    }

    if (shouldRecalculate && DESTINATION) {
        lastRouteLat  = lat;
        lastRouteLng  = lng;
        lastRouteTime = now;
        calculateRoute(pos, { lat: DESTINATION.lat, lng: DESTINATION.lng });
    }

    updateGpsBar(true, lastSeen);
}

// ── CALCULATE ROUTE ───────────────────────────────────────────────────────
function calculateRoute(origin, destination) {
    directionsService.route({
        origin,
        destination,
        travelMode: google.maps.TravelMode.DRIVING,
        region: 'ID',
    }, (result, status) => {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);
            if (routePolyline) { routePolyline.setMap(null); routePolyline = null; }

            const leg = result.routes[0].legs[0];
            document.getElementById('routeDistance').textContent =
                (leg.distance.value / 1000).toFixed(1);
            document.getElementById('routeDuration').textContent =
                Math.ceil(leg.duration.value / 60);
            document.getElementById('routeCard').classList.add('visible');
        } else if (status === 'ZERO_RESULTS') {
            // Fallback: straight polyline
            directionsRenderer.setDirections({ routes: [] });
            if (routePolyline) routePolyline.setMap(null);
            routePolyline = new google.maps.Polyline({
                path: [origin, destination],
                geodesic: true,
                strokeColor: '#1a73e8',
                strokeOpacity: 0.7,
                strokeWeight: 3,
                icons: [{
                    icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 3 },
                    offset: '50%',
                }],
                map,
            });
        }
    });
}

// ── GPS BAR UPDATE ────────────────────────────────────────────────────────
function updateGpsBar(active, lastSeen) {
    const bar  = document.getElementById('gpsBar');
    const text = document.getElementById('gpsText');
    if (!bar || !text) return;
    if (active) {
        bar.className = 'gps-bar active';
        bar.querySelector('.material-icons').textContent = 'gps_fixed';
        text.textContent = 'GPS aktif · ' + (lastSeen || '');
    } else {
        bar.className = 'gps-bar waiting';
        bar.querySelector('.material-icons').textContent = 'gps_off';
        text.textContent = 'Menunggu sinyal GPS kurir';
    }
}

// ── POLLING ───────────────────────────────────────────────────────────────
async function poll() {
    try {
        const res  = await fetch(POLL_URL);
        const data = await res.json();

        if (!res.ok) return;

        // Update status badge
        const badge = document.getElementById('statusBadge');
        if (badge) {
            badge.className = 'status-badge status-' + data.status;
            badge.innerHTML = `<span class="material-icons" style="font-size:13px;">radio_button_checked</span> ${data.status_label}`;
        }

        // Update refresh time
        const lr = document.getElementById('lastRefresh');
        if (lr) lr.textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID');

        // Update courier position if delivering
        if (data.status === 'dalam_pengiriman' && data.courier && data.courier.lat) {
            updateCourierPosition(
                parseFloat(data.courier.lat),
                parseFloat(data.courier.lng),
                data.courier.last_seen,
            );
        } else if (data.status !== 'dalam_pengiriman') {
            updateGpsBar(false, null);
        }

        // If status changed to delivered/cancelled → reload page
        if (data.status === 'terkirim' || data.status === 'dibatalkan') {
            setTimeout(() => location.reload(), 2000);
        }

    } catch (e) { /* ignore network errors */ }
}

// ── BOOT ──────────────────────────────────────────────────────────────────
if (IS_DELIVERING) {
    // Load Google Maps
    window.initMap = initMap;
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${MAPS_KEY}&libraries=geometry&callback=initMap&language=id&region=ID`;
    script.async = true;
    document.head.appendChild(script);

    // Start polling
    poll();
    setInterval(poll, 15000);
}
</script>
</body>
</html>
