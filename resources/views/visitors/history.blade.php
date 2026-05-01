@extends('layouts.app')
@section('title', 'Riwayat Kunjungan')
@section('page-title', 'Riwayat Kunjungan')

@section('content')
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 20px">
        <form method="GET" action="{{ route('visitors.history') }}">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:2;min-width:200px">
                    <label class="form-label" style="margin-bottom:5px">Cari</label>
                    <div style="position:relative">
                        <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#4b6074;font-size:13px"></i>
                        <input type="text" name="search" class="form-control" style="padding-left:36px"
                               value="{{ request('search') }}" placeholder="Nama, kode, atau instansi...">
                    </div>
                </div>
                <div style="flex:1;min-width:130px">
                    <label class="form-label" style="margin-bottom:5px">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="active"      {{ request('status')=='active' ? 'selected':'' }}>Aktif</option>
                        <option value="checked_out" {{ request('status')=='checked_out' ? 'selected':'' }}>Checkout</option>
                    </select>
                </div>
                <div style="flex:1;min-width:140px">
                    <label class="form-label" style="margin-bottom:5px">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div style="flex:1;min-width:140px">
                    <label class="form-label" style="margin-bottom:5px">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('visitors.history') }}" class="btn btn-outline"><i class="fas fa-rotate-left"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            Semua Riwayat
            <span style="font-weight:400;color:#64748b;font-size:13px;margin-left:6px">({{ $visitors->total() }} data)</span>
        </span>
    </div>

    @if($visitors->isEmpty())
        <div class="empty-state">
            <i class="fas fa-box-archive"></i>
            <p>Tidak ada riwayat kunjungan ditemukan</p>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Tamu</th>
                    <th>Instansi</th>
                    <th>Tujuan</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Durasi</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visitors as $v)
                <tr>
                    <td>
                        <code style="font-size:11px;background:rgba(255,255,255,0.05);padding:2px 6px;border-radius:4px;color:#94a3b8">
                            {{ $v->visit_code }}
                        </code>
                    </td>
                    <td style="font-weight:600;color:var(--text-primary)">{{ $v->name }}</td>
                    <td>{{ $v->institution ?: '-' }}</td>
                    <td style="font-size:12.5px">{{ $v->host?->name ?? $v->department ?? '-' }}</td>
                    <td style="white-space:nowrap;font-size:12.5px">{{ $v->check_in_at?->format('d/m/Y H:i') }}</td>
                    <td style="white-space:nowrap;font-size:12.5px">{{ $v->check_out_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td style="font-size:12.5px">
                        @if($v->duration !== null)
                            @php $h = intdiv($v->duration,60); $m = $v->duration%60; @endphp
                            {{ $h > 0 ? $h.'j ' : '' }}{{ $m }}m
                        @else -
                        @endif
                    </td>
                    <td>
                        @if($v->checkout_method == 'receptionist')
                            <span class="badge" style="background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2);font-size:10px">Resepsionis</span>
                        @elseif($v->checkout_method == 'auto')
                            <span class="badge badge-auto" style="font-size:10px">Auto</span>
                        @elseif($v->checkout_method == 'self')
                            <span class="badge" style="background:rgba(16,185,129,0.1);color:#34d399;border:1px solid rgba(16,185,129,0.2);font-size:10px">Mandiri</span>
                        @else
                            <span style="color:#4b6074;font-size:12px">-</span>
                        @endif
                    </td>
                    <td>
                        @if($v->status == 'active')
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-out">Selesai</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('visitors.show', $v) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($visitors->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            {{ $visitors->firstItem() }}–{{ $visitors->lastItem() }} dari {{ $visitors->total() }} data
        </div>
        <div class="pagination">
            @if($visitors->onFirstPage())
                <span class="disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $visitors->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif
            @foreach($visitors->getUrlRange(max(1,$visitors->currentPage()-2), min($visitors->lastPage(),$visitors->currentPage()+2)) as $page => $url)
                @if($page == $visitors->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($visitors->hasMorePages())
                <a href="{{ $visitors->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
