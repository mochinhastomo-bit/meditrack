@extends('layouts.admin')
@section('title', 'Laporan Pengiriman')

@push('styles')
<style>
    .filter-card { background:#fff; border-radius:12px; border:1px solid #e0e0e0; padding:20px; margin-bottom:20px; }
    .filter-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
    .form-label { font-size:12px; font-weight:500; color:#5f6368; display:block; margin-bottom:4px; }
    .form-control {
        width:100%; padding:8px 12px; border:1px solid #e0e0e0; border-radius:8px;
        font-size:13px; color:#202124; background:#fff; outline:none;
        transition: border-color .15s;
    }
    .form-control:focus { border-color:#1a73e8; }
    .export-btns { display:flex; gap:10px; flex-wrap:wrap; margin-top:16px; }
    .btn-export {
        display:inline-flex; align-items:center; gap:6px;
        padding:10px 20px; border-radius:8px; font-size:13px;
        font-weight:500; cursor:pointer; border:none; text-decoration:none;
        transition: opacity .15s;
    }
    .btn-export:hover { opacity:.85; }
    .btn-excel { background:#137333; color:#fff; }
    .btn-pdf   { background:#c5221f; color:#fff; }
    .preview-table { width:100%; border-collapse:collapse; font-size:13px; }
    .preview-table th {
        background:#f8f9fa; padding:10px 12px; text-align:left;
        font-size:11px; font-weight:600; color:#5f6368; text-transform:uppercase;
        letter-spacing:.5px; border-bottom:2px solid #e0e0e0;
    }
    .preview-table td { padding:10px 12px; border-bottom:1px solid #f1f3f4; color:#202124; }
    .preview-table tr:last-child td { border-bottom:none; }
    .preview-table tr:hover td { background:#f8f9fa; }
</style>
@endpush

@section('content')
<div style="margin-bottom:20px;">
    <h2 style="font-size:20px;font-weight:600;color:#202124;margin:0;">Laporan Pengiriman</h2>
    <p style="font-size:13px;color:#5f6368;margin-top:4px;">Export data pengiriman kurir ke Excel (CSV) atau PDF</p>
</div>

{{-- Filter --}}
<div class="filter-card">
    <form id="filterForm" method="GET">
        <div class="filter-grid">
            <div>
                <label class="form-label">Kurir</label>
                <select name="courier_id" class="form-control">
                    <option value="">Semua Kurir</option>
                    @foreach($couriers as $c)
                    <option value="{{ $c->id }}" {{ request('courier_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Prescription::statusList() as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tanggal Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div>
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
        </div>

        <div class="export-btns">
            <button type="button" onclick="previewData()" class="btn-export" style="background:#1a73e8;color:#fff;">
                <span class="material-icons" style="font-size:16px;">search</span> Preview
            </button>
            <button type="button" onclick="exportFile('excel')" class="btn-export btn-excel">
                <span class="material-icons" style="font-size:16px;">table_view</span> Export Excel (CSV)
            </button>
            <button type="button" onclick="exportFile('pdf')" class="btn-export btn-pdf">
                <span class="material-icons" style="font-size:16px;">picture_as_pdf</span> Export PDF
            </button>
        </div>
    </form>
</div>

{{-- Preview Table --}}
<div class="card" id="previewCard" style="display:none;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div style="font-size:14px;font-weight:500;color:#202124;display:flex;align-items:center;gap:8px;">
            <span class="material-icons" style="color:#1a73e8;font-size:18px;">table_view</span>
            Preview Data
        </div>
        <span id="previewCount" style="font-size:12px;color:#5f6368;"></span>
    </div>
    <div style="overflow-x:auto;">
        <table class="preview-table">
            <thead>
                <tr>
                    <th>No. Resep</th>
                    <th>Tanggal</th>
                    <th>Pasien</th>
                    <th>Alamat</th>
                    <th>Kurir</th>
                    <th>Kendaraan</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody id="previewBody">
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function getParams() {
    const form = document.getElementById('filterForm');
    return new URLSearchParams(new FormData(form)).toString();
}

async function previewData() {
    const res  = await fetch(`{{ route('admin.reports.preview') }}?` + getParams());
    const data = await res.json();

    const tbody = document.getElementById('previewBody');
    tbody.innerHTML = '';

    data.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="font-weight:500;">${p.nomor_resep}</td>
            <td>${p.tanggal}</td>
            <td>${p.patient}</td>
            <td style="max-width:200px;white-space:normal;">${p.address}</td>
            <td>${p.kurir}</td>
            <td>${p.kendaraan}</td>
            <td><span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;background:#f1f3f4;color:#202124;">${p.status_label}</span></td>
            <td style="color:#5f6368;">${p.updated_at}</td>`;
        tbody.appendChild(tr);
    });

    document.getElementById('previewCount').textContent = `${data.length} data ditemukan`;
    document.getElementById('previewCard').style.display = 'block';
}

function exportFile(type) {
    const params = getParams();
    if (type === 'excel') {
        window.location = `{{ route('admin.reports.excel') }}?` + params;
    } else {
        window.location = `{{ route('admin.reports.pdf') }}?` + params;
    }
}
</script>
@endpush
