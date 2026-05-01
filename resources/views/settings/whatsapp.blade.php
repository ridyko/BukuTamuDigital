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
                    
                    {{-- Progress Bar (Hidden by default) --}}
                    <div id="loading-area" style="display:none; margin-bottom: 24px;">
                        <div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:8px">
                            <span id="loading-text">Menyiapkan Browser...</span>
                            <span id="loading-percent">0%</span>
                        </div>
                        <div style="width:100%; height:6px; background:#1f2d4a; border-radius:10px; overflow:hidden">
                            <div id="progress-bar" style="width:0%; height:100%; background:var(--accent); transition:width 0.3s"></div>
                        </div>
                    </div>

                    <form action="{{ route('settings.whatsapp.start') }}" method="POST" id="start-form">
                        @csrf
                        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center; margin-bottom:12px" id="start-btn">
                            <i class="fas fa-play"></i> Jalankan Gateway
                        </button>
                    </form>

                    <form action="{{ route('settings.whatsapp.reset') }}" method="POST" onsubmit="return confirm('Hapus semua data sesi dan scan ulang?')">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center; color:#94a3b8; border-color:#1f2d4a">
                            <i class="fas fa-undo"></i> Reset Sesi & Scan Ulang
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
    <div class="card" style="display:flex; flex-direction:column; height: 100%">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
            <span class="card-title">Aktivitas & QR Code</span>
            <div style="display:flex; gap:8px">
                <a href="{{ route('settings.whatsapp.qr') }}" target="_blank" class="btn btn-sm btn-outline" style="padding:4px 10px; font-size:11px; border-color:var(--accent); color:var(--accent)">
                    <i class="fas fa-qrcode"></i> Buka QR di Tab Baru
                </a>
                <button onclick="location.reload()" class="btn btn-sm btn-outline" style="padding:4px 10px; font-size:11px">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body" style="flex:1; background:#0a0f1e; border-radius:0 0 12px 12px; padding:0; overflow:hidden; position:relative; display:flex; flex-direction:column">
            @php
                $qrFile = storage_path('app/public/wa_qr.txt');
                $qrContent = File::exists($qrFile) ? File::get($qrFile) : null;
            @endphp

            @if($qrContent && !$status)
                <div style="padding:40px; text-align:center; background:#111827; border-bottom:1px solid #1f2d4a">
                    <h4 style="color:white; margin-bottom:20px; font-size:14px">Scan QR Code Berikut:</h4>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($qrContent) }}" style="border:10px solid white; border-radius:10px">
                    <p style="color:#94a3b8; font-size:12px; margin-top:20px">Buka WhatsApp > Perangkat Tertaut > Tautkan Perangkat</p>
                </div>
            @endif

            <pre style="margin:0; padding:20px; color:#10b981; font-family: 'Courier New', Courier, monospace; font-size:12px; line-height:1.4; overflow-y:auto; flex:1; min-height:500px">{{ $logs }}</pre>
        </div>
        <div style="padding:12px; font-size:11px; color:#4b6074; background:rgba(0,0,0,0.2); border-top:1px solid #1f2d4a">
            <i class="fas fa-info-circle"></i> Jika muncul QR Code di atas, silakan scan menggunakan aplikasi WhatsApp Anda (Perangkat Tertaut).
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const startForm = document.getElementById('start-form');
    const startBtn = document.getElementById('start-btn');
    const loadingArea = document.getElementById('loading-area');
    const progressBar = document.getElementById('progress-bar');
    const loadingPercent = document.getElementById('loading-percent');
    const loadingText = document.getElementById('loading-text');

    if (startForm) {
        startForm.addEventListener('submit', function() {
            startBtn.style.display = 'none';
            loadingArea.style.display = 'block';
            
            let progress = 0;
            const interval = setInterval(() => {
                progress += 1;
                progressBar.style.width = progress + '%';
                loadingPercent.innerText = progress + '%';

                if (progress < 30) {
                    loadingText.innerText = 'Menginisialisasi Node.js...';
                } else if (progress < 60) {
                    loadingText.innerText = 'Membuka Headless Browser...';
                } else if (progress < 90) {
                    loadingText.innerText = 'Memuat WhatsApp Web...';
                } else {
                    loadingText.innerText = 'Menghasilkan QR Code...';
                }

                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        location.reload(); // Refresh untuk melihat QR
                    }, 1000);
                }
            }, 200); // 200ms * 100 = 20 detik
        });
    }
</script>
@endpush

@push('styles')
<style>
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
    pre::-webkit-scrollbar { width: 6px; }
    pre::-webkit-scrollbar-track { background: transparent; }
    pre::-webkit-scrollbar-thumb { background: #1f2d4a; border-radius: 10px; }
</style>
@endpush
