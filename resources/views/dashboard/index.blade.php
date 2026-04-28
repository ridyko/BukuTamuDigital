@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
.grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
.grid-2-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media (max-width: 1024px) { .grid-2 { grid-template-columns: 1fr; } }
@media (max-width: 768px)  { .grid-2-equal { grid-template-columns: 1fr; } }

.chart-bar-wrap { display: flex; align-items: flex-end; gap: 8px; height: 120px; padding: 0 4px; }
.chart-bar-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
.chart-bar {
    width: 100%; border-radius: 6px 6px 0 0;
    background: linear-gradient(180deg, #3b82f6, #6366f1);
    transition: height .6s ease;
    min-height: 4px; position: relative;
}
.chart-bar:hover::after {
    content: attr(data-val);
    position: absolute; top: -26px; left: 50%;
    transform: translateX(-50%);
    background: #1e293b; color: #f1f5f9;
    font-size: 11px; padding: 2px 7px; border-radius: 5px;
    border: 1px solid #1f2d4a; white-space: nowrap;
}
.chart-label { font-size: 10px; color: #4b6074; text-align: center; white-space: nowrap; overflow: hidden; }

.active-pulse {
    width: 8px; height: 8px; border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16,185,129,0.4);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
    70%  { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
}

.host-bar-item { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.host-bar-label { font-size: 12.5px; color: #94a3b8; width: 160px; flex-shrink: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.host-bar-track { flex: 1; height: 6px; background: rgba(255,255,255,0.06); border-radius: 10px; overflow: hidden; }
.host-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #3b82f6, #8b5cf6); transition: width .8s ease; }
.host-bar-count { font-size: 12px; color: #94a3b8; width: 24px; text-align: right; flex-shrink: 0; }
</style>
@endpush

@section('content')
{{-- ── STAT CARDS ───────────────────────── --}}
<div class="stat-grid">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-value">{{ $stats['total_today'] }}</div>
        <div class="stat-label">Total Kunjungan Hari Ini</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
        <div class="stat-value">{{ $stats['active_now'] }}</div>
        <div class="stat-label">Tamu Masih di Dalam</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-user-minus"></i></div>
        <div class="stat-value">{{ $stats['checked_out_today'] }}</div>
        <div class="stat-label">Sudah Checkout Hari Ini</div>
    </div>
    <div class="stat-card rose">
        <div class="stat-icon rose"><i class="fas fa-calendar-month"></i></div>
        <div class="stat-value">{{ $stats['total_this_month'] }}</div>
        <div class="stat-label">Total Bulan Ini</div>
    </div>
</div>

{{-- ── MAIN GRID ────────────────────────── --}}
<div class="grid-2" style="margin-bottom:20px">
    {{-- Tabel Tamu Aktif --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="active-pulse"></div>
                <span class="card-title">Tamu Aktif Sekarang</span>
                <span class="badge badge-active" style="font-size:11px">{{ $stats['active_now'] }}</span>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isReceptionist())
            <a href="{{ route('visitors.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-right"></i> Lihat Semua
            </a>
            @endif
        </div>
        @if($activeVisitors->isEmpty())
            <div class="empty-state">
                <i class="fas fa-door-open"></i>
                <p>Tidak ada tamu aktif saat ini</p>
            </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Tamu</th>
                        <th>Tujuan</th>
                        <th>Check-in</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeVisitors as $v)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#f1f5f9">{{ $v->name }}</div>
                            <div style="font-size:11px;color:#4b6074">{{ $v->institution ?: '-' }}</div>
                        </td>
                        <td>{{ $v->host?->name ?? $v->department ?? '-' }}</td>
                        <td style="white-space:nowrap">{{ $v->check_in_at?->format('H:i') }}</td>
                        <td>
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isReceptionist())
                            <a href="{{ route('visitors.checkout', $v) }}" class="btn btn-success btn-sm btn-icon" title="Checkout">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Grafik + Top Host --}}
    <div style="display:flex;flex-direction:column;gap:20px">
        {{-- Chart 7 hari --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Kunjungan 7 Hari Terakhir</span>
            </div>
            <div class="card-body">
                @php $maxVal = $chartData->max('count') ?: 1; @endphp
                <div class="chart-bar-wrap">
                    @foreach($chartData as $d)
                    <div class="chart-bar-item">
                        <div class="chart-bar"
                             data-val="{{ $d['count'] }}"
                             style="height: {{ max(4, ($d['count']/$maxVal)*100) }}%">
                        </div>
                        <div class="chart-label">{{ $d['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Top Host --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Tamu Terbanyak Hari Ini</span>
            </div>
            <div class="card-body">
                @if($topHosts->sum('visits_count') == 0)
                    <p style="color:#4b6074;font-size:13px;text-align:center;padding:20px 0">Belum ada data hari ini</p>
                @else
                    @php $maxHost = $topHosts->max('visits_count') ?: 1; @endphp
                    @foreach($topHosts as $host)
                    @if($host->visits_count > 0)
                    <div class="host-bar-item">
                        <div class="host-bar-label" title="{{ $host->name }}">{{ $host->name }}</div>
                        <div class="host-bar-track">
                            <div class="host-bar-fill" style="width:{{ ($host->visits_count/$maxHost)*100 }}%"></div>
                        </div>
                        <div class="host-bar-count">{{ $host->visits_count }}</div>
                    </div>
                    @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── LOG AKTIVITAS TERBARU ──────────────── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list-check" style="color:#3b82f6;margin-right:6px"></i>Log Aktivitas Terbaru</span>
    </div>
    @if($recentLogs->isEmpty())
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>Belum ada aktivitas tercatat</p>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Aksi</th>
                    <th>Nama Tamu</th>
                    <th>Dilakukan Oleh</th>
                    <th>Keterangan</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentLogs as $log)
                <tr>
                    <td>
                        <span style="font-size:14px">{{ $log->action_icon }}</span>
                        <span style="margin-left:4px;font-size:12.5px;font-weight:500;color:#f1f5f9">{{ $log->action_label }}</span>
                    </td>
                    <td>{{ $log->visitor?->name ?? '-' }}</td>
                    <td>{{ $log->performer?->name ?? '⚙️ Sistem' }}</td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px" title="{{ $log->note }}">
                        {{ $log->note ?? '-' }}
                    </td>
                    <td style="white-space:nowrap;font-size:12px">{{ $log->created_at?->format('d/m H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
