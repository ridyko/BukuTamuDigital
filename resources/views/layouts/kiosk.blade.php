@php
    $gSettings = \App\Models\Setting::getAll();
    $appName   = $gSettings['app_name'] ?? 'Buku Tamu Digital';
    $appOrg    = $gSettings['app_org'] ?? 'SMKN 2 Jakarta';
    $appLogo   = isset($gSettings['app_logo']) ? asset('storage/'.$gSettings['app_logo']) : null;
    $appFav    = isset($gSettings['app_favicon']) ? asset('storage/'.$gSettings['app_favicon']) : null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kiosk') — {{ $appName }} {{ $appOrg }}</title>
    @if($appFav)
        <link rel="icon" type="image/png" href="{{ $appFav }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg:       #070d1a;
            --bg2:      #0d1629;
            --surface:  rgba(255,255,255,0.04);
            --border:   rgba(255,255,255,0.08);
            --accent:   #3b82f6;
            --accent2:  #6366f1;
            --emerald:  #10b981;
            --rose:     #f43f5e;
            --amber:    #f59e0b;
            --text:     #f1f5f9;
            --muted:    #94a3b8;
            --dim:      #475569;
            --r:        16px;
            --r-lg:     24px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%; font-family: 'Inter', sans-serif;
            background: var(--bg); color: var(--text);
            overflow: hidden; user-select: none;
        }
        a { color: inherit; text-decoration: none; }

        /* ── Animated background ─────────── */
        .kiosk-bg {
            position: fixed; inset: 0; z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(59,130,246,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 90%, rgba(99,102,241,0.06) 0%, transparent 60%),
                var(--bg);
        }
        .kiosk-bg::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.025) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── Main wrapper ────────────────── */
        .kiosk-wrap {
            position: relative; z-index: 1;
            width: 100vw; height: 100vh;
            display: flex; flex-direction: column;
            overflow: hidden;
        }

        /* ── Header bar ──────────────────── */
        .kiosk-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 32px;
            border-bottom: 1px solid var(--border);
            background: rgba(7,13,26,0.8);
            backdrop-filter: blur(12px);
            flex-shrink: 0;
        }
        .kiosk-header-logo {
            display: flex; align-items: center; gap: 12px;
        }
        .kiosk-header-logo .logo-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .kiosk-header-logo .logo-text h1 { font-size: 16px; font-weight: 700; }
        .kiosk-header-logo .logo-text p  { font-size: 12px; color: var(--muted); }
        .kiosk-header-right { display: flex; align-items: center; gap: 16px; }
        .kiosk-clock {
            text-align: right;
            font-size: 22px; font-weight: 800;
            color: var(--text); font-variant-numeric: tabular-nums;
        }
        .kiosk-date { font-size: 12px; color: var(--muted); text-align: right; margin-top: 2px; }
        .kiosk-back {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 16px; border-radius: var(--r);
            background: var(--surface); border: 1px solid var(--border);
            font-size: 13px; color: var(--muted);
            cursor: pointer; transition: all .2s;
        }
        .kiosk-back:hover { background: rgba(255,255,255,0.08); color: var(--text); }

        /* ── Content area ────────────────── */
        .kiosk-content {
            flex: 1; overflow-y: auto; overflow-x: hidden;
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 24px;
        }
        .kiosk-content::-webkit-scrollbar { width: 4px; }
        .kiosk-content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

        /* ── Card ────────────────────────── */
        .kiosk-card {
            background: rgba(13,22,41,0.9);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            backdrop-filter: blur(20px);
            box-shadow: 0 24px 80px rgba(0,0,0,0.5);
            width: 100%; max-width: 720px;
            overflow: hidden;
        }

        /* ── Form elements ───────────────── */
        .k-form-group { margin-bottom: 18px; }
        .k-label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--muted); margin-bottom: 7px;
        }
        .k-label .req { color: var(--rose); margin-left: 3px; }
        .k-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 13px 16px;
            color: var(--text); font-size: 15px;
            font-family: 'Inter', sans-serif;
            outline: none; transition: all .2s;
        }
        .k-input:focus {
            border-color: var(--accent);
            background: rgba(59,130,246,0.06);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .k-input::placeholder { color: var(--dim); }
        select.k-input option { background: #0d1629; }
        .k-input-error { border-color: var(--rose) !important; }
        .k-error { font-size: 12px; color: #fb7185; margin-top: 5px; }

        /* ── Buttons ─────────────────────── */
        .k-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 14px 28px; border-radius: 14px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            transition: all .2s; border: none; font-family: 'Inter', sans-serif;
            white-space: nowrap;
        }
        .k-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: white; box-shadow: 0 4px 20px rgba(59,130,246,0.3);
        }
        .k-btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 8px 28px rgba(59,130,246,0.4); }
        .k-btn-primary:active { transform: translateY(0); }
        .k-btn-success {
            background: linear-gradient(135deg, var(--emerald), #06b6d4);
            color: white; box-shadow: 0 4px 20px rgba(16,185,129,0.3);
        }
        .k-btn-success:hover { opacity: .9; transform: translateY(-1px); }
        .k-btn-outline {
            background: var(--surface); color: var(--muted);
            border: 1.5px solid var(--border);
        }
        .k-btn-outline:hover { background: rgba(255,255,255,0.08); color: var(--text); }
        .k-btn-lg { padding: 18px 36px; font-size: 17px; border-radius: 18px; }
        .k-btn-full { width: 100%; }

        /* ── Progress Steps ──────────────── */
        .k-steps {
            display: flex; align-items: center; justify-content: center;
            padding: 24px 32px 20px; gap: 0;
        }
        .k-step {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; font-weight: 600; color: var(--dim);
        }
        .k-step.active  { color: var(--accent); }
        .k-step.done    { color: var(--emerald); }
        .k-step-num {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; flex-shrink: 0;
            border: 2px solid currentColor; background: transparent;
            transition: all .3s;
        }
        .k-step.active .k-step-num  { background: var(--accent); color: white; border-color: var(--accent); }
        .k-step.done .k-step-num    { background: var(--emerald); color: white; border-color: var(--emerald); }
        .k-step-line {
            flex: 1; height: 2px; background: var(--border);
            margin: 0 8px; min-width: 40px; transition: background .3s;
        }
        .k-step-line.done { background: var(--emerald); }

        /* ── Scrollbar ───────────────────── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

        /* ── Animations ──────────────────── */
        @keyframes fadeSlide { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .animate-in { animation: fadeSlide .4s ease forwards; }

        @keyframes scaleIn { from { opacity:0; transform:scale(.8); } to { opacity:1; transform:scale(1); } }
        .scale-in { animation: scaleIn .5s cubic-bezier(.34,1.56,.64,1) forwards; }

        @keyframes pulse-ring {
            0%   { transform: scale(1);   opacity: .8; }
            100% { transform: scale(1.4); opacity: 0;  }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="kiosk-bg"></div>
<div class="kiosk-wrap">
    <header class="kiosk-header">
        <div class="kiosk-header-logo">
            @if($appLogo)
                <img src="{{ $appLogo }}" style="height: 40px; object-fit: contain">
            @else
                <div class="logo-icon">🏫</div>
            @endif
            <div class="logo-text">
                <h1>{{ $appName }}</h1>
                <p>{{ $appOrg }}</p>
            </div>
        </div>
        <div class="kiosk-header-right">
            @if(!request()->routeIs('kiosk.welcome'))
            <a href="{{ route('kiosk.welcome') }}" class="kiosk-back">
                <i class="fas fa-house"></i> Beranda
            </a>
            @endif
            <div>
                <div class="kiosk-clock" id="kiosk-clock">--:--</div>
                <div class="kiosk-date" id="kiosk-date">--</div>
            </div>
        </div>
    </header>
    <div class="kiosk-content">
        @yield('content')
    </div>
</div>

<script>
const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function tickClock() {
    const n = new Date();
    const H = String(n.getHours()).padStart(2,'0');
    const M = String(n.getMinutes()).padStart(2,'0');
    const S = String(n.getSeconds()).padStart(2,'0');
    document.getElementById('kiosk-clock').textContent = `${H}:${M}:${S}`;
    document.getElementById('kiosk-date').textContent =
        `${days[n.getDay()]}, ${n.getDate()} ${months[n.getMonth()]} ${n.getFullYear()}`;
}
setInterval(tickClock, 1000); tickClock();
</script>
@stack('scripts')
</body>
</html>
