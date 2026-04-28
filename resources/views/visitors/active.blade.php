@extends('layouts.app')
@section('title', 'Tamu Aktif')
@section('page-title', 'Manajemen Tamu Aktif')

@push('styles')
<style>
/* ── Duration badge colors ─── */
.dur-ok   { color: #10b981; }
.dur-warn { color: #f59e0b; }
.dur-late { color: #f43f5e; }

.dur-row-ok   { border-left: 3px solid rgba(16,185,129,0.4); }
.dur-row-warn { border-left: 3px solid rgba(245,158,11,0.4); }
.dur-row-late { border-left: 3px solid rgba(244,63,94,0.4); background: rgba(244,63,94,0.02); }

/* ── Stat mini cards ───────── */
.method-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
.method-stat {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 10px; padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
}
.method-stat-icon { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
.method-stat-val  { font-size: 22px; font-weight: 800; }
.method-stat-lbl  { font-size: 11px; color: #64748b; }

/* ── Live timer ────────────── */
.live-timer { font-variant-numeric: tabular-nums; font-weight: 700; font-size: 13.5px; }

/* ── Pulse for very late ───── */
@keyframes late-pulse { 0%,100% { opacity:1; } 50% { opacity:.5; } }
.late-pulse { animation: late-pulse 1.5s ease-in-out infinite; }

/* ── Empty ─────────────────── */
.active-empty {
    text-align: center; padding: 80px 20px;
}
.active-empty .big-icon { font-size: 64px; margin-bottom: 16px; }
.active-empty h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.active-empty p { color: #64748b; font-size: 14px; }
</style>
@endpush

@section('content')

{{-- ── Checkout Stats ───────────────────── --}}
<div class="method-stats">
    <div class="method-stat">
        <div class="method-stat-icon" style="background:rgba(16,185,129,0.1)">🙋</div>
        <div>
            <div class="method-stat-val" style="color:#10b981">{{ $checkoutStats['self'] }}</div>
            <div class="method-stat-lbl">Self-checkout hari ini</div>
        </div>
    </div>
    <div class="method-stat">
        <div class="method-stat-icon" style="background:rgba(59,130,246,0.1)">👩‍💼</div>
        <div>
            <div class="method-stat-val" style="color:#60a5fa">{{ $checkoutStats['receptionist'] }}</div>
            <div class="method-stat-lbl">Checkout resepsionis</div>
        </div>
    </div>
    <div class="method-stat">
        <div class="method-stat-icon" style="background:rgba(245,158,11,0.1)">⏰</div>
        <div>
            <div class="method-stat-val" style="color:#f59e0b">{{ $checkoutStats['auto'] }}</div>
            <div class="method-stat-lbl">Auto-checkout (00:00)</div>
        </div>
    </div>
</div>

{{-- ── Header Actions ───────────────────── --}}
<div class="card">
    <div class="card-header">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:9px;height:9px;border-radius:50%;background:#10b981;
                        box-shadow:0 0 0 0 rgba(16,185,129,0.4);
                        animation:pulse 2s infinite"></div>
            <span class="card-title">Tamu Aktif Sekarang</span>
            <span class="badge badge-active" style="font-size:11px">{{ $activeVisitors->count() }}</span>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('visitors.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-list"></i> Semua Kunjungan
            </a>
            @if(auth()->user()->isSuperAdmin() && $activeVisitors->count() > 0)
            <form action="{{ route('admin.auto-checkout') }}" method="POST"
                  onsubmit="return confirm('Jalankan auto-checkout untuk SEMUA {{ $activeVisitors->count() }} tamu aktif?')">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:rgba(245,158,11,0.1);color:#f59e0b;border:1px solid rgba(245,158,11,0.2)">
                    <i class="fas fa-clock"></i> Auto-Checkout Semua
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($activeVisitors->isEmpty())
    <div class="active-empty">
        <div class="big-icon">🚪</div>
        <h3>Tidak Ada Tamu Aktif</h3>
        <p>Semua tamu sudah check-out atau belum ada yang datang hari ini.</p>
        <a href="{{ route('kiosk.welcome') }}" target="_blank" class="btn btn-primary" style="margin-top:16px">
            <i class="fas fa-tablet-screen-button"></i> Buka Kiosk Check-In
        </a>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Tamu</th>
                    <th>Instansi</th>
                    <th>Keperluan</th>
                    <th>Ditujukan Ke</th>
                    <th>Check-In</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="active-table">
                @foreach($activeVisitors as $i => $v)
                @php
                    $mins = $v->check_in_at->diffInMinutes(now());
                    $durClass = $mins < 60 ? 'dur-ok' : ($mins < 180 ? 'dur-warn' : 'dur-late');
                    $rowClass = $mins < 60 ? 'dur-row-ok' : ($mins < 180 ? 'dur-row-warn' : 'dur-row-late');
                @endphp
                <tr class="{{ $rowClass }}" data-checkin="{{ $v->check_in_at->timestamp }}">
                    <td style="color:#4b6074;font-size:12px">{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:600;color:#f1f5f9">{{ $v->name }}</div>
                        @if($v->phone)
                        <div style="font-size:11px;color:#4b6074">
                            <i class="fas fa-phone" style="margin-right:3px"></i>{{ $v->phone }}
                        </div>
                        @endif
                    </td>
                    <td>{{ $v->institution ?: '-' }}</td>
                    <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12.5px"
                        title="{{ $v->purpose }}">{{ $v->purpose }}</td>
                    <td>
                        @if($v->host)
                        <div style="font-size:12.5px">{{ $v->host->name }}</div>
                        <div style="font-size:11px;color:#4b6074">{{ $v->host->position }}</div>
                        @else
                        <span style="color:#4b6074;font-size:12px">{{ $v->department ?? '-' }}</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;font-size:13px">
                        {{ $v->check_in_at->format('H:i') }}
                        <div style="font-size:11px;color:#4b6074">{{ $v->check_in_at->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <span class="live-timer {{ $durClass }} {{ $mins >= 180 ? 'late-pulse' : '' }}"
                              data-ts="{{ $v->check_in_at->timestamp }}">
                            {{ $v->check_in_at->diff(now())->format('%H:%I:%S') }}
                        </span>
                        @if($mins >= 180)
                        <div style="font-size:10px;color:#f43f5e;margin-top:2px">⚠ Durasi Panjang</div>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center">
                            {{-- Quick Checkout (1 klik) --}}
                            <form action="{{ route('visitors.quick-checkout', $v) }}" method="POST"
                                  onsubmit="return confirm('Checkout tamu: {{ $v->name }}?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm"
                                        style="white-space:nowrap" title="Quick Checkout">
                                    <i class="fas fa-sign-out-alt"></i> Checkout
                                </button>
                            </form>
                            {{-- Detail / Form Checkout dengan Catatan --}}
                            <a href="{{ route('visitors.checkout', $v) }}" class="btn btn-outline btn-sm btn-icon"
                               title="Checkout dengan catatan">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            <a href="{{ route('visitors.show', $v) }}" class="btn btn-outline btn-sm btn-icon"
                               title="Lihat detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Legenda ──────────────────────────── --}}
    <div style="padding:12px 20px;border-top:1px solid var(--border);display:flex;gap:20px;font-size:12px;color:#64748b">
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:rgba(16,185,129,0.4);margin-right:5px"></span>< 1 jam — Normal</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:rgba(245,158,11,0.4);margin-right:5px"></span>1–3 jam — Perlu diperhatikan</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:rgba(244,63,94,0.4);margin-right:5px"></span>> 3 jam — Segera checkout</span>
    </div>
    @endif
</div>

{{-- ── Info Auto-Checkout ───────────────── --}}
<div class="card" style="margin-top:20px">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-clock" style="color:#f59e0b;margin-right:6px"></i>Jadwal Auto-Checkout</span>
    </div>
    <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
        <div style="font-size:13.5px;color:#94a3b8">
            Sistem akan secara otomatis men-checkout semua tamu yang masih aktif setiap harinya pada pukul
            <strong style="color:#f1f5f9">00:00 WIB</strong> (tengah malam).
            Checkout tercatat sebagai <span class="badge badge-auto" style="font-size:10px">Auto</span>.
        </div>
        <div style="display:flex;align-items:center;gap:16px">
            <div style="text-align:right">
                <div style="font-size:11px;color:#4b6074">Auto-checkout berikutnya</div>
                <div style="font-size:15px;font-weight:700;color:#f59e0b" id="next-auto-checkout">--:--:--</div>
            </div>
            @if(auth()->user()->isSuperAdmin())
            <div style="padding:12px;background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.15);border-radius:10px;font-size:12px;color:#94a3b8">
                <i class="fas fa-terminal" style="color:#f59e0b;margin-right:5px"></i>
                <code style="color:#fbbf24">php artisan visitors:auto-checkout</code>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ── Live Timer Update ─────────────────────────── */
function formatDuration(seconds) {
    const h = Math.floor(seconds / 3600).toString().padStart(2,'0');
    const m = Math.floor((seconds % 3600) / 60).toString().padStart(2,'0');
    const s = Math.floor(seconds % 60).toString().padStart(2,'0');
    return `${h}:${m}:${s}`;
}

function updateTimers() {
    const now = Math.floor(Date.now() / 1000);
    document.querySelectorAll('.live-timer[data-ts]').forEach(el => {
        const ts   = parseInt(el.dataset.ts);
        const secs = now - ts;
        el.textContent = formatDuration(secs);
        // Update color class
        const mins = secs / 60;
        el.className = el.className.replace(/dur-ok|dur-warn|dur-late/g, '');
        if      (mins < 60)  el.classList.add('dur-ok');
        else if (mins < 180) el.classList.add('dur-warn');
        else                 el.classList.add('dur-late', 'late-pulse');
    });
}
setInterval(updateTimers, 1000);

/* ── Countdown to midnight ─────────────────────── */
function updateMidnightCountdown() {
    const now = new Date();
    const midnight = new Date(now); midnight.setHours(24,0,0,0);
    const diff = Math.floor((midnight - now) / 1000);
    document.getElementById('next-auto-checkout').textContent = formatDuration(diff);
}
setInterval(updateMidnightCountdown, 1000);
updateMidnightCountdown();

/* ── Auto-refresh setiap 60 detik ─────────────── */
setTimeout(() => location.reload(), 60000);
</script>
@endpush
