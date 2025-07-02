<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;


class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_code',
        'name',
        'domain',
        'subdomain',
        'database_name',
        'email',
        'phone',
        'address',
        'contact_person',
        'contact_email',
        'contact_phone',
        'license_type',
        'max_users',
        'max_storage_gb',
        'features',
        'license_start_date',
        'license_end_date',
        'is_trial',
        'trial_days',
        'current_users_count',
        'current_storage_gb',
        'last_activity_at',
        'total_logins',
        'status',
        'is_active',
        'settings',
        'notes',
        'monthly_fee',
        'currency',
        'billing_cycle',
        'next_billing_date',
    ];

    protected $casts = [
        'features' => 'array',
        'settings' => 'array',
        'license_start_date' => 'datetime',
        'license_end_date' => 'datetime',
        'next_billing_date' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_trial' => 'boolean',
        'is_active' => 'boolean',
        'monthly_fee' => 'decimal:2',
        'current_storage_gb' => 'decimal:2',
        'max_users' => 'integer',
        'max_storage_gb' => 'integer',
        'trial_days' => 'integer',
        'current_users_count' => 'integer',
        'total_logins' => 'integer',
    ];

    protected $dates = [
        'license_start_date',
        'license_end_date',
        'next_billing_date',
        'last_activity_at',
    ];

    /**
     * العلاقة مع مستخدمي المستأجر
     */
    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    /**
     * العلاقة مع المدير الرئيسي
     */
    public function systemManager(): HasOne
    {
        return $this->hasOne(TenantUser::class)->where('role', 'system_manager');
    }

    /**
     * العلاقة مع المستخدمين العاديين
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * العلاقة مع سجلات التدقيق
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AdminAuditLog::class);
    }

    /**
     * التحقق من صحة الترخيص
     */
    public function isLicenseValid(): bool
    {
        if (!$this->is_active || $this->status !== 'active') {
            return false;
        }

        return $this->license_end_date >= now();
    }

    /**
     * التحقق من انتهاء الترخيص قريباً
     */
    public function isLicenseExpiringSoon(int $days = 30): bool
    {
        return $this->license_end_date <= now()->addDays($days);
    }

    /**
     * التحقق من تجاوز حد المستخدمين
     */
    public function isUserLimitExceeded(): bool
    {
        return $this->current_users_count >= $this->max_users;
    }

    /**
     * التحقق من تجاوز حد التخزين
     */
    public function isStorageLimitExceeded(): bool
    {
        return $this->current_storage_gb >= $this->max_storage_gb;
    }

    /**
     * الحصول على الأيام المتبقية في الترخيص
     */
    public function getDaysUntilExpiry(): int
    {
        return max(0, now()->diffInDays($this->license_end_date, false));
    }

    /**
     * التحقق من وجود ميزة معينة
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * إضافة ميزة جديدة
     */
    public function addFeature(string $feature): void
    {
        $features = $this->features ?? [];
        if (!in_array($feature, $features)) {
            $features[] = $feature;
            $this->update(['features' => $features]);
        }
    }

    /**
     * إزالة ميزة
     */
    public function removeFeature(string $feature): void
    {
        $features = $this->features ?? [];
        $features = array_filter($features, fn($f) => $f !== $feature);
        $this->update(['features' => array_values($features)]);
    }

    /**
     * تحديث إحصائيات الاستخدام
     */
    public function updateUsageStats(): void
    {
        $this->update([
            'current_users_count' => $this->users()->count(),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * تجديد الترخيص
     */
    public function renewLicense(int $months = 12): void
    {
        $this->update([
            'license_end_date' => Carbon::parse($this->license_end_date)->addMonths($months),
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    /**
     * تعليق المستأجر
     */
    public function suspend(string $reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'is_active' => false,
            'notes' => $this->notes . "\n" . "معلق في " . now() . ": " . $reason,
        ]);
    }

    /**
     * إلغاء تعليق المستأجر
     */
    public function unsuspend(): void
    {
        $this->update([
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('license_end_date', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('license_end_date', '<=', now()->addDays($days))
                    ->where('license_end_date', '>=', now());
    }

    public function scopeTrial($query)
    {
        return $query->where('is_trial', true);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->tenant_code)) {
                $tenant->tenant_code = 'TNT' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
            
            if (empty($tenant->database_name)) {
                $tenant->database_name = 'tenant_' . strtolower($tenant->tenant_code);
            }
        });
    }
}
