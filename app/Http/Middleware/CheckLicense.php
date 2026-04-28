<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jangan proteksi route installer itu sendiri
        if ($request->is('install*')) {
            return $next($request);
        }

        $licenseKey = env('LICENSE_KEY');

        if (!LicenseService::validate($licenseKey)) {
            return redirect()->route('install.index')
                ->with('error', 'Lisensi tidak valid atau belum dipasang. Silakan masukkan Secret Key.');
        }

        return $next($request);
    }
}
