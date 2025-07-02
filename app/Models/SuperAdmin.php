<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
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
        'permissions',
        'can_create_tenants',
        'can_delete_tenants',
        'can_manage_licenses',
        'can_view_all_data',
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
        'permissions' => 'array',
        'can_create_tenants' => 'boolean',
        'can_delete_tenants' => 'boolean',
        'can_manage_licenses' => 'boolean',
        'can_view_all_data' => 'boolean',
        'preferences' => 'array',
        'login_attempts' => 'integer',
    ];

    /**
     * العلاقة مع سجلات التدقيق
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AdminAuditLog::class);
    }

    /**
     * التحقق من كون المستخدم سوبر أدمن
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * التحقق من كون المستخدم أدمن
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * التحقق من حالة النشاط
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
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
    public function lockAccount(int $minutes = 60): void
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
        
        // قفل الحساب بعد 3 محاولات فاشلة (أكثر صرامة للسوبر أدمن)
        if ($this->login_attempts >= 3) {
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
    }

    /**
     * التحقق من وجود صلاحية معينة
     */
    public function hasPermission(string $permission): bool
    {
        // السوبر أدمن له جميع الصلاحيات
        if ($this->isSuperAdmin()) {
            return true;
        }

        // التحقق من الصلاحيات المخصصة
        $customPermissions = $this->permissions ?? [];
        if (in_array($permission, $customPermissions)) {
            return true;
        }

        // التحقق من الصلاحيات المحددة مسبقاً
        switch ($permission) {
            case 'create_tenants':
                return $this->can_create_tenants;
            case 'delete_tenants':
                return $this->can_delete_tenants;
            case 'manage_licenses':
                return $this->can_manage_licenses;
            case 'view_all_data':
                return $this->can_view_all_data;
            default:
                return false;
        }
    }

    /**
     * إضافة صلاحية مخصصة
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * إزالة صلاحية مخصصة
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
    }

    /**
     * الحصول على جميع الصلاحيات
     */
    public function getAllPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return [
                'create_tenants',
                'delete_tenants',
                'manage_licenses',
                'view_all_data',
                'manage_super_admins',
                'system_settings',
                'audit_logs',
            ];
        }

        $permissions = [];
        
        if ($this->can_create_tenants) $permissions[] = 'create_tenants';
        if ($this->can_delete_tenants) $permissions[] = 'delete_tenants';
        if ($this->can_manage_licenses) $permissions[] = 'manage_licenses';
        if ($this->can_view_all_data) $permissions[] = 'view_all_data';

        return array_merge($permissions, $this->permissions ?? []);
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

    public function scopeSuperAdmins($query)
    {
        return $query->where('role', 'super_admin');
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['super_admin', 'admin']);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($admin) {
            if (empty($admin->locale)) {
                $admin->locale = 'ar';
            }
            if (empty($admin->timezone)) {
                $admin->timezone = 'UTC';
            }
        });
    }

    /**
     * التحقق من ما إذا كان الحساب مقفل
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until > now();
    }


}
