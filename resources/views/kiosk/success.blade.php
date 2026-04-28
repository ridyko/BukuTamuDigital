@extends('layouts.kiosk')
@section('title', 'Check-In Berhasil!')

@push('styles')
<style>
.success-wrap {
    text-align: center; max-width: 500px; width: 100%;
}
.success-icon-wrap {
    position: relative; display: inline-block; margin-bottom: 24px;
}
.success-icon-circle {
    width: 90px; height: 90px; border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #06b6d4);
    display: flex; align-items: center; justify-content: center;
    font-size: 42px; margin: 0 auto;
    box-shadow: 0 0 0 0 rgba(16,185,129,0.5);
    animation: success-pulse 2s ease-out;
}
@keyframes success-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
    50%  { box-shadow: 0 0 0 30px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
}
.success-title { font-size: 30px; font-weight: 900; margin-bottom: 8px; }
.success-sub { font-size: 15px; color: #94a3b8; margin-bottom: 28px; }

.success-card {
    background: rgba(13,22,41,0.9);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px; padding: 28px;
    display: flex; flex-direction: column; align-items: center; gap: 20px;
    backdrop-filter: blur(20px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.qr-box {
    background: white; border-radius: 16px;
    padding: 16px; display: inline-block;
}
.qr-box img { display: block; width: 180px; height: 180px; }

.visit-code-box {
    background: rgba(59,130,246,0.08);
    border: 1px solid rgba(59,130,246,0.2);
    border-radius: 12px; padding: 14px 24px;
    font-family: 'Courier New', monospace;
    font-size: 22px; font-weight: 800;
    letter-spacing: 3px; color: #60a5fa;
}
.visitor-info-row {
    width: 100%;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px; padding: 16px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    text-align: left;
}
.info-item-label { font-size: 11px; color: #4b6074; text-transform: uppercase; letter-spacing: .5px; }
.info-item-value { font-size: 14px; font-weight: 600; color: #f1f5f9; margin-top: 2px; }

.countdown-bar {
    width: 100%; height: 4px;
    background: rgba(255,255,255,0.06); border-radius: 10px; overflow: hidden;
}
.countdown-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #10b981);
    border-radius: 10px;
    transition: width 1s linear;
}
.countdown-text { font-size: 13px; color: #64748b; text-align: center; }
</style>
@endpush

@section('content')
<div class="success-wrap scale-in">
    <div class="success-icon-wrap">
        <div class="success-icon-circle">✅</div>
    </div>
    <h1 class="success-title">Check-In Berhasil!</h1>
    <p class="success-sub">Selamat datang, <strong style="color:#f1f5f9">{{ $visitor->name }}</strong>! Simpan kode kunjungan Anda.</p>

    <div class="success-card">
        {{-- QR Code --}}
        @if($visitor->qr_code && file_exists(storage_path('app/public/' . $visitor->qr_code)))
        <div>
            <div style="font-size:12px;color:#64748b;margin-bottom:8px;text-align:center">Scan QR untuk Checkout</div>
            <div class="qr-box">
                <img src="{{ asset('storage/' . $visitor->qr_code) }}" alt="QR Code">
            </div>
        </div>
        @endif

        {{-- Visit Code --}}
        <div>
            <div style="font-size:12px;color:#64748b;margin-bottom:8px;text-align:center">Kode Kunjungan Anda</div>
            <div class="visit-code-box">{{ $visitor->visit_code }}</div>
        </div>

        {{-- Visitor Info --}}
        <div class="visitor-info-row">
            <div>
                <div class="info-item-label">Nama</div>
                <div class="info-item-value">{{ $visitor->name }}</div>
            </div>
            <div>
                <div class="info-item-label">Instansi</div>
                <div class="info-item-value">{{ $visitor->institution ?: '-' }}</div>
            </div>
            <div>
                <div class="info-item-label">Keperluan</div>
                <div class="info-item-value">{{ Str::limit($visitor->purpose, 40) }}</div>
            </div>
            <div>
                <div class="info-item-label">Tujuan</div>
                <div class="info-item-value">{{ $visitor->host?->name ?? $visitor->department ?? '-' }}</div>
            </div>
            <div>
                <div class="info-item-label">Check-In</div>
                <div class="info-item-value">{{ $visitor->check_in_at?->format('H:i, d M Y') }}</div>
            </div>
            <div>
                <div class="info-item-label">Status</div>
                <div class="info-item-value" style="color:#10b981">🟢 Aktif</div>
            </div>
        </div>

        {{-- Countdown --}}
        <div style="width:100%">
            <div class="countdown-bar">
                <div class="countdown-fill" id="countdown-bar" style="width:100%"></div>
            </div>
            <div class="countdown-text" style="margin-top:8px">
                Kembali ke halaman utama dalam <span id="countdown-num" style="color:#f1f5f9;font-weight:700">20</span> detik
            </div>
        </div>

        <a href="{{ route('kiosk.welcome') }}" class="k-btn k-btn-outline" style="width:100%;justify-content:center">
            <i class="fas fa-house"></i> Kembali Sekarang
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
let secs = 20;
const barEl = document.getElementById('countdown-bar');
const numEl = document.getElementById('countdown-num');
const timer = setInterval(() => {
    secs--;
    numEl.textContent = secs;
    barEl.style.width = (secs / 20 * 100) + '%';
    if (secs <= 0) { clearInterval(timer); window.location = '{{ route("kiosk.welcome") }}'; }
}, 1000);
</script>
@endpush
