<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Super Admin ──────────────────────────────────────────
        User::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@smkn2jakarta.sch.id',
            'password'  => Hash::make('admin123'),
            'role'      => 'superadmin',
            'position'  => 'Administrator Sistem',
            'phone'     => '081234567890',
            'is_active' => true,
        ]);

        // ── Resepsionis ──────────────────────────────────────────
        User::create([
            'name'      => 'Siti Resepsionis',
            'email'     => 'resepsionis@smkn2jakarta.sch.id',
            'password'  => Hash::make('resep123'),
            'role'      => 'receptionist',
            'position'  => 'Resepsionis / Satpam',
            'phone'     => '081234567891',
            'is_active' => true,
        ]);

        // ── Staf / Guru ──────────────────────────────────────────
        $staffList = [
            ['name' => 'Budi Santoso, S.Pd',   'email' => 'budi@smkn2jakarta.sch.id',    'position' => 'Guru Teknik Informatika'],
            ['name' => 'Dewi Rahayu, M.Pd',    'email' => 'dewi@smkn2jakarta.sch.id',    'position' => 'Guru Bahasa Indonesia'],
            ['name' => 'Ahmad Fauzi, S.Kom',   'email' => 'ahmad@smkn2jakarta.sch.id',   'position' => 'Kepala Jurusan TKJ'],
            ['name' => 'Sri Wahyuni, S.E',     'email' => 'sri@smkn2jakarta.sch.id',     'position' => 'Guru Akuntansi'],
            ['name' => 'Hendra Gunawan, S.T',  'email' => 'hendra@smkn2jakarta.sch.id',  'position' => 'Guru Teknik Mesin'],
            ['name' => 'Rina Kusumawati, S.Pd','email' => 'rina@smkn2jakarta.sch.id',    'position' => 'Wali Kelas XII TKJ 1'],
            ['name' => 'Kepala Sekolah',        'email' => 'kepsek@smkn2jakarta.sch.id',  'position' => 'Kepala Sekolah'],
            ['name' => 'Wakasek Kurikulum',     'email' => 'wakasek@smkn2jakarta.sch.id', 'position' => 'Wakil Kepala Sekolah Kurikulum'],
        ];

        foreach ($staffList as $staff) {
            User::create([
                'name'      => $staff['name'],
                'email'     => $staff['email'],
                'password'  => Hash::make('staff123'),
                'role'      => 'staff',
                'position'  => $staff['position'],
                'phone'     => null,
                'is_active' => true,
            ]);
        }
    }
}
