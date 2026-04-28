@extends('layouts.kiosk')
@section('title', 'Check-Out Tamu')

@push('styles')
<style>
.checkout-wrap { max-width: 500px; width: 100%; }
.checkout-card {
    background: rgba(13,22,41,0.9);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px; padding: 36px 32px;
    backdrop-filter: blur(20px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    text-align: center;
}
.checkout-icon {
    width: 80px; height: 80px; border-radius: 20px;
    background: rgba(16,185,129,0.1); border: 1.5px solid rgba(16,185,129,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 38px; margin: 0 auto 20px;
}
.checkout-title { font-size: 26px; font-weight: 800; margin-bottom: 8px; }
.checkout-sub { font-size: 14px; color: #64748b; margin-bottom: 28px; }

.code-input-wrap { position: relative; margin-bottom: 20px; }
.code-input-wrap input {
    text-align: center; letter-spacing: 4px;
    font-size: 22px; font-weight: 800;
    text-transform: uppercase;
}
.code-input-wrap input::placeholder { letter-spacing: 1px; font-size: 15px; font-weight: 400; text-transform: none; }
</style>
@endpush

@section('content')
<div class="checkout-wrap animate-in">
    <div class="checkout-card">
        <div class="checkout-icon">👋</div>
        <h1 class="checkout-title">Check-Out Tamu</h1>
        <p class="checkout-sub">Masukkan kode kunjungan yang Anda terima saat check-in untuk melakukan check-out</p>

        <form action="{{ route('kiosk.checkout.post') }}" method="POST">
            @csrf
            <div class="code-input-wrap">
                <input type="text" name="visit_code" class="k-input {{ $errors->has('visit_code') ? 'k-input-error' : '' }}"
                       value="{{ old('visit_code') }}"
                       placeholder="Contoh: VIS-XXXXXXXX"
                       maxlength="12"
                       autocomplete="off"
                       autofocus
                       oninput="this.value = this.value.toUpperCase()">
                @error('visit_code')
                    <div class="k-error" style="text-align:center;margin-top:8px">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="k-btn k-btn-success k-btn-full" style="font-size:16px;padding:15px">
                <i class="fas fa-sign-out-alt"></i> Lakukan Check-Out
            </button>
        </form>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.06)">
            <a href="{{ route('kiosk.welcome') }}" class="k-btn k-btn-outline" style="width:100%;justify-content:center">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
