<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────

    /** Kunjungan yang ditujukan ke user ini */
    public function visitsAsHost(): HasMany
    {
        return $this->hasMany(Visitor::class, 'host_id');
    }

    /** Checkout yang dilakukan oleh user ini */
    public function checkoutsPerformed(): HasMany
    {
        return $this->hasMany(Visitor::class, 'checkout_by');
    }

    /** Log aktivitas yang dilakukan user */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'performed_by');
    }

    // ─── Role Helpers ─────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'superadmin'   => 'Super Admin',
            'receptionist' => 'Resepsionis',
            'staff'        => 'Staf / Guru',
            default        => ucfirst($this->role),
        };
    }
}
