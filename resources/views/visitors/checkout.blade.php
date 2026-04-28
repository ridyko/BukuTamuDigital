@extends('layouts.app')
@section('title', 'Checkout Tamu')
@section('page-title', 'Checkout Manual')

@section('content')
<div style="max-width:560px;margin:0 auto">
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="fas fa-sign-out-alt" style="color:#10b981;margin-right:6px"></i>
                Checkout Manual oleh Resepsionis
            </span>
        </div>
        <div class="card-body">
            {{-- Info Tamu --}}
            <div style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.15);border-radius:10px;padding:16px;margin-bottom:24px">
                <div style="font-size:11px;color:#10b981;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
                    Data Tamu
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div>
                        <div style="font-size:11px;color:#4b6074">Nama</div>
                        <div style="font-weight:600;color:#f1f5f9">{{ $visitor->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074">Instansi</div>
                        <div style="color:#94a3b8">{{ $visitor->institution ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074">Keperluan</div>
                        <div style="color:#94a3b8">{{ $visitor->purpose }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074">Check-In</div>
                        <div style="color:#94a3b8">{{ $visitor->check_in_at?->format('H:i, d M Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074">Durasi Sejauh Ini</div>
                        <div style="color:#f59e0b;font-weight:600">{{ $visitor->duration }} menit</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#4b6074">Ditujukan Kepada</div>
                        <div style="color:#94a3b8">{{ $visitor->host?->name ?? $visitor->department ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Form Checkout --}}
            <form action="{{ route('visitors.checkout.process', $visitor) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Misal: Tamu meminjam peralatan, akan dikembalikan besok...">{{ old('notes', $visitor->notes) }}</textarea>
                </div>

                <div style="background:rgba(59,130,246,0.06);border:1px solid rgba(59,130,246,0.15);border-radius:8px;padding:12px;margin-bottom:20px;font-size:13px;color:#94a3b8">
                    <i class="fas fa-info-circle" style="color:#3b82f6;margin-right:6px"></i>
                    Checkout akan dicatat atas nama <strong style="color:#f1f5f9">{{ auth()->user()->name }}</strong>
                    dengan metode <strong style="color:#f1f5f9">Resepsionis</strong>.
                </div>

                <div style="display:flex;gap:10px">
                    <button type="submit" class="btn btn-success" style="flex:1;justify-content:center">
                        <i class="fas fa-check"></i> Konfirmasi Checkout
                    </button>
                    <a href="{{ route('visitors.show', $visitor) }}" class="btn btn-outline">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
