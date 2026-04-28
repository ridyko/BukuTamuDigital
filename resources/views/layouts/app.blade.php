<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Buku Tamu Digital SMKN 2 Jakarta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --sidebar-w: 260px;
            --bg-dark: #0b1120;
            --bg-card: #111827;
            --bg-sidebar: #0f1629;
            --bg-sidebar-hover: #1a2744;
            --border: #1f2d4a;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #4b6074;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --accent-glow: rgba(59,130,246,0.15);
            --emerald: #10b981;
            --emerald-glow: rgba(16,185,129,0.15);
            --amber: #f59e0b;
            --rose: #f43f5e;
            --violet: #8b5cf6;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 4px 24px rgba(0,0,0,0.35);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text-primary); }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }

        /* ── LAYOUT ─────────────────────────────── */
        .layout { display: flex; min-height: 100vh; }

        /* ── SIDEBAR ────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s ease;
        }
        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-logo .logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--accent), var(--violet));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; margin-bottom: 10px;
        }
        .sidebar-logo .logo-title {
            font-weight: 700; font-size: 14px; color: var(--text-primary);
            line-height: 1.3;
        }
        .sidebar-logo .logo-subtitle {
            font-size: 11px; color: var(--text-secondary); margin-top: 2px;
        }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-label {
            font-size: 10px; font-weight: 600; color: var(--text-muted);
            letter-spacing: 1px; text-transform: uppercase;
            padding: 8px 8px 6px; margin-top: 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: var(--radius-sm);
            color: var(--text-secondary); font-size: 13.5px; font-weight: 500;
            transition: all .2s; cursor: pointer; margin-bottom: 2px;
        }
        .nav-item:hover { background: var(--bg-sidebar-hover); color: var(--text-primary); }
        .nav-item.active {
            background: var(--accent-glow);
            color: var(--accent);
            border: 1px solid rgba(59,130,246,0.2);
        }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; }
        .nav-badge {
            margin-left: auto;
            background: var(--rose);
            color: white;
            font-size: 10px; font-weight: 700;
            padding: 2px 6px; border-radius: 10px;
        }
        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid var(--border);
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: var(--radius-sm);
            background: rgba(255,255,255,0.03);
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--violet));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: white; flex-shrink: 0;
        }
        .user-name { font-size: 12.5px; font-weight: 600; color: var(--text-primary); }
        .user-role { font-size: 11px; color: var(--text-secondary); }
        .btn-logout {
            margin-left: auto;
            width: 30px; height: 30px; border-radius: 8px;
            background: rgba(244,63,94,0.1); border: 1px solid rgba(244,63,94,0.2);
            color: var(--rose); display: flex; align-items: center; justify-content: center;
            font-size: 13px; cursor: pointer; transition: all .2s;
        }
        .btn-logout:hover { background: rgba(244,63,94,0.2); }

        /* ── MAIN CONTENT ────────────────────────── */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── TOPBAR ─────────────────────────────── */
        .topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 16px; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-time {
            font-size: 12px; color: var(--text-secondary);
            background: rgba(255,255,255,0.04); padding: 6px 12px; border-radius: 8px;
        }

        /* ── NOTIFICATION BELL ───────────────────── */
        .notif-wrap { position: relative; }
        .notif-bell {
            width: 36px; height: 36px; border-radius: 8px;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary); font-size: 14px; cursor: pointer;
            transition: all .2s; position: relative;
        }
        .notif-bell:hover { background: rgba(255,255,255,0.09); color: var(--text-primary); }
        .notif-bell.has-unread { color: var(--amber); border-color: rgba(245,158,11,0.3); }
        .notif-count {
            position: absolute; top: -5px; right: -5px;
            background: var(--rose); color: white;
            font-size: 9px; font-weight: 800;
            min-width: 16px; height: 16px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3px;
            border: 1.5px solid var(--bg-card);
        }
        /* Dropdown */
        .notif-dropdown {
            position: absolute; top: calc(100% + 8px); right: 0;
            width: 340px;
            background: #111827;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            overflow: hidden;
            display: none; z-index: 200;
            animation: fadeIn .2s ease;
        }
        .notif-dropdown.open { display: block; }
        .notif-dd-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .notif-dd-title { font-size: 13px; font-weight: 700; }
        .notif-dd-mark { font-size: 11px; color: var(--accent); cursor: pointer; border: none; background: none; font-family: inherit; }
        .notif-dd-mark:hover { text-decoration: underline; }
        .notif-item {
            display: flex; gap: 10px; padding: 12px 16px;
            border-bottom: 1px solid rgba(31,45,74,0.4);
            transition: background .15s;
        }
        .notif-item:hover { background: rgba(255,255,255,0.03); }
        .notif-item.unread { background: rgba(59,130,246,0.04); }
        .notif-item-icon {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            background: rgba(59,130,246,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .notif-item-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .notif-item-sub  { font-size: 11.5px; color: var(--text-secondary); margin-top: 2px; }
        .notif-item-time { font-size: 10.5px; color: var(--text-muted); margin-top: 4px; }
        .notif-dd-empty  { padding: 28px 16px; text-align: center; color: var(--text-muted); font-size: 13px; }
        .notif-dd-footer {
            padding: 10px 16px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .notif-dd-footer a { font-size: 12.5px; color: var(--accent); }
        .notif-dd-footer a:hover { text-decoration: underline; }

        .topbar-badge {
            width: 36px; height: 36px; border-radius: 8px;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary); font-size: 14px; cursor: pointer;
            transition: all .2s; position: relative;
        }
        .topbar-badge:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }
        .badge-dot {
            position: absolute; top: 6px; right: 6px;
            width: 7px; height: 7px; border-radius: 50%; background: var(--emerald);
            border: 1.5px solid var(--bg-card);
        }

        /* ── PAGE CONTENT ────────────────────────── */
        .page-content { padding: 28px; flex: 1; }

        /* ── ALERTS ─────────────────────────────── */
        .alert {
            padding: 12px 16px; border-radius: var(--radius-sm);
            margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
            font-size: 13.5px;
        }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #34d399; }
        .alert-error   { background: rgba(244,63,94,0.1);  border: 1px solid rgba(244,63,94,0.2);  color: #fb7185; }
        .alert-warning { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); color: #fbbf24; }

        /* ── CARDS ──────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 14px; font-weight: 600; }
        .card-body { padding: 20px; }

        /* ── STAT CARDS ─────────────────────────── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            position: relative; overflow: hidden;
            transition: transform .2s, border-color .2s;
        }
        .stat-card:hover { transform: translateY(-2px); border-color: rgba(255,255,255,0.1); }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
        }
        .stat-card.blue::before   { background: linear-gradient(90deg, var(--accent), var(--violet)); }
        .stat-card.green::before  { background: linear-gradient(90deg, var(--emerald), #06b6d4); }
        .stat-card.amber::before  { background: linear-gradient(90deg, var(--amber), #f97316); }
        .stat-card.rose::before   { background: linear-gradient(90deg, var(--rose), var(--violet)); }
        .stat-icon {
            width: 42px; height: 42px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; margin-bottom: 14px;
        }
        .stat-icon.blue   { background: var(--accent-glow); color: var(--accent); }
        .stat-icon.green  { background: var(--emerald-glow); color: var(--emerald); }
        .stat-icon.amber  { background: rgba(245,158,11,0.12); color: var(--amber); }
        .stat-icon.rose   { background: rgba(244,63,94,0.12); color: var(--rose); }
        .stat-value { font-size: 28px; font-weight: 800; line-height: 1; margin-bottom: 4px; }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }

        /* ── TABLE ──────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th {
            padding: 11px 16px;
            text-align: left; font-size: 11px; font-weight: 600;
            color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        tbody tr {
            border-bottom: 1px solid rgba(31,45,74,0.5);
            transition: background .15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }
        tbody td { padding: 12px 16px; color: var(--text-secondary); vertical-align: middle; }
        tbody td:first-child { color: var(--text-primary); }

        /* ── BADGES / STATUS ─────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600; white-space: nowrap;
        }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .badge-active  { background: rgba(16,185,129,0.12); color: var(--emerald); border: 1px solid rgba(16,185,129,0.2); }
        .badge-active::before { background: var(--emerald); }
        .badge-out     { background: rgba(148,163,184,0.08); color: var(--text-muted); border: 1px solid var(--border); }
        .badge-out::before { background: var(--text-muted); }
        .badge-auto    { background: rgba(245,158,11,0.1); color: var(--amber); border: 1px solid rgba(245,158,11,0.2); }
        .badge-role-superadmin { background: rgba(139,92,246,0.12); color: var(--violet); border: 1px solid rgba(139,92,246,0.2); }
        .badge-role-receptionist { background: rgba(59,130,246,0.12); color: var(--accent); border: 1px solid rgba(59,130,246,0.2); }
        .badge-role-staff { background: rgba(16,185,129,0.1); color: var(--emerald); border: 1px solid rgba(16,185,129,0.2); }

        /* ── BUTTONS ────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: all .2s; border: 1px solid transparent;
            white-space: nowrap;
        }
        .btn-primary {
            background: var(--accent); color: white;
            border-color: var(--accent);
        }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-success {
            background: var(--emerald); color: white;
            border-color: var(--emerald);
        }
        .btn-success:hover { background: #059669; }
        .btn-danger {
            background: rgba(244,63,94,0.1); color: var(--rose);
            border-color: rgba(244,63,94,0.2);
        }
        .btn-danger:hover { background: rgba(244,63,94,0.2); }
        .btn-outline {
            background: transparent; color: var(--text-secondary);
            border-color: var(--border);
        }
        .btn-outline:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon {
            width: 32px; height: 32px; padding: 0;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: var(--radius-sm);
        }

        /* ── FORMS ──────────────────────────────── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
        .form-label .required { color: var(--rose); margin-left: 2px; }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            color: var(--text-primary);
            font-size: 13.5px; font-family: inherit;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control option { background: var(--bg-card); }
        .form-error { font-size: 12px; color: var(--rose); margin-top: 5px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

        /* ── PAGINATION ─────────────────────────── */
        .pagination-wrap { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border); }
        .pagination-info { font-size: 12.5px; color: var(--text-muted); }
        .pagination { display: flex; gap: 4px; }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: var(--radius-sm);
            font-size: 13px; transition: all .2s;
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }
        .pagination a:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
        .pagination span.active { background: var(--accent); border-color: var(--accent); color: white; font-weight: 600; }
        .pagination span.disabled { opacity: .3; cursor: default; }

        /* ── EMPTY STATE ─────────────────────────── */
        .empty-state {
            text-align: center; padding: 60px 20px;
        }
        .empty-state i { font-size: 48px; color: var(--text-muted); margin-bottom: 16px; display: block; }
        .empty-state p { color: var(--text-secondary); font-size: 14px; }

        /* ── RESPONSIVE ─────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .page-content { padding: 16px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* ── ANIMATIONS ─────────────────────────── */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn .35s ease forwards; }

        /* ── SCROLLBAR ──────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body>
<div class="layout">
    <!-- ── SIDEBAR ────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🏫</div>
            <div class="logo-title">Buku Tamu Digital</div>
            <div class="logo-subtitle">SMKN 2 Jakarta</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isReceptionist())
            <a href="{{ route('visitors.active') }}" class="nav-item {{ request()->routeIs('visitors.active') ? 'active' : '' }}">
                <i class="fas fa-circle-dot" style="{{ \App\Models\Visitor::active()->count() > 0 ? 'color:#10b981' : '' }}"></i> Tamu Aktif
                @php $activeCount = \App\Models\Visitor::active()->count(); @endphp
                @if($activeCount > 0)
                    <span class="nav-badge">{{ $activeCount }}</span>
                @endif
            </a>
            <a href="{{ route('visitors.index') }}" class="nav-item {{ request()->routeIs('visitors.index') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Tamu Hari Ini
            </a>
            <a href="{{ route('visitors.history') }}" class="nav-item {{ request()->routeIs('visitors.history') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Riwayat Kunjungan
            </a>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Laporan & Export
            </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
            <div class="nav-label">Administrasi</div>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-user-gear"></i> Manajemen Pengguna
            </a>
            @endif

            <div class="nav-label">Kiosk</div>
            <a href="{{ url('/kiosk') }}" class="nav-item" target="_blank">
                <i class="fas fa-tablet-screen-button"></i> Buka Kiosk
            </a>

            <div class="nav-label">Akun Saya</div>
            <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="fas fa-bell {{ $unreadCount ?? 0 > 0 ? 'text-amber' : '' }}"></i> Notifikasi
                @php $nbCount = auth()->user()->unreadNotifications()->count(); @endphp
                @if($nbCount > 0)
                    <span class="nav-badge">{{ $nbCount }}</span>
                @endif
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ Str::limit(auth()->user()->name, 20) }}</div>
                    <div class="user-role">{{ auth()->user()->role_label }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" title="Keluar">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ── MAIN ──────────────────────────────── -->
    <div class="main">
        <header class="topbar">
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-right">
                <div class="topbar-time" id="live-clock">--:--</div>

                {{-- ── Notification Bell ────────────── --}}
                <div class="notif-wrap" id="notif-wrap">
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                        $recentNotifs = auth()->user()->unreadNotifications()->latest()->take(5)->get();
                    @endphp
                    <button class="notif-bell {{ $unreadCount > 0 ? 'has-unread' : '' }}"
                            id="notif-bell" title="Notifikasi" onclick="toggleNotifDropdown()">
                        <i class="fas fa-bell"></i>
                        @if($unreadCount > 0)
                            <span class="notif-count" id="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>

                    <div class="notif-dropdown" id="notif-dropdown">
                        <div class="notif-dd-header">
                            <span class="notif-dd-title">
                                <i class="fas fa-bell" style="color:var(--amber);margin-right:6px"></i>
                                Notifikasi
                                @if($unreadCount > 0)
                                    <span style="font-size:11px;color:var(--text-muted);font-weight:400">({{ $unreadCount }} belum terbaca)</span>
                                @endif
                            </span>
                            @if($unreadCount > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="notif-dd-mark">Tandai semua terbaca</button>
                            </form>
                            @endif
                        </div>

                        {{-- Notification items --}}
                        @if($recentNotifs->isEmpty())
                            <div class="notif-dd-empty">
                                <i class="fas fa-bell-slash" style="font-size:28px;margin-bottom:8px;display:block"></i>
                                Tidak ada notifikasi baru
                            </div>
                        @else
                            @foreach($recentNotifs as $notif)
                            <a href="#" onclick="readNotif('{{ $notif->id }}', event)" class="notif-item unread">
                                <div class="notif-item-icon">👤</div>
                                <div style="flex:1;min-width:0">
                                    <div class="notif-item-name">{{ $notif->data['visitor_name'] }}</div>
                                    <div class="notif-item-sub">
                                        ingin bertemu • {{ \Str::limit($notif->data['purpose'], 40) }}
                                    </div>
                                    <div class="notif-item-time">
                                        <i class="fas fa-clock" style="margin-right:3px"></i>
                                        {{ $notif->created_at->diffForHumans() }} · {{ $notif->data['check_in_display'] ?? '-' }} WIB
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        @endif

                        <div class="notif-dd-footer">
                            <a href="{{ route('notifications.index') }}">Lihat semua notifikasi →</a>
                        </div>
                    </div>
                </div>
                {{-- ── End Notification Bell ─────────── --}}

                <div class="topbar-badge" title="Tamu Aktif">
                    <i class="fas fa-user-check"></i>
                    @if(\App\Models\Visitor::active()->count() > 0)
                        <span class="badge-dot"></span>
                    @endif
                </div>
            </div>
        </header>

        <div class="page-content fade-in">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}</div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script>
// Live clock
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const s = String(now.getSeconds()).padStart(2,'0');
    const el = document.getElementById('live-clock');
    if(el) el.textContent = `${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();

// Confirm delete
document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if(!confirm(this.dataset.confirm || 'Yakin ingin menghapus?')) e.preventDefault();
    });
});

/* ── Notification Bell Dropdown ─────────────────────── */
function toggleNotifDropdown() {
    document.getElementById('notif-dropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('notif-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('notif-dropdown')?.classList.remove('open');
    }
});

function readNotif(id, e) {
    e.preventDefault();
    fetch(`/bukutamu/public/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => { window.location.href = '/bukutamu/public/notifications'; });
}

/* ── Poll unread count every 30 seconds ─────────────── */
function pollNotifications() {
    fetch('/bukutamu/public/notifications/count')
        .then(r => r.json())
        .then(data => {
            const badge  = document.getElementById('notif-badge');
            const bell   = document.getElementById('notif-bell');
            if (!bell) return;
            if (data.count > 0) {
                bell.classList.add('has-unread');
                if (!badge) {
                    const span = document.createElement('span');
                    span.className = 'notif-count'; span.id = 'notif-badge';
                    span.textContent = data.count > 9 ? '9+' : data.count;
                    bell.appendChild(span);
                } else {
                    badge.textContent = data.count > 9 ? '9+' : data.count;
                }
            } else {
                bell.classList.remove('has-unread');
                if (badge) badge.remove();
            }
        }).catch(() => {});
}
setInterval(pollNotifications, 30000);
</script>
@stack('scripts')
</body>
</html>
