@extends('layouts.kiosk')
@section('title', 'Checkout Berhasil')

@push('styles')
<style>
.done-wrap { text-align: center; max-width: 480px; width: 100%; }
.done-icon {
    width: 100px; height: 100px; border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #3b82f6);
    display: flex; align-items: center; justify-content: center;
    font-size: 50px; margin: 0 auto 24px;
    box-shadow: 0 0 40px rgba(16,185,129,0.3);
    animation: success-pulse 2s ease-out;
}
@keyframes success-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
    50%  { box-shadow: 0 0 0 40px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
}
.done-title { font-size: 32px; font-weight: 900; margin-bottom: 8px; }
.done-sub { font-size: 15px; color: #94a3b8; margin-bottom: 32px; }
.done-card {
    background: rgba(13,22,41,0.9);
    border: 1px solid rgba(16,185,129,0.2);
    border-radius: 20px; padding: 28px 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.done-stats {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: 12px; margin-bottom: 20px;
}
.done-stat-item {
    padding: 14px 12px; border-radius: 12px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
}
.done-stat-label { font-size: 11px; color: #4b6074; text-transform: uppercase; letter-spacing: .5px; }
.done-stat-value { font-size: 16px; font-weight: 700; color: #f1f5f9; margin-top: 4px; }
.done-stat-value.green { color: #10b981; }

.countdown-bar { width:100%; height:4px; background:rgba(255,255,255,0.06); border-radius:10px; overflow:hidden; margin-bottom:8px; }
.countdown-fill { height:100%; background:linear-gradient(90deg,#10b981,#3b82f6); border-radius:10px; transition: width 1s linear; }
.countdown-text { font-size:13px; color:#64748b; text-align:center; margin-bottom:16px; }
</style>
@endpush

@section('content')
<div class="done-wrap scale-in">
    <div class="done-icon">🎉</div>
    <h1 class="done-title">Terima Kasih!</h1>
    <p class="done-sub">Check-out berhasil. Sampai jumpa lagi, <strong style="color:#f1f5f9">{{ $visitor->name }}</strong>!</p>

    <div class="done-card">
        <div class="done-stats">
            <div class="done-stat-item">
                <div class="done-stat-label">Check-In</div>
                <div class="done-stat-value">{{ $visitor->check_in_at?->format('H:i') }}</div>
            </div>
            <div class="done-stat-item">
                <div class="done-stat-label">Check-Out</div>
                <div class="done-stat-value green">{{ $visitor->check_out_at?->format('H:i') }}</div>
            </div>
            <div class="done-stat-item">
                <div class="done-stat-label">Durasi</div>
                @php
                    $h = intdiv($visitor->duration ?? 0, 60);
                    $m = ($visitor->duration ?? 0) % 60;
                @endphp
                <div class="done-stat-value">{{ $h > 0 ? $h.'j ' : '' }}{{ $m }}m</div>
            </div>
        </div>

        <div style="padding:14px;background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.15);border-radius:10px;margin-bottom:20px;font-size:13px;color:#94a3b8;text-align:left">
            <i class="fas fa-circle-check" style="color:#10b981;margin-right:6px"></i>
            Kunjungan Anda telah dicatat dengan kode <strong style="color:#f1f5f9;font-family:monospace">{{ $visitor->visit_code }}</strong>
        </div>

        <div class="countdown-bar"><div class="countdown-fill" id="fill" style="width:100%"></div></div>
        <div class="countdown-text">Kembali ke beranda dalam <span id="num" style="color:#f1f5f9;font-weight:700">15</span> detik</div>

        <a href="{{ route('kiosk.welcome') }}" class="k-btn k-btn-outline k-btn-full" style="justify-content:center">
            <i class="fas fa-house"></i> Kembali ke Beranda
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
let s = 15;
const fill = document.getElementById('fill'), num = document.getElementById('num');
setInterval(() => {
    s--;
    num.textContent = s;
    fill.style.width = (s/15*100)+'%';
    if(s<=0) window.location = '{{ route("kiosk.welcome") }}';
}, 1000);
</script>
@endpush
