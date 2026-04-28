<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCheckoutVisitors extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'visitors:auto-checkout
                            {--dry-run : Tampilkan daftar tamu yang akan di-checkout tanpa benar-benar mengeksekusi}';

    /**
     * The console command description.
     */
    protected $description = 'Auto checkout semua tamu yang masih aktif saat jam 00:00 (midnight)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🕛 Auto-Checkout Buku Tamu Digital');
        $this->info('Waktu eksekusi: ' . now()->format('d/m/Y H:i:s'));
        $this->line(str_repeat('─', 50));

        // Ambil semua tamu yang masih aktif
        $activeVisitors = Visitor::active()
            ->with('host')
            ->orderBy('check_in_at')
            ->get();

        if ($activeVisitors->isEmpty()) {
            $this->info('✅ Tidak ada tamu aktif yang perlu di-checkout.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  Ditemukan {$activeVisitors->count()} tamu masih aktif:");

        // Tampilkan tabel tamu yang akan di-checkout
        $this->table(
            ['#', 'Kode', 'Nama Tamu', 'Tujuan', 'Check-in'],
            $activeVisitors->map(fn ($v, $i) => [
                $i + 1,
                $v->visit_code,
                $v->name,
                $v->host?->name ?? $v->department ?? '-',
                $v->check_in_at?->format('d/m/Y H:i'),
            ])->toArray()
        );

        if ($isDryRun) {
            $this->warn('🔍 [DRY RUN] Tidak ada perubahan yang disimpan.');
            return self::SUCCESS;
        }

        // Proses auto-checkout
        $processedCount = 0;
        $checkoutTime   = now();

        foreach ($activeVisitors as $visitor) {
            $visitor->update([
                'check_out_at'    => $checkoutTime,
                'checkout_method' => 'auto',
                'checkout_by'     => null,
                'status'          => 'checked_out',
                'notes'           => trim(($visitor->notes ?? '') . "\n[Auto-checkout: Melewati batas waktu 24:00]"),
            ]);

            // Catat di activity log
            ActivityLog::record(
                visitorId:   $visitor->id,
                action:      'auto_checkout',
                performedBy: null,
                note:        "Auto-checkout otomatis oleh sistem pada pukul 00:00 WIB. Durasi kunjungan: {$visitor->duration} menit.",
                ip:          '127.0.0.1'
            );

            $processedCount++;
        }

        // Log ke file Laravel
        Log::channel('daily')->info("Auto-checkout selesai: {$processedCount} tamu di-checkout pada " . $checkoutTime->format('d/m/Y H:i:s'));

        $this->info("✅ Berhasil auto-checkout {$processedCount} tamu.");
        $this->line(str_repeat('─', 50));

        return self::SUCCESS;
    }
}
