<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visit_code')->unique()->comment('Kode unik kunjungan');

            // Data Tamu
            $table->string('name')->comment('Nama tamu');
            $table->string('id_number')->nullable()->comment('NIK / No. Identitas');
            $table->string('institution')->nullable()->comment('Asal instansi/perusahaan');
            $table->string('phone')->nullable()->comment('No. HP tamu');
            $table->text('purpose')->comment('Keperluan kunjungan');
            $table->string('badge_number')->nullable()->comment('Nomor badge/kartu tamu');

            // Tujuan Kunjungan
            $table->foreignId('host_id')->nullable()->constrained('users')->nullOnDelete()->comment('Staf yang dituju');
            $table->string('department')->nullable()->comment('Departemen/jurusan yang dituju');

            // Media
            $table->string('photo')->nullable()->comment('Foto tamu');
            $table->text('signature')->nullable()->comment('Tanda tangan digital (base64)');
            $table->string('qr_code')->nullable()->comment('Path file QR code');

            // Check-in / Check-out
            $table->timestamp('check_in_at')->nullable()->comment('Waktu check-in');
            $table->timestamp('check_out_at')->nullable()->comment('Waktu check-out');
            $table->enum('checkout_method', ['self', 'receptionist', 'auto'])->nullable()
                ->comment('Metode: self=mandiri, receptionist=petugas, auto=otomatis jam 00.00');
            $table->foreignId('checkout_by')->nullable()->constrained('users')->nullOnDelete()
                ->comment('User yang melakukan checkout (receptionist)');

            // Status
            $table->enum('status', ['active', 'checked_out'])->default('active')
                ->comment('active=masih di dalam, checked_out=sudah keluar');
            $table->text('notes')->nullable()->comment('Catatan tambahan');

            $table->timestamps();

            // Indexes untuk pencarian cepat
            $table->index('status');
            $table->index('check_in_at');
            $table->index('visit_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
