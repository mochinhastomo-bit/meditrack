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
    <span class="badge badge-blue" style="white-space:nowrap;flex-shrink:0;">Siap Kirim</span>
</div>
<div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;gap:6px;">
    <div style="flex:1;padding:6px 10px;border-radius:6px;background:#e6f4ea;font-size:12px;color:#137333;font-weight:500;display:flex;align-items:center;gap:4px;">
        <span class="material-icons" style="font-size:14px;">check_circle</span>
        Sudah diserahkan
    </div>
    <button class="btn-batalkan" onclick="batalkan({{ $p->id }})">
        <span class="material-icons" style="font-size:13px;">undo</span> Batalkan
    </button>
</div>
