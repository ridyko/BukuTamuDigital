@extends('layouts.app')
@section('title', 'Tamu Hari Ini')
@section('page-title', 'Tamu Hari Ini')

@section('content')
{{-- ── FILTER BAR ────────────────────────── --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 20px">
        <form method="GET" action="{{ route('visitors.index') }}">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:2;min-width:200px">
                    <label class="form-label" style="margin-bottom:5px">Cari Tamu</label>
                    <div style="position:relative">
                        <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#4b6074;font-size:13px"></i>
                        <input type="text" name="search" class="form-control" style="padding-left:36px"
                               value="{{ request('search') }}" placeholder="Nama, kode, atau instansi...">
                    </div>
                </div>
                <div style="flex:1;min-width:140px">
                    <label class="form-label" style="margin-bottom:5px">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="active"      {{ request('status')=='active' ? 'selected':'' }}>Aktif (Di Dalam)</option>
                        <option value="checked_out" {{ request('status')=='checked_out' ? 'selected':'' }}>Sudah Checkout</option>
                    </select>
                </div>
                <div style="flex:1;min-width:160px">
                    <label class="form-label" style="margin-bottom:5px">Tanggal</label>
                    <input type="date" name="date" class="form-control"
                           value="{{ request('date', today()->format('Y-m-d')) }}">
                </div>
                <div style="flex:1;min-width:160px">
                    <label class="form-label" style="margin-bottom:5px">Tujuan (Staf)</label>
                    <select name="host_id" class="form-control">
                        <option value="">Semua Staf</option>
                        @foreach($hosts as $host)
                        <option value="{{ $host->id }}" {{ request('host_id')==$host->id ? 'selected':'' }}>
                            {{ $host->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:8px;flex-shrink:0">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('visitors.index') }}" class="btn btn-outline"><i class="fas fa-rotate-left"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── TABLE ─────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            Data Kunjungan
            <span style="font-weight:400;color:#64748b;font-size:13px;margin-left:6px">({{ $visitors->total() }} data)</span>
        </span>
        <a href="{{ route('visitors.history') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-history"></i> Riwayat
        </a>
    </div>

    @if($visitors->isEmpty())
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <p>Tidak ada data kunjungan ditemukan</p>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Tamu</th>
                    <th>Instansi</th>
                    <th>Keperluan</th>
                    <th>Tujuan</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visitors as $i => $v)
                <tr>
                    <td style="color:#4b6074;font-size:12px">{{ $visitors->firstItem() + $i }}</td>
                    <td>
                        <code style="font-size:11px;background:rgba(255,255,255,0.05);padding:2px 7px;border-radius:5px;color:#94a3b8">
                            {{ $v->visit_code }}
                        </code>
                    </td>
                    <td>
                        <div style="font-weight:600;color:#f1f5f9">{{ $v->name }}</div>
                        @if($v->phone)
                        <div style="font-size:11px;color:#4b6074"><i class="fas fa-phone" style="margin-right:3px"></i>{{ $v->phone }}</div>
                        @endif
                    </td>
                    <td>{{ $v->institution ?: '-' }}</td>
                    <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $v->purpose }}">
                        {{ $v->purpose }}
                    </td>
                    <td>
                        @if($v->host)
                            <div style="font-size:12.5px">{{ $v->host->name }}</div>
                            <div style="font-size:11px;color:#4b6074">{{ $v->host->position }}</div>
                        @else
                            <span style="color:#4b6074">{{ $v->department ?? '-' }}</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;font-size:12.5px">
                        {{ $v->check_in_at?->format('H:i') }}
                    </td>
                    <td style="white-space:nowrap;font-size:12.5px">
                        {{ $v->check_out_at ? $v->check_out_at->format('H:i') : '-' }}
                    </td>
                    <td>
                        @if($v->checkout_method == 'receptionist')
                            <span class="badge" style="background:rgba(59,130,246,0.1);color:#60a5fa;border:1px solid rgba(59,130,246,0.2);font-size:10px">
                                <i class="fas fa-user-tie"></i> Resepsionis
                            </span>
                        @elseif($v->checkout_method == 'auto')
                            <span class="badge badge-auto" style="font-size:10px"><i class="fas fa-clock"></i> Auto</span>
                        @elseif($v->checkout_method == 'self')
                            <span class="badge" style="background:rgba(16,185,129,0.1);color:#34d399;border:1px solid rgba(16,185,129,0.2);font-size:10px">
                                <i class="fas fa-hand"></i> Mandiri
                            </span>
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
                        <div style="display:flex;gap:5px">
                            <a href="{{ route('visitors.show', $v) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($v->isActive())
                                <a href="{{ route('visitors.checkout', $v) }}" class="btn btn-success btn-sm btn-icon" title="Checkout">
                                    <i class="fas fa-sign-out-alt"></i>
                                </a>
                            @endif
                            @if(auth()->user()->isSuperAdmin())
                            <form action="{{ route('visitors.destroy', $v) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon"
                                        title="Hapus"
                                        data-confirm="Yakin hapus data kunjungan {{ $v->name }}?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($visitors->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $visitors->firstItem() }}–{{ $visitors->lastItem() }} dari {{ $visitors->total() }} data
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

@push('scripts')
<script>
// Auto-confirm delete
document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
});
</script>
@endpush
