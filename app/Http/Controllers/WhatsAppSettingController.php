<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class WhatsAppSettingController extends Controller
{
    /**
     * Halaman Pengaturan WhatsApp
     */
    public function index()
    {
        $gatewayUrl = Setting::get('whatsapp_gateway_url', 'http://localhost:3000/send-message');
        $status     = $this->getGatewayStatus();
        $logs       = $this->getGatewayLogs();

        return view('settings.whatsapp', compact('gatewayUrl', 'status', 'logs'));
    }

    /**
     * Simpan Pengaturan Gateway
     */
    public function update(Request $request)
    {
        $request->validate([
            'gateway_url' => 'required|url',
        ]);

        Setting::set('whatsapp_gateway_url', $request->gateway_url);

        return back()->with('success', 'Konfigurasi WhatsApp berhasil disimpan.');
    }

    /**
     * Jalankan Gateway (Node.js)
     */
    public function start()
    {
        if ($this->getGatewayStatus()) {
            return back()->with('info', 'Gateway sudah berjalan.');
        }

        $path = base_path('wa-gateway');
        $logPath = base_path('storage/logs/wa-gateway.log');
        $qrPath = storage_path('app/public/wa_qr.txt');

        // Bersihkan data lama
        File::put($logPath, "--- Memulai Gateway (" . now()->format('H:i:s') . ") ---\n");
        if (File::exists($qrPath)) File::delete($qrPath);

        // Menjalankan di background dengan redirect stdin dari /dev/null untuk mencegah error ioctl
        // Kita juga set HOME ke folder gateway agar Puppeteer tidak mencoba akses /Users/mac
        $nodePath = '/usr/local/bin/node';
        $cmd = "cd {$path} && export HOME={$path} && {$nodePath} server.js < /dev/null > {$logPath} 2>&1 & echo $!";
        $pid = shell_exec($cmd);

        if ($pid) {
            Setting::set('whatsapp_gateway_pid', trim($pid));
            return back()->with('success', 'Gateway sedang dinyalakan. Tunggu beberapa saat lalu scan QR Code di tab Log.');
        }

        return back()->with('error', 'Gagal menyalakan Gateway. Pastikan Node.js terinstal.');
    }

    /**
     * Matikan Gateway
     */
    public function stop()
    {
        $pid = Setting::get('whatsapp_gateway_pid');
        
        if ($pid) {
            shell_exec("kill -9 {$pid}");
            Setting::set('whatsapp_gateway_pid', null);
            return back()->with('success', 'Gateway berhasil dimatikan.');
        }

        // Jika PID hilang, coba matikan berdasarkan nama proses
        shell_exec("pkill -f 'node server.js'");
        return back()->with('success', 'Proses Gateway dihentikan.');
    }

    /**
     * Ambil Status Gateway (Apakah proses node sedang berjalan)
     */
    private function getGatewayStatus()
    {
        $output = shell_exec("ps aux | grep '[n]ode server.js'");
        return !empty($output);
    }

    /**
     * Ambil log terbaru (untuk melihat QR Code)
     */
    private function getGatewayLogs()
    {
        $logPath = base_path('storage/logs/wa-gateway.log');
        if (File::exists($logPath)) {
            return File::get($logPath);
        }
        return 'Belum ada log aktivitas.';
    }

    /**
     * Reset Sesi WhatsApp (Hapus Folder Auth)
     */
    public function reset()
    {
        $this->stop();
        
        $path = base_path('wa-gateway/auth_session');
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        return back()->with('success', 'Sesi berhasil direset. Silakan jalankan kembali gateway.');
    }

    /**
     * Tampilkan QR Code secara langsung
     */
    public function qr()
    {
        $qrPath = storage_path('app/public/wa_qr.txt');
        if (!File::exists($qrPath)) {
            return "QR Code belum tersedia. Silakan jalankan gateway terlebih dahulu.";
        }

        $qrContent = File::get($qrPath);
        $url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrContent);

        return "<html><body style='background:#0f172a; display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh; color:white; font-family:sans-serif;'>
                    <h2 style='margin-bottom:20px'>Scan QR Code WhatsApp</h2>
                    <img src='{$url}' style='border:15px solid white; border-radius:15px; box-shadow:0 0 50px rgba(0,0,0,0.5)'>
                    <p style='margin-top:30px; color:#94a3b8'>Gunakan WhatsApp > Perangkat Tertaut > Tautkan Perangkat</p>
                    <button onclick='location.reload()' style='margin-top:20px; padding:10px 20px; border-radius:8px; border:none; background:#3b82f6; color:white; cursor:pointer'>Refresh QR</button>
                </body></html>";
    }
}
