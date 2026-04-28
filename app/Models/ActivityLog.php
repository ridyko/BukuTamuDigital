<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'visitor_id',
        'action',
        'performed_by',
        'note',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /** Label aksi */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'check_in'     => 'Check-In',
            'check_out'    => 'Check-Out (Mandiri)',
            'auto_checkout' => 'Auto Check-Out (00:00)',
            'update'       => 'Data Diperbarui',
            default        => ucfirst($this->action),
        };
    }

    /** Icon aksi */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'check_in'      => '🟢',
            'check_out'     => '🔵',
            'auto_checkout' => '🟡',
            'update'        => '✏️',
            default         => '⚪',
        };
    }

    // ─── Static Helper ───────────────────────────────────────────

    /** Catat log dengan mudah */
    public static function record(
        int $visitorId,
        string $action,
        ?int $performedBy = null,
        ?string $note = null,
        ?string $ip = null
    ): self {
        return self::create([
            'visitor_id'   => $visitorId,
            'action'       => $action,
            'performed_by' => $performedBy,
            'note'         => $note,
            'ip_address'   => $ip ?? request()->ip(),
            'created_at'   => now(),
        ]);
    }
}
