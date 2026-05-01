<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WhatsApp menggunakan API Gateway Milik Sendiri (Self-Hosted)
     * Menggunakan server Node.js lokal di port 3000
     */
    public static function send($to, $message)
    {
        // Ambil URL dari database, jika kosong gunakan default lokal
        $gatewayUrl = \App\Models\Setting::get('whatsapp_gateway_url', 'http://localhost:3000/send-message');
        
        if (empty($to)) {
            Log::warning("WhatsApp tidak terkirim: Nomor tujuan kosong.");
            return false;
        }

        // Format nomor (pastikan diawali 62)
        $to = self::formatNumber($to);

        try {
            // Mengirim request ke server Node.js lokal
            $response = Http::timeout(10)->post($gatewayUrl, [
                'phone' => $to,
                'message' => $message,
            ]);

            Log::info("WhatsApp Gateway Response: " . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WhatsApp Gateway Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor HP agar standar 62...
     */
    private static function formatNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '08')) {
            $number = '62' . substr($number, 1);
        } elseif (str_starts_with($number, '8')) {
            $number = '62' . $number;
        }
        return $number;
    }
}
