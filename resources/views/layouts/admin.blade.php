<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediTrack - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        * { font-family: 'Google Sans', 'Roboto', sans-serif; }
        body { background: #f8f9fa; color: #202124; }
        .topbar { background:#fff; border-bottom:1px solid #e0e0e0; height:64px; display:flex; align-items:center; padding:0 16px; position:fixed; top:0; left:0; right:0; z-index:100; gap:12px; }
        .topbar-logo { display:flex; align-items:center; gap:10px; text-decoration:none; min-width:auto; }
        .topbar-hamburger { display:none; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; border:none; background:none; cursor:pointer; color:#5f6368; flex-shrink:0; }
        .topbar-hamburger:hover { background:#f1f3f4; }
        .topbar-logo .logo-name { font-size:18px; font-weight:500; color:#202124; }
        .topbar-logo .logo-name span { color:#1a73e8; }
        .topbar-divider { width:1px; height:28px; background:#e0e0e0; margin:0 4px; }
        .topbar-breadcrumb { font-size:14px; color:#5f6368; display:flex; align-items:center; gap:4px; }
        .topbar-breadcrumb .page-title { color:#202124; font-weight:500; }
        .topbar-right { margin-left:auto; display:flex; align-items:center; gap:8px; }
        .user-avatar { width:36px; height:36px; border-radius:50%; background:#1a73e8; color:white; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:500; cursor:pointer; }
        .sidebar { background:#fff; border-right:1px solid #e0e0e0; width:256px; position:fixed; top:64px; left:0; bottom:0; display:flex; flex-direction:column; overflow-y:auto; z-index:90; transition:transform .25s ease; }
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:89; }
        .sidebar-overlay.active { display:block; }
        .sidebar-section-title { font-size:11px; font-weight:500; color:#80868b; letter-spacing:0.8px; text-transform:uppercase; padding:16px 16px 4px 16px; }
        .nav-item { display:flex; align-items:center; gap:12px; padding:0 12px; height:40px; border-radius:0 20px 20px 0; margin:2px 8px 2px 0; font-size:14px; font-weight:400; color:#3c4043; text-decoration:none; cursor:pointer; transition:background 0.15s; }
        .nav-item:hover { background:#f1f3f4; }
        .nav-item.active { background:#e8f0fe; color:#1a73e8; font-weight:500; }
        .nav-item .material-icons { font-size:20px; color:#5f6368; }
        .nav-item.active .material-icons { color:#1a73e8; }
        .sidebar-footer { margin-top:auto; padding:12px; border-top:1px solid #e0e0e0; }
        .sidebar-footer .user-name { font-size:13px; font-weight:500; color:#202124; }
        .sidebar-footer .user-role { font-size:11px; color:#80868b; }
        .logout-btn { display:flex; align-items:center; gap:10px; width:100%; padding:8px 12px; border-radius:8px; font-size:13px; color:#d93025; background:none; border:none; cursor:pointer; transition:background 0.15s; margin-top:4px; }
        .logout-btn:hover { background:#fce8e6; }
        .logout-btn .material-icons { font-size:18px; }
        .main-content { margin-left:256px; margin-top:64px; min-height:calc(100vh - 64px); padding:24px; }
        @media (max-width: 768px) {
            .topbar-hamburger { display:flex; }
            .topbar-logo img { height:28px; }
            .topbar-divider { display:none; }
            .topbar-breadcrumb { font-size:13px; }
            .sidebar { transform:translateX(-100%); top:64px; }
            .sidebar.open { transform:translateX(0); }
            .main-content { margin-left:0; padding:16px; }
        }
        .card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px 24px; }
        .btn-primary { background:#1a73e8; color:#fff; border:none; border-radius:4px; padding:8px 16px; font-size:14px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background 0.15s; font-family:'Google Sans',sans-serif; }
        .btn-primary:hover { background:#1557b0; }
        .btn-secondary { background:#fff; color:#1a73e8; border:1px solid #dadce0; border-radius:4px; padding:8px 16px; font-size:14px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background 0.15s; font-family:'Google Sans',sans-serif; }
        .btn-secondary:hover { background:#f8f9fa; }
        .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:100px; font-size:12px; font-weight:500; }
        .badge-green  { background:#e6f4ea; color:#137333; }
        .badge-red    { background:#fce8e6; color:#c5221f; }
        .badge-blue   { background:#e8f0fe; color:#1a73e8; }
        .badge-purple { background:#f3e8fd; color:#7627bb; }
        .badge-orange { background:#fef3e2; color:#b06000; }
        .badge-gray   { background:#f1f3f4; color:#5f6368; }
        a.table-link { color:#1a73e8; text-decoration:none; font-size:13px; font-weight:500; }
        a.table-link:hover { text-decoration:underline; }
        .table-action { background:none; border:none; font-size:13px; font-weight:500; cursor:pointer; padding:2px 6px; border-radius:4px; transition:background 0.15s; }
        .table-action.edit   { color:#1a73e8; }
        .table-action.delete { color:#c5221f; }
        .table-action.green  { color:#137333; }
        .table-action:hover  { background:#f1f3f4; }
        .modal-overlay { background:rgba(0,0,0,0.4); }
        .modal-card { background:#fff; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.15); }
        .modal-header { padding:20px 24px 16px; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between; }
        .modal-header h3 { font-size:16px; font-weight:500; color:#202124; }
        .modal-close { color:#5f6368; background:none; border:none; cursor:pointer; border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:20px; transition:background 0.15s; }
        .modal-close:hover { background:#f1f3f4; }
        .modal-body { padding:20px 24px; }
        .modal-footer { padding:12px 24px 20px; display:flex; justify-content:flex-end; gap:8px; }
        .form-input { width:100%; border:1px solid #dadce0; border-radius:4px; padding:8px 12px; font-size:14px; font-family:'Google Sans',sans-serif; color:#202124; outline:none; transition:border 0.15s, box-shadow 0.15s; box-sizing:border-box; }
        .form-input:focus { border-color:#1a73e8; box-shadow:0 0 0 2px rgba(26,115,232,0.15); }
        .form-label { font-size:13px; font-weight:500; color:#3c4043; margin-bottom:4px; display:block; }
        .form-error { font-size:12px; color:#c5221f; margin-top:4px; }
        table.dataTable { border-collapse:collapse !important; }
        table.dataTable thead th { background:#f8f9fa; color:#5f6368; font-size:12px; font-weight:500; text-transform:uppercase; letter-spacing:0.5px; padding:12px 16px; border-bottom:1px solid #e0e0e0 !important; border-top:none !important; }
        table.dataTable tbody td { padding:12px 16px; font-size:14px; color:#202124; border-bottom:1px solid #f1f3f4 !important; vertical-align:middle; }
        table.dataTable tbody tr:hover td { background:#f8f9fa; }
        table.dataTable.no-footer { border-bottom:1px solid #e0e0e0 !important; }
        .dataTables_wrapper .dataTables_filter input { border:1px solid #dadce0; border-radius:4px; padding:6px 12px; font-size:14px; margin-left:8px; outline:none; font-family:'Google Sans',sans-serif; }
        .dataTables_wrapper .dataTables_filter input:focus { border-color:#1a73e8; box-shadow:0 0 0 2px rgba(26,115,232,0.15); }
        .dataTables_wrapper .dataTables_length select { border:1px solid #dadce0; border-radius:4px; padding:6px 8px; font-size:14px; margin:0 6px; font-family:'Google Sans',sans-serif; }
        .dataTables_wrapper .dataTables_info { font-size:13px; color:#5f6368; margin-top:12px; }
        .dataTables_wrapper .dataTables_paginate { margin-top:12px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding:4px 10px; border-radius:4px; margin:0 1px; cursor:pointer; font-size:13px; border:none !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background:#e8f0fe !important; color:#1a73e8 !important; border:none; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) { background:#f1f3f4 !important; color:#202124 !important; border:none; }
        #toast-container > div { border-radius:4px !important; box-shadow:0 2px 8px rgba(0,0,0,0.15) !important; font-family:'Google Sans',sans-serif !important; font-size:14px !important; }
        .toast-success { background-color:#137333 !important; }
        .toast-error   { background-color:#c5221f !important; }
    </style>
    @stack('styles')
</head>
<body>

<header class="topbar">
    <button class="topbar-hamburger" id="sidebarToggle" aria-label="Menu">
        <span class="material-icons">menu</span>
    </button>
    <a href="{{ auth()->user()->isSuperAdmin() ? route('admin.dashboard') : route('farmasi.dashboard') }}" class="topbar-logo">
        
        <img src="{{ asset('logo.png') }}" alt="MediTrack" style="height:36px; width:auto; object-fit:contain;">
    </a>
    <div class="topbar-divider"></div>
    <div class="topbar-breadcrumb">
        <span class="material-icons" style="font-size:16px;">chevron_right</span>
        <span class="page-title">@yield('title', 'Dashboard')</span>
    </div>
    <div class="topbar-right">
        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
    </div>
</header>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <nav style="padding:8px 0; flex:1;">
        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="material-icons">dashboard</span> Dashboard
            </a>

            <div class="sidebar-section-title">Operasional</div>
            <a href="{{ route('admin.prescriptions.index') }}" class="nav-item {{ request()->routeIs('admin.prescriptions*') ? 'active' : '' }}">
                <span class="material-icons">receipt_long</span> Catatan Resep
            </a>

            <div class="sidebar-section-title">Master Data</div>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <span class="material-icons">manage_accounts</span> Manajemen User
            </a>
            <a href="{{ route('admin.couriers.index') }}" class="nav-item {{ request()->routeIs('admin.couriers*') ? 'active' : '' }}">
                <span class="material-icons">delivery_dining</span> Data Kurir
            </a>
            <a href="{{ route('admin.patients.index') }}" class="nav-item {{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
                <span class="material-icons">personal_injury</span> Data Pasien
            </a>
        @else
            <a href="{{ route('farmasi.dashboard') }}" class="nav-item {{ request()->routeIs('farmasi.dashboard') ? 'active' : '' }}">
                <span class="material-icons">dashboard</span> Dashboard
            </a>
            <div class="sidebar-section-title">Operasional</div>
            <a href="{{ route('farmasi.prescriptions.index') }}" class="nav-item {{ request()->routeIs('farmasi.prescriptions*') ? 'active' : '' }}">
                <span class="material-icons">receipt_long</span> Catatan Resep
            </a>

            <div class="sidebar-section-title">Data</div>
            <a href="{{ route('farmasi.patients.index') }}" class="nav-item {{ request()->routeIs('farmasi.patients*') ? 'active' : '' }}">
                <span class="material-icons">personal_injury</span> Data Pasien
            </a>
            <a href="{{ route('farmasi.couriers.index') }}" class="nav-item {{ request()->routeIs('farmasi.couriers*') ? 'active' : '' }}">
                <span class="material-icons">delivery_dining</span> Data Kurir
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div style="display:flex; align-items:center; gap:10px; padding:8px;">
            <div class="user-avatar" style="width:32px;height:32px;font-size:13px;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->getRoleLabelAttribute() }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <span class="material-icons">logout</span> Keluar
            </button>
        </form>
    </div>
</aside>

<main class="main-content">@yield('content')</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    toastr.options = { closeButton:true, progressBar:true, positionClass:'toast-top-right', timeOut:3500 };
</script>
@stack('scripts')
<script>
    (function() {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        function close() { sidebar.classList.remove('open'); overlay.classList.remove('active'); }
        toggle.addEventListener('click', function() {
            const isOpen = sidebar.classList.toggle('open');
            overlay.classList.toggle('active', isOpen);
        });
        overlay.addEventListener('click', close);
    })();
</script>
</body>
</html>
