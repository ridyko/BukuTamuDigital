<?php

namespace App\Http\Controllers;

use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallController extends Controller
{
    /**
     * Halaman Utama Installer / Aktivasi
     */
    public function index()
    {
        // Jika sudah ada license yang valid, arahkan ke dashboard
        if (LicenseService::validate(env('LICENSE_KEY'))) {
            return redirect()->route('dashboard');
        }

        return view('install.index');
    }

    /**
     * Proses Aktivasi Secret Key
     */
    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
        ], [
            'license_key.required' => 'Secret Key wajib diisi untuk mengaktifkan aplikasi.',
        ]);

        if (!LicenseService::validate($request->license_key)) {
            return back()->with('error', 'Secret Key tidak valid. Silakan hubungi pengembang untuk mendapatkan izin.');
        }

        try {
            $this->updateEnv('LICENSE_KEY', $request->license_key);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan kunci. Pastikan file .env dapat ditulis (Permission 666).');
        }

        return redirect()->route('dashboard')->with('success', 'Aplikasi berhasil diaktifkan!');
    }

    /**
     * Helper untuk update file .env
     */
    private function updateEnv($key, $value)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            $content = File::get($path);
            
            // Jika key sudah ada, ganti nilainya
            if (str_contains($content, $key . '=')) {
                $content = preg_replace("/{$key}=.*/", "{$key}={$value}", $content);
            } else {
                // Jika belum ada, tambahkan di baris baru
                $content .= "\n{$key}={$value}";
            }

            File::put($path, $content);
            
            // Clear config cache agar perubahan terbaca
            Artisan::call('config:clear');
        }
    }
}
