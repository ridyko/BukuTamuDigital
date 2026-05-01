<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

// ── INSTALLER / ACTIVATION ────────────────────────────────────
Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install/activate', [InstallController::class, 'activate'])->name('install.activate');

/*
|--------------------------------------------------------------------------
| Kiosk Routes (Publik — tidak perlu login)
|--------------------------------------------------------------------------
*/
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/',                        [KioskController::class, 'welcome'])->name('welcome');
    Route::get('/checkin',                 [KioskController::class, 'showCheckin'])->name('checkin');
    Route::post('/checkin',                [KioskController::class, 'processCheckin'])->name('checkin.post');
    Route::get('/checkin/success/{code}',  [KioskController::class, 'success'])->name('success');
    Route::get('/checkout',                [KioskController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout',               [KioskController::class, 'processCheckout'])->name('checkout.post');
    Route::get('/checkout/done/{code}',    [KioskController::class, 'checkoutDone'])->name('checkout.done');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Redirect root ke dashboard
Route::get('/', fn () => redirect()->route('dashboard'));

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Notifications (semua user yang login) ────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                         [NotificationController::class, 'index'])->name('index');
        Route::get('/count',                    [NotificationController::class, 'count'])->name('count');
        Route::post('/{id}/read',               [NotificationController::class, 'markRead'])->name('read');
        Route::post('/mark-all-read',           [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::delete('/{id}',                  [NotificationController::class, 'destroy'])->name('destroy');
    });

    // ── Visitors (Receptionist + Superadmin) ────────────────────
    Route::middleware('role:superadmin,receptionist')->group(function () {
        Route::get('/visitors',         [VisitorController::class, 'index'])->name('visitors.index');
        Route::get('/visitors/active',  [VisitorController::class, 'active'])->name('visitors.active');
        Route::get('/visitors/history', [VisitorController::class, 'history'])->name('visitors.history');
        Route::get('/visitors/{visitor}', [VisitorController::class, 'show'])->name('visitors.show');
        Route::get('/visitors/{visitor}/checkout',  [VisitorController::class, 'checkoutForm'])->name('visitors.checkout');
        Route::post('/visitors/{visitor}/checkout', [VisitorController::class, 'checkoutProcess'])->name('visitors.checkout.process');
        // Quick checkout tanpa halaman konfirmasi
        Route::post('/visitors/{visitor}/quick-checkout', [VisitorController::class, 'quickCheckout'])->name('visitors.quick-checkout');

        // ── Reports ────────────────────
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });

    // Hapus visitor hanya superadmin
    Route::delete('/visitors/{visitor}', [VisitorController::class, 'destroy'])
        ->name('visitors.destroy')
        ->middleware('role:superadmin');

    // ── User Management (Superadmin only) ───────────────────────
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('users', UserController::class);
        
        // General Settings
        Route::get('/settings/general', [\App\Http\Controllers\GeneralSettingController::class, 'index'])->name('settings.general');
        Route::post('/settings/general', [\App\Http\Controllers\GeneralSettingController::class, 'update'])->name('settings.general.update');
        Route::post('/settings/clear-cache', [\App\Http\Controllers\GeneralSettingController::class, 'clearCache'])->name('settings.clear-cache');

        // ── WhatsApp Settings ───────────────────────────────────
        Route::get('/settings/whatsapp', [\App\Http\Controllers\WhatsAppSettingController::class, 'index'])->name('settings.whatsapp.index');
        Route::post('/settings/whatsapp/update', [\App\Http\Controllers\WhatsAppSettingController::class, 'update'])->name('settings.whatsapp.update');
        Route::post('/settings/whatsapp/start', [\App\Http\Controllers\WhatsAppSettingController::class, 'start'])->name('settings.whatsapp.start');
        Route::post('/settings/whatsapp/stop', [\App\Http\Controllers\WhatsAppSettingController::class, 'stop'])->name('settings.whatsapp.stop');
        Route::post('/settings/whatsapp/reset', [\App\Http\Controllers\WhatsAppSettingController::class, 'reset'])->name('settings.whatsapp.reset');
        Route::get('/settings/whatsapp/qr', [\App\Http\Controllers\WhatsAppSettingController::class, 'qr'])->name('settings.whatsapp.qr');

        // Trigger auto-checkout manual (untuk testing / emergency)
        Route::post('/admin/auto-checkout', [VisitorController::class, 'triggerAutoCheckout'])->name('admin.auto-checkout');
    });

});
