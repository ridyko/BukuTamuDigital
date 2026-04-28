<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Visitor;
use App\Exports\VisitorsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitor::with('host')->latest('check_in_at');

        // Apply filters for the preview table
        if ($request->filled('date_from')) {
            $query->whereDate('check_in_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_in_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('host_id')) {
            $query->where('host_id', $request->host_id);
        }

        $visitors = $query->paginate(20)->withQueryString();
        $hosts = User::where('role', 'staff')->where('is_active', true)->orderBy('name')->get();

        return view('reports.index', compact('visitors', 'hosts'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'status', 'host_id']);
        $filename = 'Laporan_Kunjungan_' . now()->format('Ymd_His') . '.xlsx';
        
        return Excel::download(new VisitorsExport($filters), $filename);
    }

    public function exportPdf(Request $request)
    {
        $query = Visitor::with('host')->latest('check_in_at');

        if ($request->filled('date_from')) {
            $query->whereDate('check_in_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_in_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('host_id')) {
            $query->where('host_id', $request->host_id);
        }

        $visitors = $query->get();
        
        $data = [
            'visitors' => $visitors,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('d M Y H:i:s'),
            'user' => auth()->user()->name
        ];

        $pdf = Pdf::loadView('reports.pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download('Laporan_Kunjungan_' . now()->format('Ymd_His') . '.pdf');
    }
}
