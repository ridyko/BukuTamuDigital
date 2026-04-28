@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')

@section('content')
<div style="max-width:640px;margin:0 auto">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-user-pen" style="color:#f59e0b;margin-right:6px"></i>Edit Pengguna: {{ $user->name }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <select name="role" class="form-control" {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                            <option value="superadmin"   {{ old('role',$user->role)=='superadmin' ? 'selected':'' }}>Super Admin</option>
                            <option value="receptionist" {{ old('role',$user->role)=='receptionist' ? 'selected':'' }}>Resepsionis</option>
                            <option value="staff"        {{ old('role',$user->role)=='staff' ? 'selected':'' }}>Staf / Guru</option>
                        </select>
                        @if($user->id == auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div style="font-size:11px;color:#4b6074;margin-top:4px"><i class="fas fa-info-circle"></i> Tidak dapat mengubah role akun sendiri</div>
                        @endif
                        @error('role')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password Baru <span style="color:#4b6074;font-weight:400">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jabatan / Posisi</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}" placeholder="Contoh: Guru Teknik Informatika">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="0812xxxxxxxx">
                    </div>
                </div>

                @if($user->id !== auth()->id())
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               style="accent-color:#10b981;width:16px;height:16px">
                        <span class="form-label" style="margin:0">Akun Aktif</span>
                    </label>
                </div>
                @endif

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
