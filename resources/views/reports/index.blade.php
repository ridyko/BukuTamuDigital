@extends('layouts.app')
@section('title', 'Laporan Kunjungan')
@section('page-title', 'Laporan & Rekapitulasi')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-filter" style="margin-right: 8px; color: var(--accent);"></i> Filter Laporan</span>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status Kunjungan</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif (Belum Checkout)</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Selesai (Checked Out)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ditujukan Ke (Staf)</label>
                    <select name="host_id" class="form-control">
                        <option value="">Semua Staf</option>
                        @foreach($hosts as $host)
                            <option value="{{ $host->id }}" {{ request('host_id') == $host->id ? 'selected' : '' }}>
                                {{ $host->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan Preview
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="card-title">Preview Data ({{ $visitors->total() }} record)</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('reports.excel', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('reports.pdf', request()->all()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Tamu</th>
                    <th>Instansi</th>
                    <th>Tujuan</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visitors as $v)
                <tr>
                    <td><code style="color: var(--accent);">{{ $v->visit_code }}</code></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $v->name }}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">{{ $v->phone ?? '-' }}</div>
                    </td>
                    <td>{{ $v->institution ?? '-' }}</td>
                    <td>{{ $v->host?->name ?? $v->department ?? '-' }}</td>
                    <td>{{ $v->check_in_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $v->check_out_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>
                        @if($v->status == 'active')
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-out">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>Tidak ada data ditemukan untuk filter ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($visitors->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $visitors->firstItem() }} to {{ $visitors->lastItem() }} of {{ $visitors->total() }} entries</div>
        <div class="pagination">
            {{ $visitors->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
