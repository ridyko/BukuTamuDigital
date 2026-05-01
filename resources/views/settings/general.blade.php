@extends('layouts.app')
@section('title', 'Pengaturan Umum')
@section('page-title', 'Pengaturan Umum')

@section('content')
<div class="fade-in">
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-building" style="margin-right: 8px; color: var(--accent)"></i> Identitas Aplikasi & Instansi</span>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('settings.general.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-row">
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" style="display:block; margin-bottom:8px">Nama Aplikasi</label>
                        <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? 'Buku Tamu Digital' }}" placeholder="Contoh: Buku Tamu Digital">
                        @error('app_name') <small class="text-rose">{{ $message }}</small> @enderror
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" style="display:block; margin-bottom:8px">Nama Instansi / Sekolah</label>
                        <input type="text" name="app_org" class="form-control" value="{{ $settings['app_org'] ?? 'SMKN 2 Jakarta' }}" placeholder="Contoh: SMKN 2 Jakarta">
                        @error('app_org') <small class="text-rose">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="form-label" style="display:block; margin-bottom:8px">Alamat Lengkap</label>
                    <textarea name="app_address" class="form-control" rows="3" placeholder="Alamat lengkap instansi...">{{ $settings['app_address'] ?? '' }}</textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="form-label" style="display:block; margin-bottom:8px">Teks Footer (Hak Cipta)</label>
                    <input type="text" name="app_footer" class="form-control" value="{{ $settings['app_footer'] ?? '© 2026 Buku Tamu Digital' }}" placeholder="Contoh: © 2026 Nama Perusahaan">
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border); margin: 24px 0;">

                <div class="form-row" style="gap: 30px;">
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" style="display:block; margin-bottom:8px">Logo Aplikasi (PNG/JPG)</label>
                        <div style="display:flex; align-items:center; gap:15px">
                            <div style="width: 80px; height: 80px; border: 1px solid var(--border); border-radius: 12px; display:flex; align-items:center; justify-content:center; background: var(--bg-mute); overflow:hidden">
                                @if(isset($settings['app_logo']))
                                    <img src="{{ asset('storage/'.$settings['app_logo']) }}" style="max-width:100%; max-height:100%; object-fit:contain">
                                @else
                                    <i class="fas fa-image" style="color: var(--text-muted); font-size: 24px"></i>
                                @endif
                            </div>
                            <input type="file" name="app_logo" class="form-control" style="font-size: 12px">
                        </div>
                        <small style="color: var(--text-muted); font-size: 11px; display:block; margin-top:5px">Ukuran maksimal 2MB. Gunakan background transparan untuk hasil terbaik.</small>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="form-label" style="display:block; margin-bottom:8px">Favicon (Ikon Tab Browser)</label>
                        <div style="display:flex; align-items:center; gap:15px">
                            <div style="width: 50px; height: 50px; border: 1px solid var(--border); border-radius: 8px; display:flex; align-items:center; justify-content:center; background: var(--bg-mute); overflow:hidden">
                                @if(isset($settings['app_favicon']))
                                    <img src="{{ asset('storage/'.$settings['app_favicon']) }}" style="width:32px; height:32px; object-fit:contain">
                                @else
                                    <i class="fas fa-globe" style="color: var(--text-muted); font-size: 20px"></i>
                                @endif
                            </div>
                            <input type="file" name="app_favicon" class="form-control" style="font-size: 12px">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px; display:flex; justify-content:flex-end">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 30px">
                        <i class="fas fa-save" style="margin-right: 8px"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
