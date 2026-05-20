<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
    <div>
        <div style="font-size:13px;font-weight:500;color:#202124;">{{ $p->nomor_resep }}</div>
        <div style="font-size:12px;color:#5f6368;margin-top:3px;">
            <span class="material-icons" style="font-size:12px;vertical-align:-2px;">person</span>
            {{ $p->patient->name ?? '-' }} &nbsp;·&nbsp;
            <span class="material-icons" style="font-size:12px;vertical-align:-2px;">calendar_today</span>
            {{ $p->tanggal->format('d/m/Y') }}
        </div>
    </div>
    <span class="badge badge-orange" style="white-space:nowrap;flex-shrink:0;">Proses Penyiapan</span>
</div>
<button class="btn-serahkan" onclick="serahkanKurir({{ $p->id }})">
    <span class="material-icons" style="font-size:15px;">local_shipping</span>
    Serahkan ke Kurir
</button>
