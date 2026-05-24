@extends('layouts.admin')
@section('title', 'Catatan Resep')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
    <p style="font-size:13px; color:#5f6368;">Kelola catatan resep dan pengiriman obat ke pasien</p>
    <button onclick="openCreateModal()" class="btn-primary">
        <span class="material-icons" style="font-size:18px;">add</span>
        Tambah Resep
    </button>
</div>

{{-- Filter Status --}}
<div style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
    <button onclick="filterStatus('')" id="filter-all"
        style="padding:6px 14px; border-radius:100px; font-size:13px; font-weight:500; border:1px solid #1a73e8; background:#e8f0fe; color:#1a73e8; cursor:pointer;">
        Semua
    </button>
    @foreach(\App\Models\Prescription::statusList() as $key => $label)
    <button onclick="filterStatus('{{ $key }}')" id="filter-{{ $key }}"
        style="padding:6px 14px; border-radius:100px; font-size:13px; font-weight:500; border:1px solid #dadce0; background:#fff; color:#3c4043; cursor:pointer; transition:all 0.15s;">
        {{ $label }}
    </button>
    @endforeach
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table id="prescriptionsTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nomor Resep</th>
                <th>Pasien</th>
                <th>Alamat</th>
                <th>Keterangan</th>
                <th>Kurir</th>
                <th>Status</th>
                <th>Aktif</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

{{-- ===== MODAL RESEP ===== --}}
<div id="prescriptionModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 modal-overlay" onclick="closeModal()"></div>
    <div class="modal-card relative w-full max-w-xl mx-4 z-10">

        <div class="modal-header">
            <h3 id="modalTitle">Tambah Catatan Resep</h3>
            <button onclick="closeModal()" class="modal-close">&times;</button>
        </div>

        <form id="prescriptionForm">
            <div class="modal-body" style="max-height:65vh; overflow-y:auto;">
                <input type="hidden" id="prescriptionId">

                {{-- Nomor & Tanggal --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div>
                        <label class="form-label">Nomor Resep</label>
                        <input type="text" id="nomor_resep" readonly
                            style="width:100%; border:1px solid #dadce0; border-radius:4px; padding:8px 12px; font-size:14px; background:#f8f9fa; color:#5f6368; font-family:'Google Sans',sans-serif; box-sizing:border-box;">
                        <p style="font-size:11px; color:#80868b; margin-top:2px;">Otomatis dibuat sistem</p>
                    </div>
                    <div>
                        <label class="form-label">Tanggal <span style="color:#c5221f">*</span></label>
                        <input type="date" id="tanggal" class="form-input">
                        <p id="err_tanggal" class="form-error hidden"></p>
                    </div>
                </div>

                {{-- Pasien --}}
                <div style="margin-bottom:14px;">
                    <label class="form-label">Pasien <span style="color:#c5221f">*</span></label>
                    <select id="patient_id" class="form-input" onchange="loadAddresses(this.value)">
                        <option value="">— Pilih Pasien —</option>
                        @foreach(\App\Models\Patient::where('is_active', true)->orderBy('name')->get() as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->rm }})</option>
                        @endforeach
                    </select>
                    <p id="err_patient_id" class="form-error hidden"></p>
                </div>

                {{-- Alamat --}}
                <div style="margin-bottom:14px;">
                    <label class="form-label">Alamat Pengiriman</label>
                    <select id="patient_address_id" class="form-input">
                        <option value="">— Pilih pasien terlebih dahulu —</option>
                    </select>
                    <p id="err_patient_address_id" class="form-error hidden"></p>
                </div>

                {{-- Kurir (hanya Admin, karena kurir menentukan sendiri) --}}
                @if(auth()->user()->isSuperAdmin())
                <div style="margin-bottom:14px;">
                    <label class="form-label">Kurir <span style="font-size:12px; color:#80868b; font-weight:400;">(opsional — kurir bisa pilih sendiri)</span></label>
                    <select id="courier_id" class="form-input">
                        <option value="">— Kurir belum memilih —</option>
                        @foreach(\App\Models\Courier::where('is_active', true)->orderBy('name')->get() as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }} — {{ $courier->plate_number }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" id="courier_id" value="">
                @endif

                {{-- Status (hanya saat edit) --}}
                <div id="statusField" style="margin-bottom:14px;" class="hidden">
                    <label class="form-label">Status</label>
                    <select id="status" class="form-input">
                        @foreach(\App\Models\Prescription::statusList() as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Aktif (hanya saat edit) --}}
                <div id="activeField" style="margin-bottom:14px;" class="hidden">
                    <label class="form-label">Aktif</label>
                    <select id="is_active" class="form-input">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                {{-- Keterangan --}}
                <div style="margin-bottom:4px;">
                    <label class="form-label">Keterangan</label>
                    <textarea id="keterangan" rows="3" class="form-input" placeholder="Catatan tambahan untuk resep ini..."></textarea>
                </div>
            </div>
        </form>

        <div class="modal-footer" style="border-top:1px solid #e0e0e0;">
            <button onclick="closeModal()" class="btn-secondary">Batal</button>
            <button onclick="submitForm()" id="submitBtn" class="btn-primary">Simpan</button>
        </div>
    </div>
</div>

{{-- ===== MODAL HAPUS ===== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="modal-card relative w-full max-w-sm mx-4 z-10 p-6 text-center">
        <span class="material-icons" style="font-size:48px; color:#c5221f; margin-bottom:12px; display:block;">delete_outline</span>
        <h3 style="font-size:16px; font-weight:500; color:#202124; margin-bottom:6px;">Hapus Resep?</h3>
        <p id="deleteTargetName" style="font-size:13px; color:#5f6368; margin-bottom:20px;"></p>
        <div style="display:flex; justify-content:center; gap:8px;">
            <button onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
            <button onclick="confirmDelete()" id="confirmDeleteBtn"
                style="background:#c5221f; color:#fff; border:none; border-radius:4px; padding:8px 16px; font-size:14px; font-weight:500; cursor:pointer;">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL FOTO BUKTI ===== --}}
<div id="photoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 modal-overlay" onclick="closePhotoModal()"></div>
    <div class="modal-card relative w-full max-w-lg mx-4 z-10" style="overflow:hidden;">
        <div class="modal-header">
            <h3 id="photoModalTitle" style="display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:18px;color:#1a73e8;">photo_camera</span>
                Foto Bukti Pengiriman
            </h3>
            <button onclick="closePhotoModal()" class="modal-close">&times;</button>
        </div>
        <div style="padding:0;background:#000;text-align:center;max-height:60vh;overflow:hidden;">
            <img id="photoModalImg" src="" alt="Foto Bukti Pengiriman"
                style="max-width:100%;max-height:60vh;object-fit:contain;display:block;margin:0 auto;">
        </div>
        <div class="modal-footer" style="justify-content:space-between;align-items:center;">
            <span id="photoModalNomor" style="font-size:12px;color:#5f6368;font-family:monospace;"></span>
            <div style="display:flex;gap:8px;">
                <a id="photoModalDownload" href="#" download target="_blank" class="btn-secondary" style="text-decoration:none;">
                    <span class="material-icons" style="font-size:15px;vertical-align:-3px;">download</span>
                    Unduh
                </a>
                <button onclick="closePhotoModal()" class="btn-secondary">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let table, deleteTargetId = null;
const isAdmin = {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }};
const baseUrl = isAdmin ? '/admin' : '/farmasi';

// ===== STATUS CONFIG =====
const statusConfig = {
    penyiapan:        { label: 'Proses Penyiapan Obat', color: 'orange' },
    siap_kirim:       { label: 'Siap Kirim',            color: 'blue'   },
    dibawa:           { label: 'Dibawa Kurir',          color: 'teal'   },
    dalam_pengiriman: { label: 'Dalam Pengiriman',      color: 'purple' },
    terkirim:         { label: 'Terkirim',              color: 'green'  },
    dibatalkan:       { label: 'Dibatalkan',            color: 'red'    },
};

function statusBadge(status, label) {
    const colors = {
        orange: 'background:#fef3e2; color:#b06000;',
        blue:   'background:#e8f0fe; color:#1a73e8;',
        teal:   'background:#ccfbf1; color:#0d6e64;',
        purple: 'background:#f3e8fd; color:#7627bb;',
        green:  'background:#e6f4ea; color:#137333;',
        red:    'background:#fce8e6; color:#c5221f;',
        gray:   'background:#f1f3f4; color:#5f6368;',
    };
    const cfg = statusConfig[status] ?? { color: 'gray' };
    return `<span style="${colors[cfg.color]} padding:3px 10px; border-radius:100px; font-size:12px; font-weight:500;">${label}</span>`;
}

// ===== DATATABLE =====
$(document).ready(function () {
    table = $('#prescriptionsTable').DataTable({
        processing: true,
        ajax: { url: `${baseUrl}/prescriptions`, headers: { 'Accept': 'application/json' }, dataSrc: 'data' },
        columns: [
            { data: 'tanggal',      width: '90px' },
            { data: 'nomor_resep',  render: d => `<span style="font-family:monospace; font-size:12px; color:#1a73e8; font-weight:500;">${d}</span>` },
            { data: 'patient_name', render: d => `<span style="font-weight:500;">${d}</span>` },
            { data: 'address',      render: d => `<span style="font-size:12px; color:#5f6368; max-width:180px; display:block;">${d}</span>` },
            { data: 'keterangan',   render: d => d !== '-' ? `<span style="font-size:12px; color:#5f6368;">${d}</span>` : '<span style="color:#ccc;">—</span>' },
            { data: 'courier_name', render: d => `<span style="font-size:13px;">${d}</span>` },
            { data: 'status',       render: (d, t, row) => statusBadge(d, row.status_label) },
            { data: 'is_active',    render: d => d
                ? '<span class="badge badge-green">Aktif</span>'
                : '<span class="badge badge-red">Nonaktif</span>' },
            { data: null, orderable: false, searchable: false,
              render: row => {
                // Farmasi: hanya boleh edit/hapus saat status penyiapan
                const canEdit = isAdmin || row.status === 'penyiapan';
                let btns = '';

                if (canEdit) {
                    btns += `<button onclick="openEditModal(${row.id})" class="table-action edit">Edit</button>
                    <button onclick="openDeleteModal(${row.id}, '${row.nomor_resep}')" class="table-action delete" style="margin-left:4px;">Hapus</button>`;
                } else {
                    btns += `<span style="font-size:12px;color:#9aa0a6;font-style:italic;">
                        <span class="material-icons" style="font-size:13px;vertical-align:-3px;">lock</span>
                        Terkunci</span>`;
                }

                if (isAdmin && row.status === 'dalam_pengiriman') {
                    btns += ` <a href="/admin/prescriptions/${row.id}/track" class="table-action green" style="margin-left:4px;text-decoration:none;">
                        <span class="material-icons" style="font-size:14px;vertical-align:-3px;">location_on</span>Track</a>`;
                }

                if (row.delivery_photo) {
                    btns += ` <button onclick="viewPhoto('${row.delivery_photo}', '${row.nomor_resep}')" class="table-action" style="margin-left:4px;background:#e8f0fe;color:#1a73e8;border:1px solid #c5d9f7;">
                        <span class="material-icons" style="font-size:14px;vertical-align:-3px;">photo_camera</span>Foto</button>`;
                }

                return btns;
              } }
        ],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_-_END_ dari _TOTAL_ resep',
            infoEmpty: 'Tidak ada data',
            zeroRecords: 'Data tidak ditemukan',
            paginate: { previous: '‹', next: '›' },
            processing: '<div style="color:#1a73e8; font-size:14px;">Memuat data...</div>',
        },
        order: [[0, 'desc']],
    });
});

// ===== FILTER STATUS =====
let activeFilter = '';
function filterStatus(status) {
    activeFilter = status;
    // Update button styles
    document.querySelectorAll('[id^="filter-"]').forEach(btn => {
        btn.style.background = '#fff';
        btn.style.color      = '#3c4043';
        btn.style.borderColor= '#dadce0';
    });
    const active = document.getElementById(status ? `filter-${status}` : 'filter-all');
    if (active) {
        active.style.background  = '#e8f0fe';
        active.style.color       = '#1a73e8';
        active.style.borderColor = '#1a73e8';
    }
    // Filter DataTable
    table.column(6).search(status ? statusConfig[status]?.label ?? '' : '').draw();
}

// ===== LOAD ADDRESSES =====
function loadAddresses(patientId, selectedId = null) {
    const $select = $('#patient_address_id');
    $select.html('<option value="">Memuat alamat...</option>');

    if (!patientId) {
        $select.html('<option value="">— Pilih pasien terlebih dahulu —</option>');
        return;
    }

    $.get(`${baseUrl}/prescriptions/addresses-by-patient/${patientId}`, function (addresses) {
        $select.html('<option value="">— Pilih alamat —</option>');
        if (addresses.length === 0) {
            $select.html('<option value="">Pasien belum memiliki alamat</option>');
            return;
        }
        addresses.forEach(a => {
            const label    = a.is_primary ? `⭐ [${a.label}] ${a.address}` : `[${a.label}] ${a.address}`;
            const selected = a.id == selectedId ? 'selected' : '';
            $select.append(`<option value="${a.id}" ${selected}>${label}</option>`);
        });
    });
}

// ===== MODAL =====
function openCreateModal() {
    resetForm();
    $('#modalTitle').text('Tambah Catatan Resep');
    $('#prescriptionId').val('');
    $('#tanggal').val(new Date().toISOString().split('T')[0]);
    $('#nomor_resep').val('(otomatis)');
    $('#statusField').addClass('hidden');
    $('#activeField').addClass('hidden');
    showModal();
}

function openEditModal(id) {
    resetForm();
    $('#modalTitle').text('Edit Resep');
    $('#prescriptionId').val(id);
    $('#statusField').removeClass('hidden');
    $('#activeField').removeClass('hidden');

    $.get(`${baseUrl}/prescriptions/${id}`, function (p) {
        $('#nomor_resep').val(p.nomor_resep);
        $('#tanggal').val(p.tanggal);
        $('#keterangan').val(p.keterangan ?? '');
        $('#status').val(p.status);
        $('#is_active').val(p.is_active ? '1' : '0');
        $('#patient_id').val(p.patient_id);
        $('#courier_id').val(p.courier_id ?? '');
        loadAddresses(p.patient_id, p.patient_address_id);
        showModal();
    });
}

function showModal()  { $('#prescriptionModal').removeClass('hidden').addClass('flex'); }
function closeModal() { $('#prescriptionModal').addClass('hidden').removeClass('flex'); resetForm(); }

// ===== SUBMIT =====
function submitForm() {
    clearErrors();
    const id  = $('#prescriptionId').val();
    const url = id ? `${baseUrl}/prescriptions/${id}` : `${baseUrl}/prescriptions`;
    const data = {
        _method:             id ? 'PATCH' : 'POST',
        tanggal:             $('#tanggal').val(),
        patient_id:          $('#patient_id').val(),
        patient_address_id:  $('#patient_address_id').val(),
        courier_id:          $('#courier_id').val(),
        keterangan:          $('#keterangan').val(),
        status:              $('#status').val() || 'penyiapan',
        is_active:           $('#is_active').val() || '1',
    };
    $('#submitBtn').text('Menyimpan...').prop('disabled', true);
    $.ajax({ url, method: 'POST', data,
        success: res => { closeModal(); table.ajax.reload(null, false); toastr.success(res.message, 'Berhasil!'); },
        error: xhr => {
            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, (f, m) => $(`#err_${f}`).text(m[0]).removeClass('hidden'));
                toastr.error('Periksa kembali form Anda.', 'Validasi Gagal');
            } else toastr.error('Terjadi kesalahan.', 'Error');
        },
        complete: () => $('#submitBtn').html('<span class="material-icons" style="font-size:18px;">add</span> Simpan').prop('disabled', false),
    });
}

// ===== HAPUS =====
function openDeleteModal(id, nomor) {
    deleteTargetId = id;
    $('#deleteTargetName').text(`Resep nomor "${nomor}" akan dihapus permanen.`);
    $('#deleteModal').removeClass('hidden').addClass('flex');
}
function closeDeleteModal() { deleteTargetId = null; $('#deleteModal').addClass('hidden').removeClass('flex'); }
function confirmDelete() {
    if (!deleteTargetId) return;
    $('#confirmDeleteBtn').text('Menghapus...').prop('disabled', true);
    $.ajax({ url: `${baseUrl}/prescriptions/${deleteTargetId}`, method: 'POST', data: { _method: 'DELETE' },
        success: res => { closeDeleteModal(); table.ajax.reload(null, false); toastr.success(res.message, 'Berhasil!'); },
        error: xhr => toastr.error(xhr.responseJSON?.message ?? 'Gagal.', 'Error'),
        complete: () => $('#confirmDeleteBtn').text('Ya, Hapus').prop('disabled', false),
    });
}

// ===== HELPERS =====
function resetForm()   { $('#prescriptionForm')[0].reset(); $('#prescriptionId').val(''); clearErrors(); }
function clearErrors() { $('[id^="err_"]').addClass('hidden').text(''); }
$(document).keydown(e => { if (e.key === 'Escape') { closeModal(); closeDeleteModal(); closePhotoModal(); } });

// ===== FOTO BUKTI =====
function viewPhoto(url, nomor) {
    $('#photoModalImg').attr('src', url);
    $('#photoModalNomor').text(nomor);
    $('#photoModalDownload').attr('href', url);
    $('#photoModal').removeClass('hidden').addClass('flex');
}
function closePhotoModal() {
    $('#photoModal').addClass('hidden').removeClass('flex');
    $('#photoModalImg').attr('src', '');
}
</script>
@endpush
