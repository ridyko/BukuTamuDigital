<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Visitor;
use App\Notifications\VisitorArrived;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class KioskController extends Controller
{
    /** Halaman sambutan Kiosk */
    public function welcome()
    {
        $activeCount = Visitor::active()->count();
        $todayCount  = Visitor::today()->count();
        return view('kiosk.welcome', compact('activeCount', 'todayCount'));
    }

    /** Form Check-In */
    public function showCheckin()
    {
        $hosts = User::where('role', 'staff')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('kiosk.checkin', compact('hosts'));
    }

    /** Proses Check-In */
    public function processCheckin(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'purpose'    => ['required', 'string', 'max:500'],
            'id_number'  => ['nullable', 'string', 'max:30'],
            'institution'=> ['nullable', 'string', 'max:100'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'host_id'    => ['nullable', 'exists:users,id'],
            'department' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required'    => 'Nama wajib diisi.',
            'purpose.required' => 'Keperluan wajib diisi.',
        ]);

        $visitCode = Visitor::generateVisitCode();

        // ── Simpan foto (base64 dari webcam) ──────────────
        $photoPath = null;
        if ($request->filled('photo_data')) {
            $raw = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_data);
            $binary = base64_decode($raw);
            if ($binary !== false && strlen($binary) > 100) {
                $photoPath = 'photos/' . $visitCode . '.jpg';
                Storage::disk('public')->put($photoPath, $binary);
            }
        }

        // ── Simpan tanda tangan (base64) ──────────────────
        $signatureData = null;
        if ($request->filled('signature_data')) {
            $raw = preg_replace('/^data:image\/\w+;base64,/', '', $request->signature_data);
            if (strlen($raw) > 50) {
                $signatureData = $request->signature_data; // simpan full base64 untuk ditampilkan
            }
        }

        // ── Generate QR Code ──────────────────────────────
        $qrPath = null;
        try {
            $qrBinary = QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($visitCode);
            $qrPath = 'qrcodes/' . $visitCode . '.png';
            Storage::disk('public')->put($qrPath, $qrBinary);
        } catch (\Exception $e) {
            // QR gagal generate, lanjutkan tanpa QR
        }

        // ── Simpan ke database ────────────────────────────
        $visitor = Visitor::create([
            'visit_code'   => $visitCode,
            'name'         => trim($request->name),
            'id_number'    => $request->id_number,
            'institution'  => $request->institution,
            'phone'        => $request->phone,
            'purpose'      => trim($request->purpose),
            'host_id'      => $request->host_id ?: null,
            'department'   => $request->department,
            'photo'        => $photoPath,
            'signature'    => $signatureData,
            'qr_code'      => $qrPath,
            'check_in_at'  => now(),
            'status'       => 'active',
        ]);

        // ── Catat log ─────────────────────────────────────
        ActivityLog::record(
            visitorId:   $visitor->id,
            action:      'check_in',
            performedBy: null,
            note:        'Check-in mandiri via Kiosk oleh ' . $visitor->name,
            ip:          $request->ip(),
        );

        // ── Kirim notifikasi ke staf yang dituju ─────────────
        if ($visitor->host_id) {
            $host = User::find($visitor->host_id);
            if ($host) {
                // 1. Kirim Email & In-App Notification
                $host->notify(new VisitorArrived($visitor));

                // 2. Kirim WhatsApp (jika nomor HP tersedia)
                if ($host->phone) {
                    $appUrl = config('app.url');
                    $detailUrl = $appUrl . '/visitors/' . $visitor->id;

                    $message = "🔔 *ADA TAMU BARU*\n\n"
                             . "Halo Bapak/Ibu *" . $host->name . "*,\n"
                             . "Anda mendapatkan tamu baru di " . config('app.name') . ":\n\n"
                             . "👤 *Nama:* " . $visitor->name . "\n"
                             . "🏢 *Instansi:* " . ($visitor->institution ?? '-') . "\n"
                             . "📝 *Keperluan:* " . $visitor->purpose . "\n"
                             . "⏰ *Check-in:* " . $visitor->check_in_at->format('H:i') . " WIB\n\n"
                             . "Silakan klik link di bawah untuk detail tamu:\n"
                             . $detailUrl . "\n\n"
                             . "_Mohon segera ditindaklanjuti._";

                    WhatsAppService::send($host->phone, $message);
                }
            }
        }

        return redirect()->route('kiosk.success', $visitCode);
    }

    /** Halaman Sukses Check-In dengan QR Code */
    public function success(string $code)
    {
        $visitor = Visitor::where('visit_code', $code)->firstOrFail();
        return view('kiosk.success', compact('visitor'));
    }

    /** Form Self-Checkout */
    public function showCheckout()
    {
        return view('kiosk.checkout');
    }

    /** Proses Self-Checkout */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'visit_code' => ['required', 'string'],
        ], [
            'visit_code.required' => 'Kode kunjungan wajib diisi.',
        ]);

        $visitor = Visitor::where('visit_code', strtoupper(trim($request->visit_code)))->first();

        if (!$visitor) {
            return back()->withErrors(['visit_code' => 'Kode kunjungan tidak ditemukan.'])->withInput();
        }

        if (!$visitor->isActive()) {
            return back()->withErrors(['visit_code' => 'Anda sudah melakukan checkout sebelumnya.'])->withInput();
        }

        $visitor->update([
            'check_out_at'    => now(),
            'checkout_method' => 'self',
            'checkout_by'     => null,
            'status'          => 'checked_out',
        ]);

        ActivityLog::record(
            visitorId:   $visitor->id,
            action:      'check_out',
            performedBy: null,
            note:        'Self-checkout mandiri via Kiosk oleh ' . $visitor->name,
            ip:          $request->ip(),
        );

        return redirect()->route('kiosk.checkout.done', $visitor->visit_code);
    }

    /** Halaman Sukses Checkout */
    public function checkoutDone(string $code)
    {
        $visitor = Visitor::where('visit_code', $code)->firstOrFail();
        return view('kiosk.checkout_done', compact('visitor'));
    }
}
