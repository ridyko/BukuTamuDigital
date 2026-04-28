<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Seeder;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('role', 'staff')->get();
        $receptionist = User::where('role', 'receptionist')->first();

        $sampleVisitors = [
            // ── Tamu Aktif Hari Ini (belum checkout) ───────────
            [
                'name'        => 'Ahmad Fauzan',
                'institution' => 'Dinas Pendidikan DKI Jakarta',
                'purpose'     => 'Koordinasi program PKL siswa semester 5',
                'phone'       => '08112233441',
                'check_in_at' => now()->subMinutes(45),
                'host'        => $staff->first(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Siti Rahayu',
                'institution' => 'PT Teknologi Maju Bersama',
                'purpose'     => 'Mengantar berkas lamaran kerja untuk alumni',
                'phone'       => '08198765432',
                'check_in_at' => now()->subMinutes(20),
                'host'        => $staff->skip(1)->first(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Budi Hartono',
                'institution' => 'Orang Tua Murid',
                'purpose'     => 'Konsultasi nilai rapor anak kelas XII TKJ 1',
                'phone'       => '08134567890',
                'check_in_at' => now()->subHours(2)->subMinutes(10),
                'host'        => $staff->skip(5)->first(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Dewi Lestari',
                'institution' => 'Komite Sekolah',
                'purpose'     => 'Rapat komite sekolah mengenai pembangunan gedung baru',
                'phone'       => null,
                'check_in_at' => now()->subHours(3)->subMinutes(30),
                'host'        => $staff->skip(6)->first(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Randi Pratama',
                'institution' => 'PT Indo Sejahtera',
                'purpose'     => 'Presentasi program magang & rekrutmen siswa',
                'phone'       => '08175432109',
                'check_in_at' => now()->subMinutes(8),
                'host'        => $staff->skip(2)->first(),
                'status'      => 'active',
            ],

            // ── Sudah Checkout - Self (Mandiri) ─────────────────
            [
                'name'           => 'Yuni Kartika',
                'institution'    => 'Alumni SMKN 2 Jakarta',
                'purpose'        => 'Legalisir ijazah dan transkrip nilai',
                'phone'          => '08123456789',
                'check_in_at'    => now()->subHours(4),
                'check_out_at'   => now()->subHours(3)->subMinutes(20),
                'checkout_method'=> 'self',
                'status'         => 'checked_out',
                'host'           => $staff->skip(3)->first(),
            ],
            [
                'name'           => 'Hendra Gunawan',
                'institution'    => 'Lembaga Sertifikasi Profesi',
                'purpose'        => 'Verifikasi pelaksanaan uji kompetensi siswa',
                'phone'          => null,
                'check_in_at'    => now()->subHours(5),
                'check_out_at'   => now()->subHours(4)->subMinutes(15),
                'checkout_method'=> 'self',
                'status'         => 'checked_out',
                'host'           => $staff->skip(4)->first(),
            ],

            // ── Sudah Checkout - Receptionist ────────────────────
            [
                'name'           => 'Pak Supri Wartana',
                'institution'    => 'Vendor ATK & Perlengkapan Kantor',
                'purpose'        => 'Antar pesanan kertas & alat tulis',
                'phone'          => '08119988776',
                'check_in_at'    => now()->subHours(6),
                'check_out_at'   => now()->subHours(5)->subMinutes(30),
                'checkout_method'=> 'receptionist',
                'checkout_by_id' => $receptionist?->id,
                'status'         => 'checked_out',
                'host'           => null,
                'department'     => 'Tata Usaha',
            ],

            // ── Auto-Checkout (kemarin, simulasi) ────────────────
            [
                'name'           => 'Ir. Wahyu Santoso',
                'institution'    => 'Konsultan Pendidikan',
                'purpose'        => 'Audit mutu internal sekolah',
                'phone'          => null,
                'check_in_at'    => now()->subDay()->setHour(14)->setMinute(30),
                'check_out_at'   => now()->subDay()->endOfDay()->setHour(0)->setMinute(0),
                'checkout_method'=> 'auto',
                'status'         => 'checked_out',
                'host'           => $staff->skip(6)->first(),
                'notes'          => '[Auto-checkout: Melewati batas waktu 24:00]',
            ],
            [
                'name'           => 'Nuri Andriani, S.Pd',
                'institution'    => 'Pengawas Sekolah Dikmen',
                'purpose'        => 'Supervisi akademik dan observasi kelas',
                'phone'          => '08156789012',
                'check_in_at'    => now()->subDays(2)->setHour(9)->setMinute(0),
                'check_out_at'   => now()->subDays(2)->setHour(15)->setMinute(30),
                'checkout_method'=> 'self',
                'status'         => 'checked_out',
                'host'           => $staff->skip(7)->first(),
            ],
        ];

        foreach ($sampleVisitors as $data) {
            $visitCode = Visitor::generateVisitCode();

            $visitor = Visitor::create([
                'visit_code'      => $visitCode,
                'name'            => $data['name'],
                'institution'     => $data['institution'] ?? null,
                'purpose'         => $data['purpose'],
                'phone'           => $data['phone'] ?? null,
                'host_id'         => $data['host']?->id ?? null,
                'department'      => $data['department'] ?? null,
                'check_in_at'     => $data['check_in_at'],
                'check_out_at'    => $data['check_out_at'] ?? null,
                'checkout_method' => $data['checkout_method'] ?? null,
                'checkout_by'     => $data['checkout_by_id'] ?? null,
                'status'          => $data['status'],
                'notes'           => $data['notes'] ?? null,
            ]);

            // Log check-in
            ActivityLog::create([
                'visitor_id'   => $visitor->id,
                'action'       => 'check_in',
                'performed_by' => null,
                'note'         => 'Check-in via Kiosk (data uji coba)',
                'created_at'   => $data['check_in_at'],
            ]);

            // Log checkout jika sudah checkout
            if ($visitor->status === 'checked_out' && $visitor->check_out_at) {
                $action = $data['checkout_method'] === 'auto' ? 'auto_checkout' : 'check_out';
                ActivityLog::create([
                    'visitor_id'   => $visitor->id,
                    'action'       => $action,
                    'performed_by' => $data['checkout_by_id'] ?? null,
                    'note'         => match($data['checkout_method']) {
                        'self'         => 'Self-checkout mandiri via Kiosk',
                        'receptionist' => 'Checkout manual oleh resepsionis',
                        'auto'         => 'Auto-checkout otomatis sistem jam 00:00',
                        default        => null,
                    },
                    'created_at'   => $data['check_out_at'],
                ]);
            }
        }
    }
}
