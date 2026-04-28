@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@push('styles')
<style>
.notif-page-item {
    display: flex; gap: 14px; padding: 18px 20px;
    border-bottom: 1px solid rgba(31,45,74,0.4);
    transition: background .15s;
    position: relative;
}
.notif-page-item:last-child { border-bottom: none; }
.notif-page-item:hover { background: rgba(255,255,255,0.02); }
.notif-page-item.unread { background: rgba(59,130,246,0.04); }
.notif-page-item.unread::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; background: var(--accent);
}

.notif-avatar {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(99,102,241,0.15));
    border: 1px solid rgba(59,130,246,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
}
.notif-title  { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
.notif-detail { font-size: 13px; color: var(--text-secondary); margin-bottom: 6px; }
.notif-meta   { font-size: 11.5px; color: var(--text-muted); display: flex; align-items: center; gap: 12px; }
.notif-actions { display: flex; gap: 6px; margin-left: auto; align-self: flex-start; flex-shrink: 0; }

.date-separator {
    padding: 8px 20px;
    background: rgba(255,255,255,0.02);
    border-bottom: 1px solid rgba(31,45,74,0.3);
    font-size: 11px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .7px;
}
</style>
@endpush

@section('content')
{{-- ── Header & Actions ────────────── --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <span class="card-title">
            <i class="fas fa-bell" style="color:var(--amber);margin-right:6px"></i>
            Semua Notifikasi
            @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
            @if($unread > 0)
                <span class="badge" style="background:rgba(244,63,94,0.1);color:var(--rose);border:1px solid rgba(244,63,94,0.2);margin-left:6px">
                    {{ $unread }} belum terbaca
                </span>
            @endif
        </span>
        @if($unread > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">
                <i class="fas fa-check-double"></i> Tandai Semua Terbaca
            </button>
        </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <p>Tidak ada notifikasi saat ini</p>
            <p style="font-size:12px;margin-top:6px;color:#4b6074">Notifikasi akan muncul ketika ada tamu yang ingin menemui Anda</p>
        </div>
    @else
        @php
            $grouped = $notifications->groupBy(fn($n) => $n->created_at->isToday()
                ? 'Hari Ini'
                : ($n->created_at->isYesterday() ? 'Kemarin' : $n->created_at->format('d M Y')));
        @endphp

        @foreach($grouped as $dateLabel => $items)
        <div class="date-separator">{{ $dateLabel }}</div>
        @foreach($items as $notif)
        @php $data = $notif->data; $isUnread = is_null($notif->read_at); @endphp
        <div class="notif-page-item {{ $isUnread ? 'unread' : '' }}">
            <div class="notif-avatar">👤</div>
            <div style="flex:1;min-width:0">
                <div class="notif-title">
                    {{ $data['visitor_name'] }}
                    @if($isUnread)
                        <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--accent);margin-left:6px;vertical-align:middle"></span>
                    @endif
                </div>
                <div class="notif-detail">
                    <strong>Instansi:</strong> {{ $data['institution'] ?? '-' }} &bull;
                    <strong>Keperluan:</strong> {{ Str::limit($data['purpose'], 80) }}
                </div>
                <div class="notif-meta">
                    <span><i class="fas fa-clock" style="margin-right:4px"></i>{{ $notif->created_at->diffForHumans() }}</span>
                    <span><i class="fas fa-sign-in-alt" style="margin-right:4px"></i>Check-in pukul {{ $data['check_in_display'] ?? '-' }} WIB</span>
                    @if(!$isUnread)
                        <span style="color:#10b981"><i class="fas fa-check" style="margin-right:4px"></i>Sudah dibaca</span>
                    @endif
                </div>
            </div>
            <div class="notif-actions">
                {{-- Lihat Detail Tamu --}}
                <a href="{{ route('visitors.show', $data['visitor_id']) }}"
                   class="btn btn-outline btn-sm btn-icon" title="Lihat Detail Tamu">
                    <i class="fas fa-eye"></i>
                </a>
                {{-- Tandai Baca (jika belum dibaca) --}}
                @if($isUnread)
                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm btn-icon" title="Tandai Terbaca">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                @endif
                {{-- Hapus --}}
                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus"
                            data-confirm="Hapus notifikasi ini?">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        @endforeach

        {{-- Pagination --}}
        @if($notifications->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">{{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} dari {{ $notifications->total() }}</div>
            <div class="pagination">
                @if($notifications->onFirstPage())
                    <span class="disabled"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $notifications->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
                @endif
                @foreach($notifications->getUrlRange(max(1,$notifications->currentPage()-2), min($notifications->lastPage(),$notifications->currentPage()+2)) as $page => $url)
                    @if($page == $notifications->currentPage())<span class="active">{{ $page }}</span>
                    @else<a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                @if($notifications->hasMorePages())
                    <a href="{{ $notifications->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="disabled"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

{{-- ── Info Panel ───────────────────── --}}
<div class="card">
    <div class="card-body" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
        <div style="font-size:26px">💡</div>
        <div>
            <div style="font-size:13.5px;font-weight:600;margin-bottom:4px">Cara Kerja Notifikasi</div>
            <div style="font-size:13px;color:var(--text-secondary)">
                Notifikasi dikirim secara otomatis ketika tamu melakukan check-in via Kiosk dan memilih Anda sebagai orang yang dituju.
                Anda dapat melihat detail tamu dan melakukan checkout dari halaman ini.
            </div>
        </div>
    </div>
</div>
@endsection
