<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /** Daftar semua kunjungan dengan filter */
    public function index(Request $request)
    {
        $query = Visitor::with('host')->latest('check_in_at');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('visit_code', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('check_in_at', $request->date);
        } else {
            // Default: tampilkan hari ini
            $query->whereDate('check_in_at', today());
        }

        // Filter tujuan
        if ($request->filled('host_id')) {
            $query->where('host_id', $request->host_id);
        }

        $visitors = $query->paginate(15)->withQueryString();
        $hosts    = User::where('role', 'staff')->where('is_active', true)->orderBy('name')->get();

        return view('visitors.index', compact('visitors', 'hosts'));
    }

    /** Detail kunjungan */
    public function show(Visitor $visitor)
    {
        $visitor->load(['host', 'checkedOutBy', 'activityLogs.performer']);
        return view('visitors.show', compact('visitor'));
    }

    /** Form checkout manual oleh Receptionist */
    public function checkoutForm(Visitor $visitor)
    {
        if (!$visitor->isActive()) {
            return redirect()->route('visitors.show', $visitor)
                ->with('warning', 'Tamu ini sudah checkout.');
        }
        return view('visitors.checkout', compact('visitor'));
    }

    /** Proses checkout manual oleh Receptionist */
    public function checkoutProcess(Request $request, Visitor $visitor)
    {
        if (!$visitor->isActive()) {
            return redirect()->route('visitors.index')
                ->with('warning', 'Tamu ini sudah checkout.');
        }

        $visitor->update([
            'check_out_at'    => now(),
            'checkout_method' => 'receptionist',
            'checkout_by'     => auth()->id(),
            'status'          => 'checked_out',
            'notes'           => $request->notes,
        ]);

        ActivityLog::record(
            visitorId:   $visitor->id,
            action:      'check_out',
            performedBy: auth()->id(),
            note:        'Checkout manual oleh resepsionis: ' . auth()->user()->name .
                         ($request->notes ? ' | Catatan: ' . $request->notes : ''),
        );

        return redirect()->route('visitors.index')
            ->with('success', "Tamu {$visitor->name} berhasil di-checkout.");
    }

    /** Riwayat semua kunjungan (tanpa filter tanggal) */
    public function history(Request $request)
    {
        $query = Visitor::with('host')->latest('check_in_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('visit_code', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('check_in_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('check_in_at', '<=', $request->date_to);

        $visitors = $query->paginate(20)->withQueryString();
        $hosts    = User::where('role', 'staff')->where('is_active', true)->orderBy('name')->get();

        return view('visitors.history', compact('visitors', 'hosts'));
    }

    // Resource methods tidak dipakai (check-in via kiosk)
    public function create()  { abort(404); }
    public function store(Request $request)  { abort(404); }
    public function edit(Visitor $visitor)  { abort(404); }
    public function update(Request $request, Visitor $visitor)  { abort(404); }
    public function destroy(Visitor $visitor)
    {
        $visitor->activityLogs()->delete();
        $visitor->delete();
        return redirect()->route('visitors.index')
            ->with('success', 'Data kunjungan berhasil dihapus.');
    }

    /** ── Halaman Tamu Aktif (khusus Receptionist) ─── */
    public function active()
    {
        $activeVisitors = Visitor::active()
            ->with('host')
            ->oldest('check_in_at')
            ->get();

        // Statistik checkout hari ini
        $checkoutStats = [
            'self'         => Visitor::whereDate('check_in_at', today())->where('checkout_method', 'self')->count(),
            'receptionist' => Visitor::whereDate('check_in_at', today())->where('checkout_method', 'receptionist')->count(),
            'auto'         => Visitor::whereDate('check_in_at', today())->where('checkout_method', 'auto')->count(),
        ];

        return view('visitors.active', compact('activeVisitors', 'checkoutStats'));
    }

    /** ── Quick Checkout (1 klik dari tabel, tanpa form) ─── */
    public function quickCheckout(Request $request, Visitor $visitor)
    {
        if (!$visitor->isActive()) {
            return back()->with('warning', 'Tamu ini sudah checkout.');
        }

        $visitor->update([
            'check_out_at'    => now(),
            'checkout_method' => 'receptionist',
            'checkout_by'     => auth()->id(),
            'status'          => 'checked_out',
            'notes'           => 'Quick-checkout dari panel Tamu Aktif oleh ' . auth()->user()->name,
        ]);

        ActivityLog::record(
            visitorId:   $visitor->id,
            action:      'check_out',
            performedBy: auth()->id(),
            note:        'Quick-checkout dari panel Tamu Aktif oleh ' . auth()->user()->name,
        );

        return back()->with('success', "✅ {$visitor->name} berhasil di-checkout.");
    }

    /** ── Trigger Auto-Checkout Manual (Superadmin) ─── */
    public function triggerAutoCheckout(Request $request)
    {
        $activeVisitors = Visitor::active()->get();

        if ($activeVisitors->isEmpty()) {
            return back()->with('warning', 'Tidak ada tamu aktif yang perlu di-checkout.');
        }

        $count = 0;
        foreach ($activeVisitors as $visitor) {
            $visitor->update([
                'check_out_at'    => now(),
                'checkout_method' => 'auto',
                'checkout_by'     => null,
                'status'          => 'checked_out',
                'notes'           => trim(($visitor->notes ?? '') . "\n[Manual auto-checkout oleh admin: " . auth()->user()->name . "]"),
            ]);
            ActivityLog::record(
                visitorId:   $visitor->id,
                action:      'auto_checkout',
                performedBy: auth()->id(),
                note:        'Manual auto-checkout dijalankan oleh superadmin: ' . auth()->user()->name,
            );
            $count++;
        }

        return back()->with('success', "✅ {$count} tamu berhasil di-auto-checkout oleh sistem.");
    }
}
