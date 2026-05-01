@extends('layouts.app')
@section('title', 'Pengaturan WhatsApp')
@section('page-title', 'WhatsApp Gateway')

@section('content')
<div style="display:grid;grid-template-columns: 1fr 1.5fr; gap: 24px;">
    
    {{-- KOLOM KIRI: Form & Control --}}
    <div style="display:flex;flex-direction:column;gap:24px">
        
        {{-- Card Status --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Status Gateway</span></div>
            <div class="card-body" style="text-align:center; padding: 30px;">
                @if($status)
                    <div style="width:60px;height:60px;background:rgba(16,185,129,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#10b981;font-size:24px;box-shadow:0 0 20px rgba(16,185,129,0.2)">
                        <i class="fas fa-check-circle animate-pulse"></i>
                    </div>
                    <h3 style="font-size:18px;color:#10b981;margin-bottom:4px">Gateway Aktif</h3>
                    <p style="font-size:13px;color:#94a3b8;margin-bottom:24px">Server WhatsApp lokal sedang berjalan.</p>
                    
                    <form action="{{ route('settings.whatsapp.stop') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center">
                            <i class="fas fa-power-off"></i> Matikan Gateway
                        </button>
                    </form>
                @else
                    <div style="width:60px;height:60px;background:rgba(244,63,94,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#fb7185;font-size:24px">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3 style="font-size:18px;color:#fb7185;margin-bottom:4px">Gateway Mati</h3>
                    <p style="font-size:13px;color:#94a3b8;margin-bottom:24px">Server WhatsApp lokal tidak terdeteksi.</p>
                    
                    <form action="{{ route('settings.whatsapp.start') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center">
                            <i class="fas fa-play"></i> Jalankan Gateway
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Card Konfigurasi --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Konfigurasi API</span></div>
            <div class="card-body">
                <form action="{{ route('settings.whatsapp.update') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:20px">
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:8px;font-weight:600">ENDPOINT URL</label>
                        <input type="text" name="gateway_url" class="k-input" value="{{ $gatewayUrl }}" placeholder="http://localhost:3000/send-message" style="background:#0f172a; border-color:#1f2d4a; color:white">
                        <p style="font-size:11px;color:#4b6074;margin-top:6px">Gunakan alamat lokal jika menjalankan gateway di PC ini.</p>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Logs & QR --}}
    <div class="card" style="display:flex; flex-direction:column">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
            <span class="card-title">Aktivitas & QR Code</span>
            <button onclick="location.reload()" class="btn btn-sm btn-outline" style="padding:4px 10px; font-size:11px">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
        <div class="card-body" style="flex:1; background:#0a0f1e; border-radius:0 0 12px 12px; padding:0; overflow:hidden">
            <pre style="margin:0; padding:20px; color:#10b981; font-family: 'Courier New', Courier, monospace; font-size:12px; line-height:1.4; overflow-y:auto; height: 500px;">{{ $logs }}</pre>
        </div>
        <div style="padding:12px; font-size:11px; color:#4b6074; background:rgba(0,0,0,0.2); border-top:1px solid #1f2d4a">
            <i class="fas fa-info-circle"></i> Jika muncul QR Code di atas, silakan scan menggunakan aplikasi WhatsApp Anda (Perangkat Tertaut).
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
    pre::-webkit-scrollbar { width: 6px; }
    pre::-webkit-scrollbar-track { background: transparent; }
    pre::-webkit-scrollbar-thumb { background: #1f2d4a; border-radius: 10px; }
</style>
@endpush
