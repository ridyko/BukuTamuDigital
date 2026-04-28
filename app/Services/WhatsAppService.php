<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WhatsApp menggunakan API Gateway (Contoh: Fonnte)
     * Anda bisa mengganti logic ini sesuai provider yang Anda gunakan.
     */
    public static function send($to, $message)
    {
        // Token API (Dapatkan dari provider WA Gateway Anda)
        $token = env('WHATSAPP_TOKEN');
        
        if (empty($token) || empty($to)) {
            Log::warning("WhatsApp tidak terkirim: Token atau nomor tujuan kosong.");
            return false;
        }

        // Format nomor (pastikan diawali 62)
        $to = self::formatNumber($to);

        try {
            // Contoh menggunakan Fonnte API
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->withOptions([
                'verify' => false // Bypass SSL if needed for local XAMPP
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $to,
                'message' => $message,
                'delay' => '2',
                'countryCode' => '62', // Indonesia
            ]);

            Log::info("WhatsApp Fonnte Response: " . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WhatsApp Error: " . $e->getMessage());
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
