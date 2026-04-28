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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->cascadeOnDelete();
            $table->enum('action', ['check_in', 'check_out', 'auto_checkout', 'update'])
                ->comment('Jenis aksi yang dilakukan');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete()
                ->comment('User yang melakukan aksi (null = sistem/auto)');
            $table->text('note')->nullable()->comment('Keterangan detail');
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['visitor_id', 'action']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
