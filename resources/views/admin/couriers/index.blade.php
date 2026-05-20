@extends('layouts.admin')
@section('title', 'Data Kurir')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
    <p style="font-size:13px; color:#5f6368;">Kelola data kurir pengiriman obat</p>
    <button onclick="openCreateModal()" class="btn-primary">
        <span class="material-icons" style="font-size:18px;">add</span>
        Tambah Kurir
    </button>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table id="couriersTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>Plat Nomor</th>
                <th>No. HP</th>
                <th>Akun Login Android</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

{{-- ===== MODAL KURIR ===== --}}
<div id="courierModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 modal-overlay" onclick="closeModal()"></div>
    <div class="modal-card relative w-full max-w-lg mx-4 z-10">

        <div class="modal-header">
            <h3 id="modalTitle">Tambah Kurir</h3>
            <button onclick="closeModal()" class="modal-close">&times;</button>
        </div>

        <form id="courierForm">
            <div class="modal-body" style="max-height:68vh; overflow-y:auto;">
                <input type="hidden" id="courierId">

                {{-- Data Profil --}}
                <div style="margin-bottom:6px;">
                    <p style="font-size:12px; font-weight:500; color:#80868b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">Data Profil Kurir</p>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div style="grid-column:1/-1;">
                        <label class="form-label">NIK <span style="color:#c5221f">*</span></label>
                        <input type="text" id="nik" maxlength="16" placeholder="16 digit NIK" class="form-input">
                        <p id="err_nik" class="form-error hidden"></p>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="form-label">Nama Lengkap <span style="color:#c5221f">*</span></label>
                        <input type="text" id="name" placeholder="Nama lengkap kurir" class="form-input">
                        <p id="err_name" class="form-error hidden"></p>
                    </div>
                    <div>
                        <label class="form-label">Plat Nomor <span style="color:#c5221f">*</span></label>
                        <input type="text" id="plate_number" placeholder="BM 1234 AB" class="form-input" style="text-transform:uppercase;">
                        <p id="err_plate_number" class="form-error hidden"></p>
                    </div>
                    <div>
                        <label class="form-label">No. HP <span style="color:#c5221f">*</span></label>
                        <input type="text" id="phone" placeholder="08xxxxxxxxxx" class="form-input">
                        <p id="err_phone" class="form-error hidden"></p>
                    </div>
                </div>

                {{-- Status (edit only) --}}
                <div id="statusField" class="hidden" style="margin-bottom:14px;">
                    <label class="form-label">Status</label>
                    <select id="is_active" class="form-input">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                {{-- Divider --}}
                <div style="border-top:1px solid #e0e0e0; margin:16px 0;"></div>

                {{-- Akun Login --}}
                <p style="font-size:12px; font-weight:500; color:#80868b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                    Akun Login Android
                </p>

                {{-- Mode Selector --}}
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:16px;">
                    <label id="mode-none-label"
                        style="border:2px solid #1a73e8; border-radius:8px; padding:10px 8px; text-align:center; cursor:pointer; background:#e8f0fe;">
                        <input type="radio" name="account_mode" id="mode-none" value="none" checked style="display:none;">
                        <span class="material-icons" style="font-size:20px; color:#1a73e8; display:block; margin-bottom:2px;">link_off</span>
                        <span style="font-size:12px; font-weight:500; color:#1a73e8;">Tanpa Akun</span>
                    </label>
                    <label id="mode-existing-label"
                        style="border:2px solid #dadce0; border-radius:8px; padding:10px 8px; text-align:center; cursor:pointer; background:#fff;">
                        <input type="radio" name="account_mode" id="mode-existing" value="existing" style="display:none;">
                        <span class="material-icons" style="font-size:20px; color:#5f6368; display:block; margin-bottom:2px;">link</span>
                        <span style="font-size:12px; font-weight:500; color:#5f6368;">Hubungkan Akun</span>
                    </label>
                    <label id="mode-new-label"
                        style="border:2px solid #dadce0; border-radius:8px; padding:10px 8px; text-align:center; cursor:pointer; background:#fff;">
                        <input type="radio" name="account_mode" id="mode-new" value="new" style="display:none;">
                        <span class="material-icons" style="font-size:20px; color:#5f6368; display:block; margin-bottom:2px;">person_add</span>
                        <span style="font-size:12px; font-weight:500; color:#5f6368;">Buat Akun Baru</span>
                    </label>
                </div>

                {{-- Panel: Tanpa Akun --}}
                <div id="panel-none">
                    <div style="background:#f8f9fa; border:1px solid #e0e0e0; border-radius:6px; padding:12px 14px; font-size:13px; color:#5f6368; display:flex; align-items:center; gap:8px;">
                        <span class="material-icons" style="font-size:18px; color:#80868b;">info</span>
                        Kurir tidak memiliki akun login. Akun dapat ditambahkan kapan saja melalui menu Edit.
                    </div>
                </div>

                {{-- Panel: Hubungkan Akun Existing --}}
                <div id="panel-existing" class="hidden">
                    <label class="form-label">Pilih Akun Kurir <span style="color:#c5221f">*</span></label>
                    <select id="user_id" class="form-input">
                        <option value="">Memuat daftar akun...</option>
                    </select>
                    <p id="err_user_id" class="form-error hidden"></p>
                    <p style="font-size:12px; color:#80868b; margin-top:4px;">
                        Hanya menampilkan akun dengan role Kurir yang belum terhubung.
                    </p>
                </div>

                {{-- Panel: Buat Akun Baru --}}
                <div id="panel-new" class="hidden">
                    <div style="background:#e6f4ea; border:1px solid #a8d5b5; border-radius:6px; padding:10px 14px; font-size:12px; color:#137333; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                        <span class="material-icons" style="font-size:16px;">check_circle</span>
                        Akun login baru dengan role <strong>Kurir</strong> akan dibuat otomatis bersamaan dengan data profil kurir.
                    </div>
                    <div style="margin-bottom:12px;">
                        <label class="form-label">Email Login <span style="color:#c5221f">*</span></label>
                        <input type="email" id="login_email" placeholder="email@domain.com" class="form-input">
                        <p id="err_login_email" class="form-error hidden"></p>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label class="form-label">Password <span style="color:#c5221f">*</span></label>
                            <input type="password" id="login_password" placeholder="Min. 8 karakter" class="form-input">
                            <p id="err_login_password" class="form-error hidden"></p>
                        </div>
                        <div>
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" id="login_password_confirmation" placeholder="Ulangi password" class="form-input">
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <div class="modal-footer" style="border-top:1px solid #e0e0e0;">
            <button onclick="closeModal()" class="btn-secondary">Batal</button>
            <button onclick="submitForm()" id="submitBtn" class="btn-primary">
                <span class="material-icons" style="font-size:18px;">save</span> Simpan
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL HAPUS ===== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="modal-card relative w-full max-w-sm mx-4 z-10 p-6 text-center">
        <span class="material-icons" style="font-size:48px; color:#c5221f; margin-bottom:12px; display:block;">delete_outline</span>
        <h3 style="font-size:16px; font-weight:500; color:#202124; margin-bottom:6px;">Hapus Kurir?</h3>
        <p id="deleteTargetName" style="font-size:13px; color:#5f6368; margin-bottom:20px;"></p>
        <div style="display:flex; justify-content:center; gap:8px;">
            <button onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
            <button onclick="confirmDelete()" id="confirmDeleteBtn"
                style="background:#c5221f; color:#fff; border:none; border-radius:4px; padding:8px 16px; font-size:14px; font-weight:500; cursor:pointer; font-family:'Google Sans',sans-serif;">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let table, deleteTargetId = null;

// ===== DATATABLE =====
$(document).ready(function () {
    table = $('#couriersTable').DataTable({
        processing: true,
        ajax: { url: '{{ route("admin.couriers.index") }}', headers: { 'Accept': 'application/json' }, dataSrc: 'data' },
        columns: [
            { data: 'nik', render: d => `<span style="font-family:monospace; font-size:13px;">${d}</span>` },
            { data: 'name', render: d => `<span style="font-weight:500;">${d}</span>` },
            { data: 'plate_number', render: d => `<span style="font-family:monospace; font-weight:600; color:#1a73e8;">${d}</span>` },
            { data: 'phone' },
            {
                data: 'user',
                render: u => u
                    ? `<div style="display:flex; align-items:center; gap:6px;">
                            <span class="material-icons" style="font-size:16px; color:#137333;">verified_user</span>
                            <div>
                                <div style="font-size:13px; font-weight:500; color:#202124;">${u.name}</div>
                                <div style="font-size:11px; color:#80868b;">${u.email}</div>
                            </div>
                       </div>`
                    : `<span style="font-size:12px; color:#80868b; display:flex; align-items:center; gap:4px;">
                            <span class="material-icons" style="font-size:14px;">link_off</span> Belum terhubung
                       </span>`,
            },
            {
                data: 'is_active',
                render: d => d
                    ? '<span class="badge badge-green">Aktif</span>'
                    : '<span class="badge badge-red">Nonaktif</span>',
            },
            {
                data: null, orderable: false, searchable: false,
                render: row => `
                    <button onclick="openEditModal(${row.id})" class="table-action edit">Edit</button>
                    <button onclick="openDeleteModal(${row.id}, '${row.name}')" class="table-action delete" style="margin-left:4px;">Hapus</button>
                `,
            },
        ],
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_-_END_ dari _TOTAL_ kurir',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Data tidak ditemukan',
            paginate: { previous: '‹', next: '›' },
        },
        order: [[1, 'asc']],
    });

    // Mode selector click
    document.querySelectorAll('input[name="account_mode"]').forEach(radio => {
        radio.closest('label').addEventListener('click', () => {
            setTimeout(() => switchMode(radio.value), 10);
        });
    });
});

// ===== MODE SWITCH =====
function switchMode(mode) {
    const modes = ['none', 'existing', 'new'];
    modes.forEach(m => {
        const label = document.getElementById(`mode-${m}-label`);
        const panel = document.getElementById(`panel-${m}`);
        const icon  = label.querySelector('.material-icons');
        const text  = label.querySelector('span:last-child');
        if (m === mode) {
            label.style.borderColor = '#1a73e8';
            label.style.background  = '#e8f0fe';
            icon.style.color        = '#1a73e8';
            text.style.color        = '#1a73e8';
            panel.classList.remove('hidden');
        } else {
            label.style.borderColor = '#dadce0';
            label.style.background  = '#fff';
            icon.style.color        = '#5f6368';
            text.style.color        = '#5f6368';
            panel.classList.add('hidden');
        }
    });

    if (mode === 'existing') loadAvailableUsers();
}

function loadAvailableUsers(selectedId = null) {
    const courierId = $('#courierId').val();
    const url = `/admin/couriers/available-users${courierId ? `?exclude=${courierId}` : ''}`;
    $.get(url, function (users) {
        const $s = $('#user_id');
        $s.html('<option value="">— Pilih akun —</option>');
        if (users.length === 0) {
            $s.html('<option value="">Tidak ada akun kurir tersedia</option>');
        } else {
            users.forEach(u => {
                const active   = u.is_active ? '' : ' (nonaktif)';
                const selected = u.id == selectedId ? 'selected' : '';
                $s.append(`<option value="${u.id}" ${selected}>${u.name} — ${u.email}${active}</option>`);
            });
        }
        if (selectedId && !users.find(u => u.id == selectedId)) {
            $.get(`/admin/users/${selectedId}`, u => {
                $s.append(`<option value="${u.id}" selected>${u.name} — ${u.email}</option>`);
            });
        }
    });
}

// ===== MODAL =====
function openCreateModal() {
    resetForm();
    $('#modalTitle').text('Tambah Kurir');
    $('#courierId').val('');
    $('#statusField').addClass('hidden');
    switchMode('none');
    document.getElementById('mode-none').checked = true;
    showModal();
}

function openEditModal(id) {
    resetForm();
    $('#modalTitle').text('Edit Kurir');
    $('#courierId').val(id);
    $('#statusField').removeClass('hidden');

    $.get(`/admin/couriers/${id}`, function (c) {
        $('#nik').val(c.nik);
        $('#name').val(c.name);
        $('#plate_number').val(c.plate_number);
        $('#phone').val(c.phone);
        $('#is_active').val(c.is_active ? '1' : '0');

        if (c.user_id) {
            document.getElementById('mode-existing').checked = true;
            switchMode('existing');
            loadAvailableUsers(c.user_id);
        } else {
            document.getElementById('mode-none').checked = true;
            switchMode('none');
        }
        showModal();
    });
}

function showModal()  { $('#courierModal').removeClass('hidden').addClass('flex'); }
function closeModal() { $('#courierModal').addClass('hidden').removeClass('flex'); resetForm(); }

// ===== SUBMIT =====
function submitForm() {
    clearErrors();
    const id   = $('#courierId').val();
    const url  = id ? `/admin/couriers/${id}` : '/admin/couriers';
    const mode = $('input[name="account_mode"]:checked').val();

    const data = {
        _method:                      id ? 'PATCH' : 'POST',
        nik:                          $('#nik').val(),
        name:                         $('#name').val(),
        plate_number:                 $('#plate_number').val(),
        phone:                        $('#phone').val(),
        is_active:                    $('#is_active').val() || '1',
        account_mode:                 mode,
        user_id:                      mode === 'existing' ? $('#user_id').val() : '',
        login_email:                  mode === 'new' ? $('#login_email').val() : '',
        login_password:               mode === 'new' ? $('#login_password').val() : '',
        login_password_confirmation:  mode === 'new' ? $('#login_password_confirmation').val() : '',
    };

    $('#submitBtn').html('<span class="material-icons" style="font-size:18px;">hourglass_empty</span> Menyimpan...').prop('disabled', true);

    $.ajax({ url, method: 'POST', data,
        success: res => { closeModal(); table.ajax.reload(null, false); toastr.success(res.message, 'Berhasil!'); },
        error: xhr => {
            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, (f, m) => $(`#err_${f}`).text(m[0]).removeClass('hidden'));
                toastr.error('Periksa kembali form Anda.', 'Validasi Gagal');
            } else {
                toastr.error(xhr.responseJSON?.message ?? 'Terjadi kesalahan.', 'Error');
            }
        },
        complete: () => $('#submitBtn').html('<span class="material-icons" style="font-size:18px;">save</span> Simpan').prop('disabled', false),
    });
}

// ===== HAPUS =====
function openDeleteModal(id, name) {
    deleteTargetId = id;
    $('#deleteTargetName').text(`Data kurir "${name}" akan dihapus permanen.`);
    $('#deleteModal').removeClass('hidden').addClass('flex');
}
function closeDeleteModal() { deleteTargetId = null; $('#deleteModal').addClass('hidden').removeClass('flex'); }
function confirmDelete() {
    if (!deleteTargetId) return;
    $('#confirmDeleteBtn').text('Menghapus...').prop('disabled', true);
    $.ajax({ url: `/admin/couriers/${deleteTargetId}`, method: 'POST', data: { _method: 'DELETE' },
        success: res => { closeDeleteModal(); table.ajax.reload(null, false); toastr.success(res.message, 'Berhasil!'); },
        error: xhr => toastr.error(xhr.responseJSON?.message ?? 'Gagal.', 'Error'),
        complete: () => $('#confirmDeleteBtn').text('Ya, Hapus').prop('disabled', false),
    });
}

// ===== HELPERS =====
function resetForm() {
    $('#courierForm')[0].reset();
    $('#courierId').val('');
    $('#login_email').val('');
    $('#login_password').val('');
    $('#login_password_confirmation').val('');
    clearErrors();
}
function clearErrors() { $('[id^="err_"]').addClass('hidden').text(''); }
$(document).keydown(e => { if (e.key === 'Escape') { closeModal(); closeDeleteModal(); } });
</script>
@endpush
