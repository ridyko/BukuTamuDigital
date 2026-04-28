@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 20px">
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <form method="GET" action="{{ route('users.index') }}" style="display:flex;gap:12px;flex:1;flex-wrap:wrap">
                <div style="flex:2;min-width:200px">
                    <div style="position:relative">
                        <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#4b6074;font-size:13px"></i>
                        <input type="text" name="search" class="form-control" style="padding-left:36px"
                               value="{{ request('search') }}" placeholder="Cari nama, email, jabatan...">
                    </div>
                </div>
                <div style="min-width:160px">
                    <select name="role" class="form-control">
                        <option value="">Semua Role</option>
                        <option value="superadmin"   {{ request('role')=='superadmin' ? 'selected':'' }}>Super Admin</option>
                        <option value="receptionist" {{ request('role')=='receptionist' ? 'selected':'' }}>Resepsionis</option>
                        <option value="staff"        {{ request('role')=='staff' ? 'selected':'' }}>Staf / Guru</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline"><i class="fas fa-filter"></i> Filter</button>
            </form>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pengguna
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Pengguna <span style="font-weight:400;color:#64748b;font-size:13px">({{ $users->total() }})</span></span>
    </div>

    @if($users->isEmpty())
        <div class="empty-state">
            <i class="fas fa-user-slash"></i>
            <p>Tidak ada pengguna ditemukan</p>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Jabatan</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:14px;flex-shrink:0">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;color:#f1f5f9">{{ $user->name }}</div>
                                <div style="font-size:11px;color:#4b6074">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->role == 'superadmin')
                            <span class="badge badge-role-superadmin">Super Admin</span>
                        @elseif($user->role == 'receptionist')
                            <span class="badge badge-role-receptionist">Resepsionis</span>
                        @else
                            <span class="badge badge-role-staff">Staf / Guru</span>
                        @endif
                    </td>
                    <td>{{ $user->position ?: '-' }}</td>
                    <td>{{ $user->phone ?: '-' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-out">Nonaktif</span>
                        @endif
                    </td>
                    <td style="font-size:12px;white-space:nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:5px">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon"
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Sudah nonaktif' }}"
                                        data-confirm="Nonaktifkan pengguna {{ $user->name }}?">
                                    <i class="fas fa-ban"></i>
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

    @if($users->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">{{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}</div>
        <div class="pagination">
            @if($users->onFirstPage())
                <span class="disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $users->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif
            @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
                @if($page == $users->currentPage())<span class="active">{{ $page }}</span>
                @else<a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
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
document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', e => {
        if (!confirm(btn.dataset.confirm)) e.preventDefault();
    });
});
</script>
@endpush
