@extends('layouts.kiosk')
@section('title', 'Selamat Datang')

@push('styles')
<style>
.welcome-hero {
    text-align: center;
    padding: 20px 24px 40px;
    max-width: 800px;
    width: 100%;
}
.welcome-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25);
    color: #34d399; padding: 8px 18px; border-radius: 40px;
    font-size: 13px; font-weight: 600; margin-bottom: 28px;
}
.welcome-badge .dot { width:8px;height:8px;border-radius:50%;background:#10b981;position:relative; }
.welcome-badge .dot::after {
    content:''; position:absolute; inset: -3px; border-radius:50%;
    background: rgba(16,185,129,0.4);
    animation: pulse-ring 1.5s ease-out infinite;
}
.welcome-title {
    font-size: 52px; font-weight: 900; line-height: 1.1;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #f1f5f9 0%, #94a3b8 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.welcome-title span {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.welcome-sub { font-size: 17px; color: #94a3b8; margin-bottom: 50px; }

.welcome-btns {
    display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    max-width: 640px; margin: 0 auto 40px;
}
.welcome-btn-card {
    background: rgba(255,255,255,0.03);
    border: 1.5px solid rgba(255,255,255,0.08);
    border-radius: 20px; padding: 32px 24px;
    cursor: pointer; transition: all .25s;
    display: flex; flex-direction: column; align-items: center; gap: 14px;
    position: relative; overflow: hidden;
}
.welcome-btn-card::before {
    content:''; position:absolute; inset:0;
    background: linear-gradient(135deg, transparent 50%, rgba(255,255,255,0.015));
}
.welcome-btn-card:hover { transform: translateY(-4px); }
.welcome-btn-card.checkin {
    border-color: rgba(59,130,246,0.3);
}
.welcome-btn-card.checkin:hover {
    background: rgba(59,130,246,0.06);
    border-color: rgba(59,130,246,0.5);
    box-shadow: 0 12px 40px rgba(59,130,246,0.15);
}
.welcome-btn-card.checkout {
    border-color: rgba(16,185,129,0.25);
}
.welcome-btn-card.checkout:hover {
    background: rgba(16,185,129,0.05);
    border-color: rgba(16,185,129,0.4);
    box-shadow: 0 12px 40px rgba(16,185,129,0.12);
}
.btn-card-icon {
    width: 72px; height: 72px; border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px;
}
.checkin .btn-card-icon { background: rgba(59,130,246,0.12); }
.checkout .btn-card-icon { background: rgba(16,185,129,0.1); }
.btn-card-title { font-size: 20px; font-weight: 800; }
.checkin .btn-card-title { color: #60a5fa; }
.checkout .btn-card-title { color: #34d399; }
.btn-card-sub { font-size: 13px; color: #64748b; text-align: center; line-height: 1.5; }
.btn-card-arrow {
    margin-top: 6px; width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
}
.checkin .btn-card-arrow { background: rgba(59,130,246,0.12); color: #60a5fa; }
.checkout .btn-card-arrow { background: rgba(16,185,129,0.1); color: #34d399; }

.welcome-stats {
    display: flex; gap: 24px; justify-content: center;
    font-size: 13px; color: #64748b;
}
.welcome-stat { display: flex; align-items: center; gap: 6px; }
.welcome-stat strong { color: #f1f5f9; font-weight: 700; font-size: 15px; }
</style>
@endpush

@section('content')
<div class="welcome-hero animate-in">
    <div class="welcome-badge">
        <div class="dot"></div>
        Sistem Aktif
    </div>

    <h1 class="welcome-title">
        Selamat Datang di<br><span>{{ $gSettings['app_org'] ?? 'SMKN 2 Jakarta' }}</span>
    </h1>
    <p class="welcome-sub">Silakan pilih layanan yang Anda butuhkan</p>

    <div class="welcome-btns">
        <a href="{{ route('kiosk.checkin') }}" class="welcome-btn-card checkin">
            <div class="btn-card-icon">🚪</div>
            <div class="btn-card-title">Saya Datang</div>
            <div class="btn-card-sub">Daftarkan kunjungan Anda dan dapatkan kode tamu</div>
            <div class="btn-card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>
        <a href="{{ route('kiosk.checkout') }}" class="welcome-btn-card checkout">
            <div class="btn-card-icon">👋</div>
            <div class="btn-card-title">Saya Pulang</div>
            <div class="btn-card-sub">Masukkan kode kunjungan untuk check-out</div>
            <div class="btn-card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>
    </div>

    <div class="welcome-stats">
        <div class="welcome-stat">
            <i class="fas fa-circle" style="color:#10b981;font-size:8px"></i>
            <strong>{{ $activeCount }}</strong> tamu masih di dalam
        </div>
        <div class="welcome-stat" style="color:#1e3a5f">•</div>
        <div class="welcome-stat">
            <i class="fas fa-users" style="color:#3b82f6"></i>
            <strong>{{ $todayCount }}</strong> kunjungan hari ini
        </div>
    </div>
</div>
@endsection
