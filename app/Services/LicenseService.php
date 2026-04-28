<?php

namespace App\Services;

class LicenseService
{
    /**
     * Validasi License Key
     * Format contoh: BUKUTAMU-SMKN2-2026-XXXXX
     */
    public static function validate($key)
    {
        if (empty($key)) return false;

        // Logika sederhana: Harus diawali BUKUTAMU-SMKN2 dan panjang tertentu
        // Di aplikasi nyata, ini bisa dicek ke server lisensi Anda.
        $validPrefix = 'BUKUTAMU-SMKN2';
        
        if (str_starts_with($key, $validPrefix) && strlen($key) > 25) {
            return true;
        }

        // Contoh key valid untuk dicoba: BUKUTAMU-SMKN2-2026-DEVELOPER-LICENSE-KEY
        return false;
    }
}
