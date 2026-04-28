@extends('layouts.app')
@section('title', 'Detail Kunjungan')
@section('page-title', 'Detail Kunjungan')

@section('content')
<div style="display:flex;gap:20px;flex-wrap:wrap">
    {{-- ── KOLOM KIRI: Info Utama ─────────────── --}}
    <div style="flex:2;min-width:300px;display:flex;flex-direction:column;gap:20px">

        {{-- Header Card --}}
        <div class="card">
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                    <div style="width:60px;height:60px;border-radius:14px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">
                        👤
                    </div>
                    <div>
                        <h2 style="font-size:20px;font-weight:700;color:#f1f5f9">{{ $visitor->name }}</h2>
                        <div style="font-size:13px;color:#94a3b8">{{ $visitor->institution ?: 'Tidak ada instansi' }}</div>
                        <div style="margin-top:6px">
                            @if($visitor->isActive())
                                <span class="badge badge-active">🟢 Masih di Dalam</span>
                            @else
                                <span class="badge badge-out">Sudah Keluar</span>
                            @endif
                        </div>
                    </div>
                    <div style="margin-left:auto">
                        <code style="font-size:13px;background:rgba(255,255,255,0.05);padding:6px 12px;border-radius:8px;color:#94a3b8;border:1px solid #1f2d4a">
                            {{ $visitor->visit_code }}
                        </code>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <div style="font-size:11px;color:#4b6074;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">No. Identitas</div>
                        <div style="font-size:14px;color:#f1f5f9">{{ $visitor->id_number ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">No. HP</div>
                        <div style="font-size:14px;color:#f1f5f9">{{ $visitor->phone ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Keperluan</div>
                        <div style="font-size:14px;color:#f1f5f9">{{ $visitor->purpose }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Ditujukan Kepada</div>
                        <div style="font-size:14px;color:#f1f5f9">{{ $visitor->host?->name ?? $visitor->department ?? '-' }}</div>
                        @if($visitor->host)
                        <div style="font-size:12px;color:#4b6074">{{ $visitor->host->position }}</div>
                        @endif
                    </div>
                </div>

                @if($visitor->notes)
                <div style="margin-top:16px;padding:12px;background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.15);border-radius:8px">
                    <div style="font-size:11px;color:#f59e0b;font-weight:600;margin-bottom:4px">📝 Catatan</div>
                    <div style="font-size:13px;color:#94a3b8">{{ $visitor->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Timeline Check-in / Check-out --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Waktu Kunjungan</span></div>
            <div class="card-body">
                <div style="display:flex;gap:24px;flex-wrap:wrap">
                    <div style="text-align:center;flex:1">
                        <div style="font-size:11px;color:#4b6074;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Check-In</div>
                        <div style="font-size:28px;font-weight:800;color:#10b981">
                            {{ $visitor->check_in_at?->format('H:i') ?? '-' }}
                        </div>
                        <div style="font-size:12px;color:#94a3b8;margin-top:4px">
                            {{ $visitor->check_in_at?->isoFormat('dddd, D MMMM Y') }}
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;color:#1f2d4a">
                        <i class="fas fa-arrow-right" style="font-size:20px"></i>
                    </div>

                    <div style="text-align:center;flex:1">
                        <div style="font-size:11px;color:#4b6074;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Check-Out</div>
                        @if($visitor->check_out_at)
                        <div style="font-size:28px;font-weight:800;color:
                            {{ $visitor->checkout_method == 'auto' ? '#f59e0b' : '#f1f5f9' }}">
                            {{ $visitor->check_out_at->format('H:i') }}
                        </div>
                        <div style="font-size:12px;color:#94a3b8;margin-top:4px">
                            {{ $visitor->checkout_method_label }}
                            @if($visitor->checkedOutBy)
                                — {{ $visitor->checkedOutBy->name }}
                            @endif
                        </div>
                        @else
                        <div style="font-size:28px;font-weight:800;color:#4b6074">--:--</div>
                        <div style="font-size:12px;color:#4b6074">Belum checkout</div>
                        @endif
                    </div>

                    <div style="text-align:center;flex:1;border-left:1px solid #1f2d4a;padding-left:24px">
                        <div style="font-size:11px;color:#4b6074;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Durasi</div>
                        <div style="font-size:28px;font-weight:800;color:#8b5cf6">
                            {{ $visitor->duration }} <span style="font-size:16px;font-weight:400">mnt</span>
                        </div>
                        <div style="font-size:12px;color:#94a3b8">
                            @php
                            $h = intdiv($visitor->duration ?? 0, 60);
                            $m = ($visitor->duration ?? 0) % 60;
                            @endphp
                            @if($h > 0) {{ $h }} jam @endif {{ $m }} menit
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Log Aktivitas --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Log Aktivitas</span></div>
            <div class="card-body" style="padding:0">
                @foreach($visitor->activityLogs as $log)
                <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 20px;border-bottom:1px solid rgba(31,45,74,0.4)">
                    <div style="font-size:18px;margin-top:2px">{{ $log->action_icon }}</div>
                    <div style="flex:1">
                        <div style="font-size:13.5px;font-weight:600;color:#f1f5f9">{{ $log->action_label }}</div>
                        @if($log->note)
                        <div style="font-size:12px;color:#94a3b8;margin-top:2px">{{ $log->note }}</div>
                        @endif
                        <div style="font-size:11px;color:#4b6074;margin-top:4px">
                            {{ $log->performer?->name ?? '⚙️ Sistem' }} •
                            {{ $log->created_at?->isoFormat('D MMM Y, HH:mm') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── KOLOM KANAN: Foto + Aksi ────────────── --}}
    <div style="flex:1;min-width:240px;display:flex;flex-direction:column;gap:20px">

        {{-- Foto --}}
        @if($visitor->photo)
        <div class="card">
            <div class="card-header"><span class="card-title">Foto Tamu</span></div>
            <div class="card-body" style="text-align:center">
                <img src="{{ asset('storage/'.$visitor->photo) }}"
                     style="border-radius:10px;width:100%;max-height:220px;object-fit:cover;border:1px solid #1f2d4a"
                     alt="Foto Tamu">
            </div>
        </div>
        @endif

        {{-- Aksi --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Aksi</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
                @if($visitor->isActive() && (auth()->user()->isSuperAdmin() || auth()->user()->isReceptionist()))
                <a href="{{ route('visitors.checkout', $visitor) }}" class="btn btn-success" style="width:100%;justify-content:center">
                    <i class="fas fa-sign-out-alt"></i> Checkout Tamu Ini
                </a>
                @endif
                <a href="{{ route('visitors.index') }}" class="btn btn-outline" style="width:100%;justify-content:center">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                @if(auth()->user()->isSuperAdmin())
                <form action="{{ route('visitors.destroy', $visitor) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center"
                            data-confirm="Yakin hapus data kunjungan ini secara permanen?">
                        <i class="fas fa-trash"></i> Hapus Data
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- QR Code --}}
        @if($visitor->qr_code)
        <div class="card">
            <div class="card-header"><span class="card-title">QR Code</span></div>
            <div class="card-body" style="text-align:center">
                <img src="{{ asset('storage/'.$visitor->qr_code) }}"
                     style="width:140px;height:140px;border-radius:8px;border:1px solid #1f2d4a"
                     alt="QR Code">
                <div style="font-size:11px;color:#4b6074;margin-top:8px">{{ $visitor->visit_code }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
