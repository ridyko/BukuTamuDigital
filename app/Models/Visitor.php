<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Visitor extends Model
{
    protected $fillable = [
        'visit_code',
        'name',
        'id_number',
        'institution',
        'phone',
        'purpose',
        'badge_number',
        'host_id',
        'department',
        'photo',
        'signature',
        'qr_code',
        'check_in_at',
        'check_out_at',
        'checkout_method',
        'checkout_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'check_in_at'  => 'datetime',
        'check_out_at' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────

    /** Staf/guru yang menjadi tujuan kunjungan */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /** User yang melakukan checkout (receptionist) */
    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checkout_by');
    }

    /** Semua log aktivitas kunjungan ini */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /** Tamu yang masih di dalam (belum checkout) */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Tamu yang sudah checkout */
    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked_out');
    }

    /** Kunjungan hari ini */
    public function scopeToday($query)
    {
        return $query->whereDate('check_in_at', today());
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /** Generate kode kunjungan unik */
    public static function generateVisitCode(): string
    {
        do {
            $code = 'VIS-' . strtoupper(Str::random(8));
        } while (self::where('visit_code', $code)->exists());

        return $code;
    }

    /** Durasi kunjungan dalam menit */
    public function getDurationAttribute(): ?int
    {
        if (!$this->check_in_at) return null;
        $end = $this->check_out_at ?? now();
        return (int) $this->check_in_at->diffInMinutes($end);
    }

    /** Label metode checkout */
    public function getCheckoutMethodLabelAttribute(): string
    {
        return match ($this->checkout_method) {
            'self'         => 'Mandiri',
            'receptionist' => 'Oleh Resepsionis',
            'auto'         => 'Otomatis (00:00)',
            default        => '-',
        };
    }

    /** Apakah tamu masih aktif */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
