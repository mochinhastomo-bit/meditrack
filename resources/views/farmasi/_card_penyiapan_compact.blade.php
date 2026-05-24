{{-- Baris ringkas (selalu tampil) --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
    <div style="min-width:0;flex:1;">
        <div data-role="nomor" style="font-size:12px;font-weight:600;color:#202124;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            {{ $p->nomor_resep }}
        </div>
        <div style="font-size:11px;color:#5f6368;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <span class="material-icons" style="font-size:11px;vertical-align:-1px;">person</span>
            {{ $p->patient->name ?? '-' }}
            &nbsp;·&nbsp;
            <span class="material-icons" style="font-size:11px;vertical-align:-1px;">calendar_today</span>
            {{ $p->tanggal->format('d/m/Y') }}
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
        <span class="badge badge-orange" style="font-size:10px;padding:2px 7px;">Penyiapan</span>
        <span class="material-icons compact-chevron">expand_more</span>
    </div>
</div>

{{-- Detail expandable --}}
<div class="compact-body">
    @if($p->keterangan)
    <div style="font-size:11px;color:#5f6368;margin-bottom:8px;padding:6px 8px;background:#f8f9fa;border-radius:4px;line-height:1.4;">
        <span class="material-icons" style="font-size:12px;vertical-align:-2px;">notes</span>
        {{ $p->keterangan }}
    </div>
    @endif
    <button class="btn-serahkan" onclick="event.stopPropagation(); serahkanKurir({{ $p->id }})">
        <span class="material-icons" style="font-size:14px;">local_shipping</span>
        Siap Kirim
    </button>
</div>
