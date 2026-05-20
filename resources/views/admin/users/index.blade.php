@extends('layouts.admin')
@section('title', 'Manajemen User')

@section('content')

<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-500">Kelola akun Super Admin, Farmasi, dan Kurir</p>
    <button onclick="openCreateModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
        + Tambah User
    </button>
</div>

<div class="bg-white rounded-xl shadow p-5">
    <table id="usersTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

{{-- ===== MODAL FORM ===== --}}
<div id="userModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 z-10">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 id="modalTitle" class="text-base font-semibold text-gray-800">Tambah User</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        <form id="userForm" class="px-6 py-4 space-y-4">
            @csrf
            <input type="hidden" id="userId">
            <input type="hidden" id="formMethod" value="POST">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="name" placeholder="Nama lengkap"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p id="err_name" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" placeholder="email@domain.com"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p id="err_email" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" id="phone" placeholder="08xxxxxxxxxx"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p id="err_phone" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select id="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="farmasi">Farmasi</option>
                        <option value="kurir">Kurir</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                    <p id="err_role" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <div id="statusField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="is_active"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- Info kurir --}}
            <div id="kurirInfo" class="hidden bg-orange-50 border border-orange-200 rounded-lg px-3 py-2 text-xs text-orange-700">
                🚗 Akun kurir hanya dapat digunakan melalui <strong>Aplikasi Android MediTrack</strong>. Login via web akan ditolak.
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password <span class="text-red-500" id="passwordRequired">*</span>
                    <span id="passwordOptional" class="text-gray-400 font-normal hidden">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" id="password" placeholder="Minimal 8 karakter"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p id="err_password" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" placeholder="Ulangi password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </form>

        <div class="px-6 py-4 border-t flex justify-end gap-2">
            <button onclick="closeModal()"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition">
                Batal
            </button>
            <button onclick="submitForm()" id="submitBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg transition">
                Simpan
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL HAPUS ===== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 z-10 p-6 text-center">
        <div class="text-5xl mb-3">🗑️</div>
        <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus User?</h3>
        <p id="deleteUserName" class="text-sm text-gray-500 mb-5"></p>
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteModal()"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-5 py-2 rounded-lg transition">
                Batal
            </button>
            <button onclick="confirmDelete()" id="confirmDeleteBtn"
                class="bg-red-600 hover:bg-red-700 text-white text-sm px-5 py-2 rounded-lg transition">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let table;
let deleteUserId = null;

const roleBadge = {
    superadmin: '<span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Super Admin</span>',
    farmasi:    '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Farmasi</span>',
    kurir:      '<span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Kurir</span>',
};

// ===== DATATABLE =====
$(document).ready(function () {
    table = $('#usersTable').DataTable({
        processing: true,
        ajax: {
            url: '{{ route("admin.users.index") }}',
            headers: { 'Accept': 'application/json' },
            dataSrc: 'data',
        },
        columns: [
            { data: 'name' },
            { data: 'email' },
            { data: 'phone', render: d => d ?? '<span class="text-gray-400">-</span>' },
            { data: 'role',      render: d => roleBadge[d] ?? d },
            {
                data: 'is_active',
                render: d => d
                    ? '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>'
                    : '<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>'
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: row => `
                    <button onclick="openEditModal(${row.id})"
                        class="text-blue-600 hover:text-blue-800 text-xs font-medium mr-3">Edit</button>
                    <button onclick="openDeleteModal(${row.id}, '${row.name}')"
                        class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                `
            }
        ],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_-_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            zeroRecords: 'Data tidak ditemukan',
            paginate: { previous: '‹', next: '›' },
            processing: '<div class="text-blue-600 text-sm py-2">Memuat data...</div>',
        },
        order: [[0, 'asc']],
    });

    // Tampilkan info kurir saat role berubah
    $('#role').on('change', function () {
        toggleKurirInfo($(this).val());
    });
});

function toggleKurirInfo(role) {
    if (role === 'kurir') {
        $('#kurirInfo').removeClass('hidden');
    } else {
        $('#kurirInfo').addClass('hidden');
    }
}

// ===== MODAL =====
function openCreateModal() {
    resetForm();
    $('#modalTitle').text('Tambah User');
    $('#formMethod').val('POST');
    $('#userId').val('');
    $('#statusField').addClass('hidden');
    $('#passwordRequired').removeClass('hidden');
    $('#passwordOptional').addClass('hidden');
    showModal();
}

function openEditModal(id) {
    resetForm();
    $('#modalTitle').text('Edit User');
    $('#formMethod').val('PUT');
    $('#userId').val(id);
    $('#statusField').removeClass('hidden');
    $('#passwordRequired').addClass('hidden');
    $('#passwordOptional').removeClass('hidden');

    $.get(`/admin/users/${id}`, function (user) {
        $('#name').val(user.name);
        $('#email').val(user.email);
        $('#phone').val(user.phone ?? '');
        $('#role').val(user.role);
        $('#is_active').val(user.is_active ? '1' : '0');
        toggleKurirInfo(user.role);
        showModal();
    });
}

function showModal() {
    $('#userModal').removeClass('hidden').addClass('flex');
}

function closeModal() {
    $('#userModal').addClass('hidden').removeClass('flex');
    resetForm();
}

function openDeleteModal(id, name) {
    deleteUserId = id;
    $('#deleteUserName').text(`User "${name}" akan dihapus permanen.`);
    $('#deleteModal').removeClass('hidden').addClass('flex');
}

function closeDeleteModal() {
    deleteUserId = null;
    $('#deleteModal').addClass('hidden').removeClass('flex');
}

// ===== SUBMIT =====
function submitForm() {
    clearErrors();
    const id     = $('#userId').val();
    const method = $('#formMethod').val();
    const url    = id ? `/admin/users/${id}` : '/admin/users';

    const data = {
        _method:               method === 'PUT' ? 'PATCH' : 'POST',
        name:                  $('#name').val(),
        email:                 $('#email').val(),
        phone:                 $('#phone').val(),
        role:                  $('#role').val(),
        is_active:             $('#is_active').val(),
        password:              $('#password').val(),
        password_confirmation: $('#password_confirmation').val(),
    };

    $('#submitBtn').text('Menyimpan...').prop('disabled', true);

    $.ajax({
        url, method: 'POST', data,
        success: function (res) {
            closeModal();
            table.ajax.reload(null, false);
            toastr.success(res.message, 'Berhasil!');
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                $.each(errors, function (field, messages) {
                    $(`#err_${field}`).text(messages[0]).removeClass('hidden');
                });
                toastr.error('Periksa kembali form Anda.', 'Validasi Gagal');
            } else {
                toastr.error('Terjadi kesalahan. Coba lagi.', 'Error');
            }
        },
        complete: function () {
            $('#submitBtn').text('Simpan').prop('disabled', false);
        }
    });
}

// ===== HAPUS =====
function confirmDelete() {
    if (!deleteUserId) return;
    $('#confirmDeleteBtn').text('Menghapus...').prop('disabled', true);

    $.ajax({
        url: `/admin/users/${deleteUserId}`,
        method: 'POST',
        data: { _method: 'DELETE' },
        success: function (res) {
            closeDeleteModal();
            table.ajax.reload(null, false);
            toastr.success(res.message, 'Berhasil!');
        },
        error: function (xhr) {
            toastr.error(xhr.responseJSON?.message ?? 'Terjadi kesalahan.', 'Gagal');
        },
        complete: function () {
            $('#confirmDeleteBtn').text('Ya, Hapus').prop('disabled', false);
        }
    });
}

// ===== HELPERS =====
function resetForm() {
    $('#userForm')[0].reset();
    $('#password').val('');
    $('#password_confirmation').val('');
    $('#kurirInfo').addClass('hidden');
    clearErrors();
}

function clearErrors() {
    $('[id^="err_"]').addClass('hidden').text('');
}

$(document).keydown(e => {
    if (e.key === 'Escape') { closeModal(); closeDeleteModal(); }
});
</script>
@endpush
