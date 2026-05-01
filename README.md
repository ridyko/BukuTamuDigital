# 📔 Buku Tamu Digital Professional (White-Label Ready)

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-Commercial-green.svg)](#)

Buku Tamu Digital adalah solusi manajemen pengunjung modern yang dirancang untuk Sekolah, Instansi Pemerintah, maupun Perusahaan Swasta. Dilengkapi dengan fitur **White-Labeling** penuh, aplikasi ini siap digunakan dengan identitas brand Anda sendiri hanya dalam hitungan detik.

---

## ✨ Fitur Unggulan

### 🏢 1. Full White-Label System
Ubah seluruh identitas aplikasi langsung dari Dashboard Admin tanpa menyentuh kode program:
- Ubah Nama Aplikasi & Instansi.
- Upload Logo & Favicon kustom.
- Atur Alamat & Footer sesuai kebutuhan.
- Branding otomatis sinkron ke: **Halaman Login, Dashboard Admin, Interface Kiosk, dan Laporan PDF.**

### 📱 2. Interface Kiosk Mandiri
Halaman khusus pengunjung yang sangat intuitif untuk proses check-in mandiri. Dilengkapi dengan:
- Pencarian Data Host/Tujuan otomatis.
- Generasi QR Code Kunjungan unik.
- Interface Responsif & Modern.

### 🔔 3. Integrasi WhatsApp (Optional)
Notifikasi otomatis ke HP petugas atau tamu (membutuhkan WA Gateway) untuk memberikan kesan profesional dan modern.

### 📄 4. Laporan Cerdas & Profesional
- Ekspor laporan ke format **Excel** dan **PDF**.
- Laporan PDF dilengkapi dengan Kop Surat otomatis (Logo & Alamat Instansi).
- Filter laporan berdasarkan tanggal, status, dan tujuan.

---

## 👥 Manajemen Role & Hak Akses

Aplikasi ini memiliki sistem otorisasi berbasis Role yang ketat untuk menjaga keamanan data:

### 👑 1. Super Admin
- **Hak Akses**: Seluruh fitur aplikasi.
- **Aktivitas**:
  - Manajemen Pengaturan Umum & Branding (White-Label).
  - Manajemen Akun Pengguna (Tambah/Edit/Hapus User).
  - Monitoring Dashboard Global & Statistik Kunjungan.
  - Akses penuh ke seluruh Laporan & Log Sistem.

### 👮 2. Receptionist / Security
- **Hak Akses**: Operasional harian buku tamu.
- **Aktivitas**:
  - Monitoring tamu yang sedang berada di lokasi (In-House).
  - Membantu pendaftaran tamu (Manual Check-in).
  - Melakukan proses Check-out tamu secara cepat.
  - Cetak label/kartu tamu (jika diperlukan).

### 👨‍💼 3. Staff / Host
- **Hak Akses**: Dashboard pribadi.
- **Aktivitas**:
  - Melihat daftar tamu yang datang menemui dirinya.
  - Menerima notifikasi kedatangan tamu secara real-time.
  - Melihat riwayat kunjungan pribadi.

### 🚶 4. Guest / Kiosk (Public Interface)
- **Hak Akses**: Registrasi mandiri.
- **Aktivitas**:
  - Mengisi formulir kunjungan digital.
  - Memilih tujuan (Staff) secara otomatis.
  - Mendapatkan QR Code Kunjungan sebagai bukti check-in.

---

## 🖥️ Kebutuhan Sistem

- **PHP 8.2** atau lebih tinggi.
- **MySQL 8.0** atau MariaDB.
- **Composer** (Dependency Manager).
- Ekstensi PHP: `gd`, `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`.

---

## 🚀 Panduan Instalasi

1. **Clone & Install Dependency**
   ```bash
   git clone https://github.com/username/repository.git
   cd bukutamu
   composer install
   ```

2. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan atur koneksi database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Migrasi Database**
   ```bash
   php artisan migrate --seed
   ```

4. **Setup Storage Link** (Penting untuk Logo & Foto)
   ```bash
   php artisan storage:link
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```

---

## 🛠️ Panduan Penggunaan & Branding

### Akses Admin
- **URL**: `yoursite.com/login`
- **Default Login**: Cek file `database/seeders/UserSeeder.php` untuk akun admin pertama.

### Mengubah Identitas (White-Label)
1. Masuk ke Dashboard Admin.
2. Pilih menu **Pengaturan Umum**.
3. Isi Nama Aplikasi, Nama Instansi, Alamat, dan Footer.
4. Upload Logo & Favicon Anda.
5. Klik **Simpan Perubahan**.

### Sinkronisasi Tampilan
Jika Logo atau Nama tidak langsung berubah di PDF atau Kiosk, gunakan fitur **"Hapus Cache"** yang tersedia di halaman Pengaturan Umum. Fitur ini akan menyegarkan tampilan tanpa menghapus data rahasia (Secret Key).

---

## 🛡️ Keamanan & Pemeliharaan

- **Secret Key**: Pastikan `LICENSE_KEY` di file `.env` terjaga kerahasiaannya.
- **Mode Produksi**: Pastikan `APP_DEBUG=false` pada file `.env` saat aplikasi sudah online.
- **Backup**: Lakukan backup database secara berkala melalui menu yang tersedia atau database manager Anda.

---

## 📝 Lisensi
Produk ini adalah produk komersial. Dilarang mendistribusikan ulang kode sumber tanpa izin tertulis dari pengembang.

---
**Developed with ❤️ for Professional Management.**
