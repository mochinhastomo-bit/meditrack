@extends('layouts.admin')
@section('title', 'Data Pasien')

@section('content')

<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-500">Kelola data pasien dan alamat pengiriman</p>
    <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
        + Tambah Pasien
    </button>
</div>

<div class="bg-white rounded-xl shadow p-5">
    <table id="patientsTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>NIK</th>
                <th>No. RM</th>
                <th>Nama</th>
                <th>Tgl. Lahir</th>
                <th>No. HP</th>
                <th>Alamat</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

{{-- ===== MODAL PASIEN ===== --}}
<div id="patientModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 z-10">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 id="modalTitle" class="text-base font-semibold text-gray-800">Tambah Pasien</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="patientForm" class="px-6 py-4 space-y-3">
            @csrf
            <input type="hidden" id="patientId">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                    <input type="text" id="nik" maxlength="16" placeholder="16 digit NIK"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="err_nik" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. RM <span class="text-red-500">*</span></label>
                    <input type="text" id="rm" placeholder="Nomor Rekam Medis"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="err_rm" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="name" placeholder="Nama lengkap pasien"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p id="err_name" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="birth_date"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="err_birth_date" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP <span class="text-red-500">*</span></label>
                    <input type="text" id="phone" placeholder="08xxxxxxxxxx"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="err_phone" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
            </div>
            <div id="statusFieldPatient" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </form>
        <div class="px-6 py-4 border-t flex justify-end gap-2">
            <button onclick="closeModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg">Batal</button>
            <button onclick="submitPatient()" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">Simpan</button>
        </div>
    </div>
</div>

{{-- ===== MODAL ALAMAT + GOOGLE MAPS ===== --}}
<div id="addressModal" class="fixed inset-0 z-40 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeAddressModal()"></div>
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 z-10 flex flex-col" style="max-height:92vh">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
            <div>
                <h3 class="text-base font-semibold text-gray-800">📍 Alamat Pasien</h3>
                <p id="addressPatientName" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openAddressForm()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg">+ Tambah Alamat</button>
                <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
        </div>

        {{-- Daftar Alamat --}}
        <div class="overflow-y-auto p-5 flex-1">
            <table id="addressTable" class="w-full" style="width:100%">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Alamat</th>
                        <th>Koordinat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        {{-- Form Tambah/Edit Alamat dengan Google Maps --}}
        <div id="addressForm" class="hidden border-t bg-gray-50 flex-shrink-0" style="max-height:70vh; overflow-y:auto">
            <div class="px-6 pt-4 pb-2">
                <h4 id="addressFormTitle" class="text-sm font-semibold text-gray-700 mb-3">Tambah Alamat</h4>
                <input type="hidden" id="addressId">

                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                        <select id="addr_label" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Rumah">🏠 Rumah</option>
                            <option value="Kantor">🏢 Kantor</option>
                            <option value="Lainnya">📌 Lainnya</option>
                        </select>
                    </div>
                    <div class="col-span-2 flex items-end">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer pb-1.5">
                            <input type="checkbox" id="is_primary" class="rounded w-4 h-4">
                            <span>Jadikan alamat utama <span class="text-yellow-500">⭐</span></span>
                        </label>
                    </div>
                </div>

                {{-- Google Maps Search --}}
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">🔍 Cari Lokasi</label>
                    <input id="mapSearchBox" type="text" placeholder="Ketik nama jalan, tempat, atau area..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Ketik untuk mencari, atau klik langsung di peta di bawah</p>
                </div>

                {{-- Google Maps Container --}}
                <div class="mb-3 rounded-xl overflow-hidden border border-gray-200" style="height:300px; position:relative;">
                    <div id="googleMap" style="width:100%; height:100%;"></div>
                    <div class="absolute top-2 right-2 bg-white rounded-lg shadow px-2 py-1 text-xs text-gray-500">
                        Klik peta atau geser marker 📍
                    </div>
                </div>

                {{-- Alamat & Koordinat --}}
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea id="addr_address" rows="2" placeholder="Alamat terisi otomatis saat klik peta, atau isi manual..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    <p id="err_address" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="text" id="addr_latitude" placeholder="Terisi otomatis dari peta"
                            class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p id="err_latitude" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="text" id="addr_longitude" placeholder="Terisi otomatis dari peta"
                            class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p id="err_longitude" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pb-4">
                    <button onclick="cancelAddressForm()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-1.5 rounded-lg">Batal</button>
                    <button onclick="submitAddress()" id="addressSubmitBtn" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1.5 rounded-lg">Simpan Alamat</button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ===== MODAL HAPUS ===== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 z-10 p-6 text-center">
        <div class="text-5xl mb-3">🗑️</div>
        <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Pasien?</h3>
        <p id="deleteTargetName" class="text-sm text-gray-500 mb-5"></p>
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-5 py-2 rounded-lg">Batal</button>
            <button onclick="confirmDelete()" id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white text-sm px-5 py-2 rounded-lg">Ya, Hapus</button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Hilangkan spinner number input */
    #addr_latitude::-webkit-outer-spin-button,
    #addr_latitude::-webkit-inner-spin-button,
    #addr_longitude::-webkit-outer-spin-button,
    #addr_longitude::-webkit-inner-spin-button { -webkit-appearance: none; }
</style>
@endpush

@push('scripts')
{{-- Google Maps API --}}
<script>
    window.GMAPS_KEY = '{{ config("services.google_maps.key") }}';
</script>

<script>
let table, addrTable, deleteTargetId = null;
let currentPatientId = null;
let map, marker, geocoder, searchBox, placesService;
let mapInitialized = false;

// ===== PATIENTS DATATABLE =====
$(document).ready(function () {
    table = $('#patientsTable').DataTable({
        processing: true,
        ajax: { url: '{{ route("admin.patients.index") }}', headers: { 'Accept': 'application/json' }, dataSrc: 'data' },
        columns: [
            { data: 'nik' },
            { data: 'rm' },
            { data: 'name' },
            { data: 'birth_date', render: d => d ? new Date(d).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) : '-' },
            { data: 'phone' },
            { data: 'addresses_count', render: d => `<span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">${d} alamat</span>` },
            { data: 'is_active', render: d => d
                ? '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>'
                : '<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>' },
            { data: null, orderable: false, searchable: false,
              render: row => `
                <button onclick="openAddressModal(${row.id}, '${row.name.replace(/'/g,"\\'")}')" class="text-green-600 hover:text-green-800 text-xs font-medium mr-2">📍 Alamat</button>
                <button onclick="openEditModal(${row.id})" class="text-blue-600 hover:text-blue-800 text-xs font-medium mr-2">Edit</button>
                <button onclick="openDeleteModal(${row.id}, '${row.name.replace(/'/g,"\\'")}')" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
              ` }
        ],
        language: { search:'Cari:', lengthMenu:'Tampilkan _MENU_ data', info:'Menampilkan _START_-_END_ dari _TOTAL_ data', infoEmpty:'Tidak ada data', zeroRecords:'Data tidak ditemukan', paginate:{previous:'‹',next:'›'} },
        order: [[2, 'asc']],
    });
});

// ===== GOOGLE MAPS =====
function loadGoogleMaps() {
    if (document.getElementById('gmaps-script')) return;
    const script = document.createElement('script');
    script.id  = 'gmaps-script';
    script.src = `https://maps.googleapis.com/maps/api/js?key=${window.GMAPS_KEY}&libraries=places&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

window.initMap = function () {
    // Default center: Pekanbaru, Riau
    const defaultCenter = { lat: -0.5070677, lng: 101.4477793 };

    map = new google.maps.Map(document.getElementById('googleMap'), {
        center: defaultCenter,
        zoom: 13,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
    });

    geocoder = new google.maps.Geocoder();

    // Marker draggable
    marker = new google.maps.Marker({
        map,
        position: defaultCenter,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: 'Geser untuk memilih lokasi',
    });

    // Klik peta → pindah marker + reverse geocode
    map.addListener('click', function (event) {
        placeMarker(event.latLng);
    });

    // Drag marker selesai → reverse geocode
    marker.addListener('dragend', function () {
        reverseGeocode(marker.getPosition());
    });

    // Places Autocomplete Search Box
    const input = document.getElementById('mapSearchBox');
    const autocomplete = new google.maps.places.Autocomplete(input, {
        componentRestrictions: { country: 'id' },
        fields: ['geometry', 'formatted_address', 'name'],
    });

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;

        map.setCenter(place.geometry.location);
        map.setZoom(17);
        placeMarker(place.geometry.location);

        // Isi alamat dari Places
        if (place.formatted_address) {
            $('#addr_address').val(place.formatted_address);
        }
    });

    mapInitialized = true;

    // Jika ada koordinat existing (mode edit), pindah marker ke sana
    const existingLat = parseFloat($('#addr_latitude').val());
    const existingLng = parseFloat($('#addr_longitude').val());
    if (!isNaN(existingLat) && !isNaN(existingLng)) {
        const pos = new google.maps.LatLng(existingLat, existingLng);
        map.setCenter(pos);
        map.setZoom(17);
        marker.setPosition(pos);
    }
};

function placeMarker(latLng) {
    marker.setPosition(latLng);
    $('#addr_latitude').val(latLng.lat().toFixed(7));
    $('#addr_longitude').val(latLng.lng().toFixed(7));
    reverseGeocode(latLng);
}

function reverseGeocode(latLng) {
    geocoder.geocode({ location: latLng }, function (results, status) {
        if (status === 'OK' && results[0]) {
            // Isi field alamat hanya jika masih kosong atau user belum isi manual
            if (!$('#addr_address').val() || $('#addr_address').data('auto')) {
                $('#addr_address').val(results[0].formatted_address);
                $('#addr_address').data('auto', true);
            }
        }
    });
}

function moveMapToExistingCoords() {
    if (!mapInitialized) return;
    const lat = parseFloat($('#addr_latitude').val());
    const lng = parseFloat($('#addr_longitude').val());
    if (!isNaN(lat) && !isNaN(lng)) {
        const pos = new google.maps.LatLng(lat, lng);
        map.setCenter(pos);
        map.setZoom(17);
        marker.setPosition(pos);
    }
}

// ===== PATIENT MODAL =====
function openCreateModal() {
    resetForm();
    $('#modalTitle').text('Tambah Pasien');
    $('#patientId').val('');
    $('#statusFieldPatient').addClass('hidden');
    $('#patientModal').removeClass('hidden').addClass('flex');
}

function openEditModal(id) {
    resetForm();
    $('#modalTitle').text('Edit Pasien');
    $('#patientId').val(id);
    $('#statusFieldPatient').removeClass('hidden');
    $.get(`/admin/patients/${id}`, function (p) {
        $('#nik').val(p.nik);
        $('#rm').val(p.rm);
        $('#name').val(p.name);
        $('#birth_date').val(p.birth_date);
        $('#phone').val(p.phone);
        $('#is_active').val(p.is_active ? '1' : '0');
        $('#patientModal').removeClass('hidden').addClass('flex');
    });
}

function closeModal() { $('#patientModal').addClass('hidden').removeClass('flex'); resetForm(); }

function submitPatient() {
    clearErrors();
    const id  = $('#patientId').val();
    const url = id ? `/admin/patients/${id}` : '/admin/patients';
    const data = {
        _method: id ? 'PATCH' : 'POST',
        nik: $('#nik').val(), rm: $('#rm').val(), name: $('#name').val(),
        birth_date: $('#birth_date').val(), phone: $('#phone').val(), is_active: $('#is_active').val(),
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
        complete: () => $('#submitBtn').text('Simpan').prop('disabled', false),
    });
}

// ===== ADDRESS MODAL =====
function openAddressModal(patientId, patientName) {
    currentPatientId = patientId;
    $('#addressPatientName').text(patientName);
    $('#addressModal').removeClass('hidden').addClass('flex');
    cancelAddressForm();
    loadAddressTable();
    loadGoogleMaps(); // Load Maps API lazy
}

function closeAddressModal() {
    $('#addressModal').addClass('hidden').removeClass('flex');
    currentPatientId = null;
    mapInitialized = false;
    if (addrTable) { addrTable.destroy(); addrTable = null; }
    table.ajax.reload(null, false);
}

function loadAddressTable() {
    if (addrTable) { addrTable.destroy(); addrTable = null; }
    addrTable = $('#addressTable').DataTable({
        processing: true,
        ajax: { url: `/admin/patients/${currentPatientId}/addresses`, headers: { 'Accept': 'application/json' }, dataSrc: 'data' },
        columns: [
            { data: 'label', render: d => `<span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-medium">${d}</span>` },
            { data: 'address', render: d => `<span class="text-xs text-gray-600">${d}</span>` },
            { data: null, render: row => (row.latitude && row.longitude)
                ? `<a href="https://www.google.com/maps?q=${row.latitude},${row.longitude}" target="_blank" class="text-xs text-blue-500 hover:underline">📍 ${parseFloat(row.latitude).toFixed(4)}, ${parseFloat(row.longitude).toFixed(4)}</a>`
                : '<span class="text-xs text-gray-400">Belum ada</span>' },
            { data: 'is_primary', render: d => d ? '<span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700 font-medium">⭐ Utama</span>' : '' },
            { data: null, orderable: false, searchable: false,
              render: row => `
                <button onclick="editAddress(${row.id})" class="text-blue-600 text-xs font-medium mr-2">Edit</button>
                <button onclick="deleteAddress(${row.id})" class="text-red-500 text-xs font-medium">Hapus</button>
              ` }
        ],
        language: { search:'Cari:', info:'_START_-_END_ dari _TOTAL_', infoEmpty:'Belum ada alamat', zeroRecords:'Tidak ditemukan', paginate:{previous:'‹',next:'›'} },
        paging: false, order: [[3, 'desc']],
    });
}

function openAddressForm() {
    resetAddressForm();
    $('#addressFormTitle').text('Tambah Alamat');
    $('#addressId').val('');
    $('#addressForm').removeClass('hidden');
    setTimeout(() => {
        if (mapInitialized && map) google.maps.event.trigger(map, 'resize');
    }, 100);
}

function editAddress(id) {
    $.get(`/admin/patients/${currentPatientId}/addresses/${id}`, function (a) {
        $('#addressFormTitle').text('Edit Alamat');
        $('#addressId').val(a.id);
        $('#addr_label').val(a.label);
        $('#addr_address').val(a.address).removeData('auto');
        $('#addr_latitude').val(a.latitude ?? '');
        $('#addr_longitude').val(a.longitude ?? '');
        $('#is_primary').prop('checked', a.is_primary);
        $('#addressForm').removeClass('hidden');
        setTimeout(() => {
            if (mapInitialized && map) {
                google.maps.event.trigger(map, 'resize');
                moveMapToExistingCoords();
            }
        }, 150);
    });
}

function cancelAddressForm() {
    $('#addressForm').addClass('hidden');
    resetAddressForm();
}

function submitAddress() {
    clearAddressErrors();
    const id  = $('#addressId').val();
    const url = id
        ? `/admin/patients/${currentPatientId}/addresses/${id}`
        : `/admin/patients/${currentPatientId}/addresses`;
    const data = {
        _method:    id ? 'PATCH' : 'POST',
        label:      $('#addr_label').val(),
        address:    $('#addr_address').val(),
        latitude:   $('#addr_latitude').val(),
        longitude:  $('#addr_longitude').val(),
        is_primary: $('#is_primary').is(':checked') ? 1 : 0,
    };
    $('#addressSubmitBtn').text('Menyimpan...').prop('disabled', true);
    $.ajax({ url, method: 'POST', data,
        success: res => { cancelAddressForm(); loadAddressTable(); toastr.success(res.message, 'Berhasil!'); },
        error: xhr => {
            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, (f, m) => $(`#err_${f}`).text(m[0]).removeClass('hidden'));
                toastr.error('Periksa kembali form.', 'Validasi Gagal');
            } else toastr.error('Terjadi kesalahan.', 'Error');
        },
        complete: () => $('#addressSubmitBtn').text('Simpan Alamat').prop('disabled', false),
    });
}

function deleteAddress(id) {
    if (!confirm('Hapus alamat ini?')) return;
    $.ajax({ url: `/admin/patients/${currentPatientId}/addresses/${id}`, method: 'POST', data: { _method: 'DELETE' },
        success: res => { loadAddressTable(); toastr.success(res.message, 'Berhasil!'); },
        error: () => toastr.error('Gagal menghapus alamat.', 'Error'),
    });
}

// ===== HAPUS PASIEN =====
function openDeleteModal(id, name) {
    deleteTargetId = id;
    $('#deleteTargetName').text(`Pasien "${name}" dan semua alamatnya akan dihapus.`);
    $('#deleteModal').removeClass('hidden').addClass('flex');
}
function closeDeleteModal() { deleteTargetId = null; $('#deleteModal').addClass('hidden').removeClass('flex'); }
function confirmDelete() {
    if (!deleteTargetId) return;
    $('#confirmDeleteBtn').text('Menghapus...').prop('disabled', true);
    $.ajax({ url: `/admin/patients/${deleteTargetId}`, method: 'POST', data: { _method: 'DELETE' },
        success: res => { closeDeleteModal(); table.ajax.reload(null, false); toastr.success(res.message, 'Berhasil!'); },
        error: xhr => toastr.error(xhr.responseJSON?.message ?? 'Gagal.', 'Error'),
        complete: () => $('#confirmDeleteBtn').text('Ya, Hapus').prop('disabled', false),
    });
}

// ===== HELPERS =====
function resetForm()         { $('#patientForm')[0].reset(); $('#patientId').val(''); clearErrors(); }
function clearErrors()       { $('[id^="err_"]').addClass('hidden').text(''); }
function resetAddressForm()  { $('#addr_label').val('Rumah'); $('#addr_address').val('').removeData('auto'); $('#addr_latitude').val(''); $('#addr_longitude').val(''); $('#is_primary').prop('checked', false); $('#mapSearchBox').val(''); clearAddressErrors(); }
function clearAddressErrors(){ ['label','address','latitude','longitude'].forEach(f => $(`#err_${f}`).addClass('hidden').text('')); }

// Hentikan Autocomplete saat tekan Enter di search box
$('#mapSearchBox').on('keydown', function(e) { if (e.key === 'Enter') e.preventDefault(); });

$(document).keydown(e => { if (e.key === 'Escape') { closeModal(); closeAddressModal(); closeDeleteModal(); } });
</script>
@endpush
