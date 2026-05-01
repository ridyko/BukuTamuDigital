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

        // Menjalankan di background menggunakan nohup (Linux/Mac)
        $cmd = "cd {$path} && nohup node server.js > {$logPath} 2>&1 & echo $!";
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
}
