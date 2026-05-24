@extends('layouts.admin')
@section('title', 'Tracking — ' . $prescription->nomor_resep)

@push('styles')
<style>
    #tracking-map {
        width: 100%;
        height: 520px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        background: #f1f3f4;
    }
    .info-panel { display: flex; flex-direction: column; gap: 12px; }
    .info-row { display: flex; align-items: flex-start; gap: 10px; }
    .info-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .info-label { font-size: 11px; color: #5f6368; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-value { font-size: 14px; color: #202124; font-weight: 500; margin-top: 2px; }
    .pulse { animation: pulse 2s infinite; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.4; }
    }
    .status-bar { display: flex; gap: 0; margin: 20px 0; }
    .status-step { flex: 1; text-align: center; position: relative; }
    .status-step::before {
        content: '';
        position: absolute;
        top: 14px;
        left: -50%;
        width: 100%;
        height: 2px;
        background: #e0e0e0;
        z-index: 0;
    }
    .status-step:first-child::before { display: none; }
    .step-dot {
        width: 28px; height: 28px; border-radius: 50%;
        background: #e0e0e0; color: #fff;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto; font-size: 14px; position: relative; z-index: 1;
        transition: background 0.3s;
    }
    .step-dot.done  { background: #137333; }
    .step-dot.active { background: #1a73e8; }
    .step-label { font-size: 11px; color: #5f6368; margin-top: 6px; line-height: 1.3; }
    .step-label.done   { color: #137333; font-weight: 500; }
    .step-label.active { color: #1a73e8; font-weight: 500; }
    #last-update-badge { font-size: 12px; color: #5f6368; display: flex; align-items: center; gap: 4px; }
</style>
@endpush

@section('content')

{{-- ── Header ─────────────────────────────────────────────────────────── --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
            <a href="{{ route('admin.prescriptions.index') }}" style="color:#1a73e8;text-decoration:none;font-size:13px;">
                <span class="material-icons" style="font-size:16px;vertical-align:-3px;">arrow_back</span>
                Catatan Resep
            </a>
            <span class="material-icons" style="font-size:14px;color:#9aa0a6;">chevron_right</span>
            <span style="font-size:13px;color:#5f6368;">Tracking</span>
        </div>
        <h1 style="font-size:20px;font-weight:500;color:#202124;">
            <span class="material-icons" style="vertical-align:-4px;color:#1a73e8;">location_on</span>
            {{ $prescription->nomor_resep }}
        </h1>
    </div>
    <div id="last-update-badge">
        <span class="material-icons pulse" style="font-size:14px;color:#1a73e8;" id="live-dot">fiber_manual_record</span>
        <span id="last-update-text">Memuat...</span>
    </div>
</div>

{{-- ── Progress Status ─────────────────────────────────────────────────── --}}
@php
$steps  = ['penyiapan','siap_kirim','dibawa','dalam_pengiriman','terkirim'];
$labels = ['Penyiapan','Siap Kirim','Dibawa Kurir','Dalam Pengiriman','Terkirim'];
$curIdx = array_search($prescription->status, $steps);
if ($curIdx === false) $curIdx = -1;
if ($prescription->status === 'dibatalkan') $curIdx = -1;
@endphp

<div class="card" style="margin-bottom:16px;padding:20px 32px;">
    @if($prescription->status === 'dibatalkan')
    <div style="text-align:center;color:#c5221f;font-weight:500;font-size:14px;">
        <span class="material-icons" style="vertical-align:-5px;">cancel</span>
        Pengiriman ini telah dibatalkan
    </div>
    @else
    <div class="status-bar">
        @foreach($steps as $i => $step)
        <div class="status-step">
            <div class="step-dot {{ $i < $curIdx ? 'done' : ($i === $curIdx ? 'active' : '') }}">
                @if($i < $curIdx)
                    <span class="material-icons" style="font-size:14px;">check</span>
                @else
                    {{ $i + 1 }}
                @endif
            </div>
            <div class="step-label {{ $i < $curIdx ? 'done' : ($i === $curIdx ? 'active' : '') }}">
                {{ $labels[$i] }}
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ── Main Layout: Map + Info ─────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:1fr 340px; gap:16px;">

    {{-- MAP --}}
    <div class="card" style="padding:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="font-size:14px;font-weight:500;color:#202124;">
                <span class="material-icons" style="vertical-align:-4px;color:#1a73e8;font-size:18px;">map</span>
                Lokasi Kurir Real-Time
            </div>
            <button onclick="centerMap()" class="btn-secondary" style="padding:6px 12px;font-size:13px;">
                <span class="material-icons" style="font-size:15px;">my_location</span>
                Pusatkan Peta
            </button>
        </div>
        <div id="tracking-map"></div>

        @if($prescription->status !== 'dalam_pengiriman')
        <div style="margin-top:12px;padding:12px;background:#fef3e2;border-radius:6px;font-size:13px;color:#b06000;display:flex;align-items:center;gap:8px;">
            <span class="material-icons" style="font-size:16px;">info</span>
            @if($prescription->status === 'terkirim')
                Pengiriman telah selesai.
            @else
                Tracking real-time hanya aktif saat status <strong>Dalam Pengiriman</strong>.
            @endif
        </div>
        @endif
    </div>

    {{-- INFO PANEL --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Info Resep --}}
        <div class="card">
            <div style="font-size:13px;font-weight:500;color:#202124;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:16px;color:#1a73e8;">receipt_long</span>
                Info Resep
            </div>
            <div class="info-panel">
                <div class="info-row">
                    <div class="info-icon" style="background:#e8f0fe;">
                        <span class="material-icons" style="color:#1a73e8;font-size:18px;">tag</span>
                    </div>
                    <div>
                        <div class="info-label">Nomor Resep</div>
                        <div class="info-value">{{ $prescription->nomor_resep }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon" style="background:#e6f4ea;">
                        <span class="material-icons" style="color:#137333;font-size:18px;">person</span>
                    </div>
                    <div>
                        <div class="info-label">Pasien</div>
                        <div class="info-value">{{ $prescription->patient->name ?? '-' }}</div>
                        <div style="font-size:12px;color:#5f6368;">{{ $prescription->patient->phone ?? '' }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon" style="background:#fef3e2;">
                        <span class="material-icons" style="color:#b06000;font-size:18px;">location_on</span>
                    </div>
                    <div>
                        <div class="info-label">Tujuan Pengiriman</div>
                        @if($prescription->address)
                        <div class="info-value" style="font-size:13px;">{{ $prescription->address->label }}</div>
                        <div style="font-size:12px;color:#5f6368;line-height:1.4;">{{ $prescription->address->address }}</div>
                        @else
                        <div class="info-value" style="color:#9aa0a6;font-style:italic;">Belum ditentukan</div>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon" style="background:#f1f3f4;">
                        <span class="material-icons" style="color:#5f6368;font-size:18px;">calendar_today</span>
                    </div>
                    <div>
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">{{ $prescription->tanggal->format('d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Kurir --}}
        <div class="card">
            <div style="font-size:13px;font-weight:500;color:#202124;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:16px;color:#b06000;">delivery_dining</span>
                Info Kurir
            </div>
            @if($prescription->courier)
            <div class="info-panel">
                <div class="info-row">
                    <div class="info-icon" style="background:#fef3e2;">
                        <span class="material-icons" style="color:#b06000;font-size:18px;">person</span>
                    </div>
                    <div>
                        <div class="info-label">Nama Kurir</div>
                        <div class="info-value">{{ $prescription->courier->name }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon" style="background:#fef3e2;">
                        <span class="material-icons" style="color:#b06000;font-size:18px;">two_wheeler</span>
                    </div>
                    <div>
                        <div class="info-label">Plat Nomor</div>
                        <div class="info-value" style="letter-spacing:1px;">{{ $prescription->courier->plate_number }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon" style="background:#fef3e2;">
                        <span class="material-icons" style="color:#b06000;font-size:18px;">phone</span>
                    </div>
                    <div>
                        <div class="info-label">Nomor HP</div>
                        <div class="info-value">
                            <a href="tel:{{ $prescription->courier->phone }}" style="color:#1a73e8;text-decoration:none;">
                                {{ $prescription->courier->phone }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="info-row" id="location-info-row">
                    <div class="info-icon" style="background:#e8f0fe;">
                        <span class="material-icons" style="color:#1a73e8;font-size:18px;">gps_fixed</span>
                    </div>
                    <div>
                        <div class="info-label">Koordinat Terakhir</div>
                        <div class="info-value" id="coords-display" style="font-size:12px;font-family:monospace;">
                            @if($prescription->courier->last_latitude)
                                {{ $prescription->courier->last_latitude }}, {{ $prescription->courier->last_longitude }}
                            @else
                                Belum ada data GPS
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div style="text-align:center;padding:20px;color:#9aa0a6;">
                <span class="material-icons" style="font-size:32px;display:block;margin-bottom:6px;">person_off</span>
                <div style="font-size:13px;">Belum ada kurir yang ditugaskan</div>
            </div>
            @endif
        </div>

        {{-- Jarak & ETA --}}
        <div class="card" id="route-info-card" style="display:none;padding:16px;">
            <div style="font-size:13px;font-weight:500;color:#202124;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:16px;color:#1a73e8;">route</span>
                Estimasi Rute
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div style="background:#e8f0fe;border-radius:8px;padding:12px;text-align:center;">
                    <div style="font-size:10px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                        <span class="material-icons" style="font-size:12px;vertical-align:-2px;">straighten</span> Jarak
                    </div>
                    <div style="font-size:18px;font-weight:700;color:#1a73e8;" id="route-distance">—</div>
                </div>
                <div style="background:#e6f4ea;border-radius:8px;padding:12px;text-align:center;">
                    <div style="font-size:10px;color:#5f6368;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                        <span class="material-icons" style="font-size:12px;vertical-align:-2px;">schedule</span> Estimasi
                    </div>
                    <div style="font-size:18px;font-weight:700;color:#137333;" id="route-duration">—</div>
                </div>
            </div>
        </div>

        {{-- Status Badge --}}
        <div class="card" style="text-align:center;padding:16px;">
            <div style="font-size:12px;color:#5f6368;margin-bottom:6px;">Status Saat Ini</div>
            <span class="badge badge-{{ $prescription->status_color }}" style="font-size:14px;padding:6px 16px;" id="current-status-badge">
                {{ $prescription->status_label }}
            </span>
        </div>

        {{-- Foto Bukti Pengiriman --}}
        @if($prescription->delivery_photo)
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:500;color:#202124;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:16px;color:#137333;">photo_camera</span>
                Foto Bukti Pengiriman
            </div>
            <a href="{{ asset('storage/' . $prescription->delivery_photo) }}" target="_blank">
                <img src="{{ asset('storage/' . $prescription->delivery_photo) }}"
                    alt="Foto Bukti Pengiriman"
                    style="width:100%;border-radius:8px;object-fit:cover;max-height:220px;cursor:zoom-in;border:1px solid #e0e0e0;">
            </a>
            <div style="font-size:11px;color:#5f6368;margin-top:8px;text-align:center;">
                <span class="material-icons" style="font-size:12px;vertical-align:-2px;">info</span>
                Foto diambil saat kurir tiba di lokasi pasien
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
    const PRESCRIPTION_ID = {{ $prescription->id }};
    const MAPS_KEY = '{{ $mapsKey }}';
    const IS_ACTIVE = {{ $prescription->status === 'dalam_pengiriman' ? 'true' : 'false' }};

    @php
        $destData = $prescription->address ? [
            'lat'   => (float) $prescription->address->latitude,
            'lng'   => (float) $prescription->address->longitude,
            'label' => $prescription->address->label,
        ] : null;
    @endphp
    const DESTINATION = @json($destData);

    let map, courierMarker, destinationMarker;
    let directionsService, directionsRenderer;
    let refreshInterval;
    let lastRouteLat = null, lastRouteLng = null, lastRouteTime = 0;
    const ROUTE_THROTTLE_MS = 20000; // hitung ulang rute maks tiap 20 detik
    const ROUTE_MIN_MOVE_M  = 30;    // atau jika bergerak > 30 meter

    // ── Init Maps ─────────────────────────────────────────────────────────
    window.initTrackingMap = function() {

        const defaultCenter = DESTINATION
            ? { lat: DESTINATION.lat, lng: DESTINATION.lng }
            : { lat: -6.2088, lng: 106.8456 };

        map = new google.maps.Map(document.getElementById('tracking-map'), {
            center           : defaultCenter,
            zoom             : 14,
            mapTypeControl   : false,
            streetViewControl: false,
            fullscreenControlOptions: { position: google.maps.ControlPosition.TOP_RIGHT },
            styles: [{ featureType:'poi', stylers:[{visibility:'off'}] }],
        });

        // ── Directions Service + Renderer ─────────────────────────────────
        directionsService  = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map            : map,
            suppressMarkers: true,        // marker kita kelola sendiri
            polylineOptions: {
                strokeColor  : '#1a73e8',
                strokeWeight : 5,
                strokeOpacity: 0.85,
            },
        });

        // ── Marker tujuan (merah) ─────────────────────────────────────────
        if (DESTINATION) {
            destinationMarker = new google.maps.Marker({
                position : { lat: DESTINATION.lat, lng: DESTINATION.lng },
                map      : map,
                title    : 'Tujuan: ' + DESTINATION.label,
                icon     : {
                    path        : google.maps.SymbolPath.CIRCLE,
                    scale       : 11,
                    fillColor   : '#c5221f',
                    fillOpacity : 1,
                    strokeColor : '#fff',
                    strokeWeight: 2.5,
                },
                animation: google.maps.Animation.DROP,
            });
            const iw = new google.maps.InfoWindow({
                content: `<div style="font-family:'Google Sans',sans-serif;padding:4px 8px;">
                    <div style="font-weight:500;color:#202124;">${DESTINATION.label}</div>
                    <div style="font-size:12px;color:#5f6368;margin-top:2px;">Tujuan Pengiriman</div>
                </div>`,
            });
            destinationMarker.addListener('click', () => iw.open(map, destinationMarker));
        }

        // ── Marker kurir (panah biru, belum terlihat) ─────────────────────
        courierMarker = new google.maps.Marker({
            position : defaultCenter,
            map      : map,
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
            zIndex: 2,
        });

        // ── Mulai polling ─────────────────────────────────────────────────
        if (IS_ACTIVE) {
            fetchLocation();
            refreshInterval = setInterval(fetchLocation, 10000);
        } else {
            fetchLocation(false);
            document.getElementById('live-dot').style.display = 'none';
            document.getElementById('last-update-text').textContent = 'Tracking tidak aktif';
        }
    };

    // ── Fetch lokasi dari API ─────────────────────────────────────────────
    function fetchLocation(updateText = true) {
        fetch(`/api/track/${PRESCRIPTION_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.tracking || !data.location?.latitude) {
                    if (updateText)
                        document.getElementById('last-update-text').textContent = 'Kurir belum mengirim GPS';
                    return;
                }

                const lat = parseFloat(data.location.latitude);
                const lng = parseFloat(data.location.longitude);
                const pos = { lat, lng };

                // Geser marker kurir
                courierMarker.setPosition(pos);
                courierMarker.setVisible(true);

                // Arahkan panah menghadap tujuan
                if (destinationMarker) {
                    const heading = google.maps.geometry.spherical.computeHeading(
                        new google.maps.LatLng(lat, lng),
                        destinationMarker.getPosition()
                    );
                    courierMarker.setIcon({ ...courierMarker.getIcon(), rotation: heading });
                }

                // Update info panel
                document.getElementById('coords-display').textContent =
                    `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                if (updateText)
                    document.getElementById('last-update-text').textContent =
                        'Diperbarui ' + data.location.last_seen;

                // Hitung ulang rute jika perlu (throttle)
                if (destinationMarker) {
                    const now      = Date.now();
                    const movedFar = lastRouteLat !== null &&
                        google.maps.geometry.spherical.computeDistanceBetween(
                            new google.maps.LatLng(lastRouteLat, lastRouteLng),
                            new google.maps.LatLng(lat, lng)
                        ) > ROUTE_MIN_MOVE_M;
                    const timeOk   = (now - lastRouteTime) > ROUTE_THROTTLE_MS;

                    if (lastRouteLat === null || movedFar || timeOk) {
                        calculateRoute(pos, destinationMarker.getPosition().toJSON());
                        lastRouteLat  = lat;
                        lastRouteLng  = lng;
                        lastRouteTime = now;
                    }
                }
            })
            .catch(() => {
                if (updateText)
                    document.getElementById('last-update-text').textContent = 'Gagal memuat lokasi';
            });
    }

    // ── Hitung & gambar rute jalan (Directions API) ───────────────────────
    function calculateRoute(origin, destination) {
        directionsService.route({
            origin     : origin,
            destination: destination,
            travelMode : google.maps.TravelMode.DRIVING,
            unitSystem : google.maps.UnitSystem.METRIC,
            region     : 'ID',
        }, function(result, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);

                const leg  = result.routes[0].legs[0];
                document.getElementById('route-distance').textContent = leg.distance.text;
                document.getElementById('route-duration').textContent = leg.duration.text;
                document.getElementById('route-info-card').style.display = 'block';

            } else if (status === 'ZERO_RESULTS') {
                // Fallback garis lurus jika tidak ada rute jalan
                directionsRenderer.setDirections({ routes: [] });
                new google.maps.Polyline({
                    path        : [origin, destination],
                    strokeColor : '#1a73e8',
                    strokeOpacity: 0.5,
                    strokeWeight: 3,
                    geodesic    : true,
                    map         : map,
                });
            }
        });
    }

    // ── Center peta ke kurir / tujuan ─────────────────────────────────────
    function centerMap() {
        if (courierMarker?.getVisible()) {
            map.panTo(courierMarker.getPosition());
            map.setZoom(15);
        } else if (DESTINATION) {
            map.panTo({ lat: DESTINATION.lat, lng: DESTINATION.lng });
            map.setZoom(15);
        }
    }

    // ── Load Google Maps (dengan library geometry) ────────────────────────
    (function loadMaps() {
        if (document.getElementById('gmap-script')) return;
        const s = document.createElement('script');
        s.id  = 'gmap-script';
        s.src = `https://maps.googleapis.com/maps/api/js?key=${MAPS_KEY}&callback=initTrackingMap&libraries=geometry`;
        s.async = true;
        document.head.appendChild(s);
    })();
</script>
@endpush
