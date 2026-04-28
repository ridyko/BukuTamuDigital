@extends('layouts.kiosk')
@section('title', 'Check-In Tamu')

@push('styles')
<style>
.step-panel { display: none; }
.step-panel.active { display: block; animation: fadeSlide .3s ease; }
.step-body { padding: 28px 32px 32px; }
.k-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 600px) { .k-form-grid { grid-template-columns: 1fr; } }

/* Camera */
.camera-wrap {
    position: relative; width: 100%; aspect-ratio: 4/3; max-height: 240px;
    background: #0a0f1e; border: 1.5px solid rgba(255,255,255,0.08);
    border-radius: 14px; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
}
.camera-wrap video, .camera-wrap canvas {
    width: 100%; height: 100%; object-fit: cover; border-radius: 14px;
}
.camera-wrap .cam-placeholder {
    text-align: center; color: #475569; font-size: 13px;
}
.camera-wrap .cam-placeholder i { font-size: 40px; display: block; margin-bottom: 8px; }
.cam-controls { display: flex; gap: 10px; margin-top: 10px; }

/* Signature */
#signature-canvas {
    width: 100%; height: 160px;
    background: rgba(255,255,255,0.03);
    border: 1.5px solid rgba(255,255,255,0.1);
    border-radius: 14px; cursor: crosshair;
    touch-action: none;
}
.sig-controls { display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px; }

/* Nav buttons */
.k-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; gap: 12px; }
</style>
@endpush

@section('content')
<div class="kiosk-card animate-in" style="max-width:680px">
    {{-- Progress Steps --}}
    <div class="k-steps">
        <div class="k-step active" id="s-label-1">
            <div class="k-step-num">1</div>
            <span>Data Diri</span>
        </div>
        <div class="k-step-line" id="s-line-1"></div>
        <div class="k-step" id="s-label-2">
            <div class="k-step-num">2</div>
            <span>Keperluan</span>
        </div>
        <div class="k-step-line" id="s-line-2"></div>
        <div class="k-step" id="s-label-3">
            <div class="k-step-num">3</div>
            <span>Foto & TTD</span>
        </div>
    </div>

    <form id="checkin-form" action="{{ route('kiosk.checkin.post') }}" method="POST">
        @csrf

        {{-- ── STEP 1: Data Diri ───────────────────────── --}}
        <div class="step-panel active step-body" id="step-1">
            <div class="k-form-grid">
                <div class="k-form-group" style="grid-column:1/-1">
                    <label class="k-label">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" id="name" class="k-input" value="{{ old('name') }}"
                           placeholder="Masukkan nama lengkap Anda" autocomplete="off">
                    @error('name')<div class="k-error">{{ $message }}</div>@enderror
                </div>
                <div class="k-form-group">
                    <label class="k-label">NIK / No. Identitas</label>
                    <input type="text" name="id_number" class="k-input" value="{{ old('id_number') }}"
                           placeholder="Opsional" inputmode="numeric">
                </div>
                <div class="k-form-group">
                    <label class="k-label">No. HP / WhatsApp</label>
                    <input type="tel" name="phone" class="k-input" value="{{ old('phone') }}"
                           placeholder="Opsional" inputmode="tel">
                </div>
                <div class="k-form-group" style="grid-column:1/-1">
                    <label class="k-label">Asal Instansi / Perusahaan</label>
                    <input type="text" name="institution" class="k-input" value="{{ old('institution') }}"
                           placeholder="Contoh: PT Maju Bersama, Dinas Pendidikan, dll.">
                </div>
            </div>
            <div class="k-nav">
                <a href="{{ route('kiosk.welcome') }}" class="k-btn k-btn-outline">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="k-btn k-btn-primary" onclick="goStep(2)">
                    Lanjut <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ── STEP 2: Keperluan & Tujuan ─────────────── --}}
        <div class="step-panel step-body" id="step-2">
            <div class="k-form-group">
                <label class="k-label">Keperluan Kunjungan <span class="req">*</span></label>
                <textarea name="purpose" id="purpose" class="k-input" rows="3"
                          placeholder="Jelaskan tujuan kunjungan Anda...">{{ old('purpose') }}</textarea>
                @error('purpose')<div class="k-error">{{ $message }}</div>@enderror
            </div>

            <div class="k-form-group">
                <label class="k-label">Ditujukan Kepada (Pilih Staf/Guru)</label>
                <select name="host_id" class="k-input" id="host_select"
                        onchange="document.getElementById('dept-row').style.display = this.value ? 'none' : 'block'">
                    <option value="">-- Pilih staf yang dituju --</option>
                    @foreach($hosts as $host)
                    <option value="{{ $host->id }}" {{ old('host_id')==$host->id?'selected':'' }}>
                        {{ $host->name }} — {{ $host->position }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="k-form-group" id="dept-row" style="{{ old('host_id') ? 'display:none' : '' }}">
                <label class="k-label">Atau ketik Departemen / Jurusan</label>
                <input type="text" name="department" class="k-input" value="{{ old('department') }}"
                       placeholder="Contoh: Tata Usaha, Kesiswaan, ...">
            </div>

            <div class="k-nav">
                <button type="button" class="k-btn k-btn-outline" onclick="goStep(1)">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
                <button type="button" class="k-btn k-btn-primary" onclick="goStep(3)">
                    Lanjut <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ── STEP 3: Foto & Tanda Tangan ────────────── --}}
        <div class="step-panel step-body" id="step-3">
            <div class="k-form-grid">
                {{-- Foto Webcam --}}
                <div>
                    <label class="k-label">📷 Foto Tamu <span style="color:#64748b;font-weight:400">(Opsional)</span></label>
                    <div class="camera-wrap" id="camera-wrap">
                        <div class="cam-placeholder" id="cam-placeholder">
                            <i class="fas fa-camera"></i>
                            <div>Kamera belum aktif</div>
                        </div>
                        <video id="webcam" autoplay muted playsinline style="display:none"></video>
                        <canvas id="photo-canvas" style="display:none"></canvas>
                    </div>
                    <div class="cam-controls">
                        <button type="button" class="k-btn k-btn-outline k-btn-sm" onclick="startCamera()" id="btn-start-cam" style="font-size:13px;padding:8px 14px">
                            <i class="fas fa-video"></i> Aktifkan
                        </button>
                        <button type="button" class="k-btn k-btn-primary" onclick="takePhoto()" id="btn-take-photo" style="display:none;font-size:13px;padding:8px 14px">
                            <i class="fas fa-camera"></i> Ambil Foto
                        </button>
                        <button type="button" class="k-btn k-btn-outline" onclick="retakePhoto()" id="btn-retake" style="display:none;font-size:13px;padding:8px 14px">
                            <i class="fas fa-rotate-right"></i> Ulangi
                        </button>
                    </div>
                    <input type="hidden" name="photo_data" id="photo_data">
                </div>

                {{-- Tanda Tangan --}}
                <div>
                    <label class="k-label">✍️ Tanda Tangan <span style="color:#64748b;font-weight:400">(Opsional)</span></label>
                    <canvas id="signature-canvas"></canvas>
                    <div class="sig-controls">
                        <button type="button" class="k-btn k-btn-outline" onclick="clearSignature()" style="font-size:12px;padding:7px 14px">
                            <i class="fas fa-eraser"></i> Hapus
                        </button>
                    </div>
                    <input type="hidden" name="signature_data" id="signature_data">
                </div>
            </div>

            {{-- Preview ringkasan --}}
            <div style="margin-top:20px;padding:16px;background:rgba(59,130,246,0.05);border:1px solid rgba(59,130,246,0.15);border-radius:12px;font-size:13px">
                <div style="font-weight:600;color:#60a5fa;margin-bottom:8px"><i class="fas fa-list-check"></i> Ringkasan Kunjungan</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;color:#94a3b8">
                    <div>Nama: <span style="color:#f1f5f9;font-weight:500" id="preview-name">-</span></div>
                    <div>Instansi: <span style="color:#f1f5f9;font-weight:500" id="preview-inst">-</span></div>
                    <div style="grid-column:1/-1">Keperluan: <span style="color:#f1f5f9;font-weight:500" id="preview-purpose">-</span></div>
                </div>
            </div>

            <div class="k-nav">
                <button type="button" class="k-btn k-btn-outline" onclick="goStep(2)">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
                <button type="submit" class="k-btn k-btn-success" id="btn-submit" onclick="saveSignature()">
                    <i class="fas fa-check-circle"></i> Selesaikan Check-In
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
/* ── Step Navigation ─────────────────────────────── */
let currentStep = 1;
function goStep(n) {
    // Validasi step 1
    if (n === 2) {
        const name = document.getElementById('name').value.trim();
        if (!name) { document.getElementById('name').focus(); showError('name', 'Nama wajib diisi.'); return; }
        clearError('name');
        // Update preview
        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-inst').textContent = document.querySelector('[name="institution"]').value || '-';
    }
    // Validasi step 2
    if (n === 3) {
        const purpose = document.getElementById('purpose').value.trim();
        if (!purpose) { document.getElementById('purpose').focus(); showError('purpose', 'Keperluan wajib diisi.'); return; }
        clearError('purpose');
        document.getElementById('preview-purpose').textContent = purpose.substring(0, 60) + (purpose.length > 60 ? '...' : '');
        
        // Trigger resize canvas saat step 3 aktif (agar width/height tidak 0)
        setTimeout(resizeSigCanvas, 50);
    }

    document.getElementById(`step-${currentStep}`).classList.remove('active');
    currentStep = n;
    document.getElementById(`step-${currentStep}`).classList.add('active');

    // Update step indicators
    for (let i = 1; i <= 3; i++) {
        const lbl  = document.getElementById(`s-label-${i}`);
        const line = document.getElementById(`s-line-${i}`);
        lbl.className = 'k-step' + (i === n ? ' active' : (i < n ? ' done' : ''));
        if (line) line.className = 'k-step-line' + (i < n ? ' done' : '');
    }
    document.querySelector('.kiosk-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function showError(field, msg) {
    clearError(field);
    const el = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
    if (el) { el.classList.add('k-input-error'); const d = document.createElement('div'); d.className='k-error'; d.id=`err-${field}`; d.textContent=msg; el.parentNode.appendChild(d); }
}
function clearError(field) {
    const el = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
    if (el) el.classList.remove('k-input-error');
    const err = document.getElementById(`err-${field}`);
    if (err) err.remove();
}

/* ── Webcam ──────────────────────────────────────── */
let stream = null;
async function startCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { width:640, height:480, facingMode:'user' } });
        const video = document.getElementById('webcam');
        video.srcObject = stream;
        video.style.display = 'block';
        document.getElementById('cam-placeholder').style.display = 'none';
        document.getElementById('photo-canvas').style.display = 'none';
        document.getElementById('btn-start-cam').style.display = 'none';
        document.getElementById('btn-take-photo').style.display = 'inline-flex';
        document.getElementById('btn-retake').style.display = 'none';
    } catch(e) {
        alert('Kamera tidak dapat diakses: ' + e.message);
    }
}
function takePhoto() {
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('photo-canvas');
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    canvas.style.display = 'block';
    video.style.display = 'none';
    document.getElementById('photo_data').value = canvas.toDataURL('image/jpeg', 0.85);
    document.getElementById('btn-take-photo').style.display = 'none';
    document.getElementById('btn-retake').style.display = 'inline-flex';
    if (stream) stream.getTracks().forEach(t => t.stop());
}
function retakePhoto() {
    document.getElementById('photo_data').value = '';
    document.getElementById('photo-canvas').style.display = 'none';
    document.getElementById('btn-start-cam').style.display = 'inline-flex';
    document.getElementById('btn-retake').style.display = 'none';
    document.getElementById('webcam').style.display = 'none';
    document.getElementById('cam-placeholder').style.display = 'flex';
}

/* ── Signature Pad ───────────────────────────────── */
const sigCanvas = document.getElementById('signature-canvas');
const sigCtx    = sigCanvas.getContext('2d');
let sigDrawing  = false;
let lastX = 0, lastY = 0;

function getPos(e) {
    const r = sigCanvas.getBoundingClientRect();
    const src = e.touches ? e.touches[0] : e;
    return { x: src.clientX - r.left, y: src.clientY - r.top };
}
function sigStart(e) {
    e.preventDefault(); sigDrawing = true;
    const p = getPos(e); lastX = p.x; lastY = p.y;
}
function sigMove(e) {
    if (!sigDrawing) return; e.preventDefault();
    const p = getPos(e);
    sigCtx.beginPath();
    sigCtx.moveTo(lastX, lastY);
    sigCtx.lineTo(p.x, p.y);
    sigCtx.strokeStyle = '#ffffff';
    sigCtx.lineWidth = 3;
    sigCtx.lineCap = 'round';
    sigCtx.lineJoin = 'round';
    sigCtx.stroke();
    lastX = p.x; lastY = p.y;
}
function sigEnd() { sigDrawing = false; }
function clearSignature() {
    sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
    document.getElementById('signature_data').value = '';
}
function saveSignature() {
    if (sigCanvas.toDataURL() !== document.createElement('canvas').toDataURL()) {
        document.getElementById('signature_data').value = sigCanvas.toDataURL('image/png');
    }
}

// Resize canvas to match display size
function resizeSigCanvas() {
    const rect = sigCanvas.getBoundingClientRect();
    sigCanvas.width  = rect.width;
    sigCanvas.height = rect.height || 160;
}
window.addEventListener('load', resizeSigCanvas);

sigCanvas.addEventListener('mousedown',  sigStart);
sigCanvas.addEventListener('mousemove',  sigMove);
sigCanvas.addEventListener('mouseup',    sigEnd);
sigCanvas.addEventListener('mouseleave', sigEnd);
sigCanvas.addEventListener('touchstart', sigStart, { passive: false });
sigCanvas.addEventListener('touchmove',  sigMove,  { passive: false });
sigCanvas.addEventListener('touchend',   sigEnd);

// If there were validation errors, show the correct step
@if($errors->any())
    @if($errors->has('name') || $errors->has('id_number') || $errors->has('phone') || $errors->has('institution'))
        goStep(1);
    @elseif($errors->has('purpose') || $errors->has('host_id'))
        goStep(2);
    @endif
@endif

// Prevent double submit
document.getElementById('checkin-form').addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
});
</script>
@endpush
