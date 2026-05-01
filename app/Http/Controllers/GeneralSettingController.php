<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getAll();
        return view('settings.general', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'     => 'required|string|max:100',
            'app_org'      => 'required|string|max:100',
            'app_address'  => 'nullable|string',
            'app_footer'   => 'nullable|string',
            'app_logo'     => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'app_favicon'  => 'nullable|image|mimes:png,ico,jpg|max:1024',
        ]);

        // Update Text Settings
        Setting::set('app_name', $request->app_name);
        Setting::set('app_org', $request->app_org);
        Setting::set('app_address', $request->app_address);
        Setting::set('app_footer', $request->app_footer);

        // Handle Logo Upload
        if ($request->hasFile('app_logo')) {
            // Hapus logo lama jika ada
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo) Storage::disk('public')->delete($oldLogo);

            $path = $request->file('app_logo')->store('branding', 'public');
            Setting::set('app_logo', $path);
        }

        // Handle Favicon Upload
        if ($request->hasFile('app_favicon')) {
            $oldFav = Setting::get('app_favicon');
            if ($oldFav) Storage::disk('public')->delete($oldFav);

            $path = $request->file('app_favicon')->store('branding', 'public');
            Setting::set('app_favicon', $path);
        }

        return back()->with('success', 'Pengaturan identitas berhasil diperbarui!');
    }

    public function clearCache()
    {
        try {
            // Hanya hapus cache tampilan dan konfigurasi agar branding sinkron
            // Tanpa menghapus cache aplikasi secara total (agar Secret Key aman)
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            
            return back()->with('success', 'Cache tampilan dan konfigurasi berhasil dibersihkan! Branding kini sudah sinkron dan Secret Key Anda tetap aman.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan cache: ' . $e->getMessage());
        }
    }
}
