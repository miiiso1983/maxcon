<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class TenantUser extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'status',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'locale',
        'timezone',
        'preferences',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'two_factor_confirmed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'preferences' => 'array',
        'login_attempts' => 'integer',
    ];

    /**
     * العلاقة مع المستأجر
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * التحقق من كون المستخدم مدير نظام
     */
    public function isSystemManager(): bool
    {
        return $this->role === 'system_manager';
    }

    /**
     * التحقق من كون المستخدم مدير
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['system_manager', 'admin']);
    }

    /**
     * التحقق من كون المستخدم مدير أو موظف
     */
    public function isManager(): bool
    {
        return in_array($this->role, ['system_manager', 'admin', 'manager']);
    }

    /**
     * التحقق من حالة النشاط
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->tenant->isLicenseValid() &&
               (!$this->locked_until || $this->locked_until < now());
    }

    /**
     * التحقق من تفعيل المصادقة الثنائية
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && 
               !empty($this->two_factor_secret) && 
               $this->two_factor_confirmed_at !== null;
    }

    /**
     * قفل الحساب مؤقتاً
     */
    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
            'login_attempts' => 0,
        ]);
    }

    /**
     * إلغاء قفل الحساب
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'login_attempts' => 0,
        ]);
    }

    /**
     * زيادة محاولات تسجيل الدخول
     */
    public function incrementLoginAttempts(): void
    {
        $this->increment('login_attempts');
        
        // قفل الحساب بعد 5 محاولات فاشلة
        if ($this->login_attempts >= 5) {
            $this->lockAccount();
        }
    }

    /**
     * إعادة تعيين محاولات تسجيل الدخول
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * تسجيل نجاح تسجيل الدخول
     */
    public function recordSuccessfulLogin(string $ipAddress): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
            'login_attempts' => 0,
            'locked_until' => null,
        ]);

        // تحديث إحصائيات المستأجر
        $this->tenant->increment('total_logins');
        $this->tenant->update(['last_activity_at' => now()]);
    }

    /**
     * الحصول على الصلاحيات بناءً على الدور
     */
    public function getPermissions(): array
    {
        $permissions = [];

        switch ($this->role) {
            case 'system_manager':
                $permissions = [
                    'manage_users',
                    'manage_settings',
                    'view_reports',
                    'manage_data',
                    'full_access',
                ];
                break;
            case 'admin':
                $permissions = [
                    'manage_users',
                    'view_reports',
                    'manage_data',
                ];
                break;
            case 'manager':
                $permissions = [
                    'view_reports',
                    'manage_data',
                ];
                break;
            case 'employee':
                $permissions = [
                    'view_data',
                ];
                break;
        }

        return $permissions;
    }

    /**
     * التحقق من وجود صلاحية معينة
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('locked_until')
                          ->orWhere('locked_until', '<', now());
                    });
    }

    public function scopeSystemManagers($query)
    {
        return $query->where('role', 'system_manager');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->locale)) {
                $user->locale = 'ar';
            }
            if (empty($user->timezone)) {
                $user->timezone = 'UTC';
            }
        });
    }
}
