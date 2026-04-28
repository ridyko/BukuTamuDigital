@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')

@section('content')
<div style="max-width:640px;margin:0 auto">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-user-plus" style="color:#3b82f6;margin-right:6px"></i>Form Tambah Pengguna</span>
        </div>
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Pd" autofocus>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role / Peran <span class="required">*</span></label>
                        <select name="role" class="form-control">
                            <option value="">-- Pilih Role --</option>
                            <option value="superadmin"   {{ old('role')=='superadmin' ? 'selected':'' }}>Super Admin</option>
                            <option value="receptionist" {{ old('role')=='receptionist' ? 'selected':'' }}>Resepsionis</option>
                            <option value="staff"        {{ old('role')=='staff' ? 'selected':'' }}>Staf / Guru</option>
                        </select>
                        @error('role')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@smkn2jakarta.sch.id">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jabatan / Posisi</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position') }}" placeholder="Contoh: Guru Teknik Informatika">
                        @error('position')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Contoh: 0812xxxxxxxx">
                        @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengguna</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
