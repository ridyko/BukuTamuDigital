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
        
        // Re-encode logo menggunakan GD agar "bersih" dan ringan untuk dompdf
        $logoBase64 = null;
        $logoPath = \App\Models\Setting::get('app_logo');
        if ($logoPath) {
            $logoFull = storage_path('app/public/' . $logoPath);
            if (file_exists($logoFull)) {
                try {
                    // Load gambar asli
                    $img = null;
                    $ext = strtolower(pathinfo($logoFull, PATHINFO_EXTENSION));
                    if ($ext == 'png') $img = @imagecreatefrompng($logoFull);
                    elseif ($ext == 'jpg' || $ext == 'jpeg') $img = @imagecreatefromjpeg($logoFull);
                    
                    if ($img) {
                        // Resize sedikit agar tidak terlalu berat (max height 100px)
                        $width = imagesx($img);
                        $height = imagesy($img);
                        $newHeight = 100;
                        $newWidth = ($width / $height) * $newHeight;
                        
                        $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
                        // Jaga transparansi PNG
                        imagealphablending($tmpImg, false);
                        imagesavealpha($tmpImg, true);
                        imagecopyresampled($tmpImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        
                        // Output ke buffer sebagai PNG bersih
                        ob_start();
                        imagepng($tmpImg);
                        $logoData = ob_get_clean();
                        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                        
                        imagedestroy($img);
                        imagedestroy($tmpImg);
                    }
                } catch (\Exception $e) {
                    // Fallback ke base64 mentah jika GD gagal
                    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoFull));
                }
            }
        }
        
        $data = [
            'visitors' => $visitors,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('d M Y H:i:s'),
            'user' => auth()->user()->name,
            'logoBase64' => $logoBase64
        ];

        $pdf = Pdf::loadView('reports.pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'tempDir' => storage_path('app/public'),
                'logOutputFile' => storage_path('logs/dompdf.log.html'),
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);
        return $pdf->download('Laporan_Kunjungan_' . now()->format('Ymd_His') . '.pdf');
    }
}
