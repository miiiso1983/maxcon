<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'super_admin_id',
        'tenant_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'severity',
        'category',
        'status',
        'error_message',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع السوبر أدمن
     */
    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class);
    }

    /**
     * العلاقة مع المستأجر
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * إنشاء سجل تدقيق جديد
     */
    public static function createLog(array $data): self
    {
        // دمج البيانات الافتراضية مع البيانات المرسلة
        $logData = array_merge([
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'Unknown',
            'url' => request()->fullUrl() ?? '',
            'method' => request()->method() ?? 'GET',
            'category' => 'general',
            'severity' => 'low',
            'status' => 'success',
        ], $data);

        // تقصير النصوص الطويلة لتجنب أخطاء قاعدة البيانات
        $logData['action'] = substr($logData['action'] ?? 'unknown', 0, 95);
        $logData['category'] = substr($logData['category'], 0, 95);
        $logData['severity'] = substr($logData['severity'], 0, 45);
        $logData['status'] = substr($logData['status'], 0, 45);
        $logData['method'] = substr($logData['method'], 0, 15);

        if (isset($logData['user_agent'])) {
            $logData['user_agent'] = substr($logData['user_agent'], 0, 500);
        }

        return self::create($logData);
    }

    /**
     * تسجيل عملية تسجيل دخول
     */
    public static function logLogin(SuperAdmin $admin, bool $success = true): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'action' => 'login',
            'description' => $success ? 'تسجيل دخول ناجح' : 'محاولة تسجيل دخول فاشلة',
            'category' => 'authentication',
            'severity' => $success ? 'low' : 'medium',
            'status' => $success ? 'success' : 'failed',
        ]);
    }

    /**
     * تسجيل عملية تسجيل خروج
     */
    public static function logLogout(SuperAdmin $admin): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'action' => 'logout',
            'description' => 'تسجيل خروج',
            'category' => 'authentication',
            'severity' => 'low',
            'status' => 'success',
        ]);
    }

    /**
     * تسجيل إنشاء مستأجر
     */
    public static function logTenantCreated(SuperAdmin $admin, Tenant $tenant): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'tenant_id' => $tenant->id,
            'action' => 'create',
            'model_type' => 'Tenant',
            'model_id' => $tenant->id,
            'description' => "إنشاء مستأجر جديد: {$tenant->name}",
            'new_values' => $tenant->toArray(),
            'category' => 'tenant_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);
    }

    /**
     * تسجيل تحديث مستأجر
     */
    public static function logTenantUpdated(SuperAdmin $admin, Tenant $tenant, array $oldValues): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'tenant_id' => $tenant->id,
            'action' => 'update',
            'model_type' => 'Tenant',
            'model_id' => $tenant->id,
            'description' => "تحديث بيانات المستأجر: {$tenant->name}",
            'old_values' => $oldValues,
            'new_values' => $tenant->toArray(),
            'category' => 'tenant_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);
    }

    /**
     * تسجيل حذف مستأجر
     */
    public static function logTenantDeleted(SuperAdmin $admin, Tenant $tenant): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'tenant_id' => $tenant->id,
            'action' => 'delete',
            'model_type' => 'Tenant',
            'model_id' => $tenant->id,
            'description' => "حذف المستأجر: {$tenant->name}",
            'old_values' => $tenant->toArray(),
            'category' => 'tenant_management',
            'severity' => 'high',
            'status' => 'success',
        ]);
    }

    /**
     * تسجيل تعليق مستأجر
     */
    public static function logTenantSuspended(SuperAdmin $admin, Tenant $tenant, string $reason): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'tenant_id' => $tenant->id,
            'action' => 'suspend',
            'model_type' => 'Tenant',
            'model_id' => $tenant->id,
            'description' => "تعليق المستأجر: {$tenant->name} - السبب: {$reason}",
            'metadata' => ['reason' => $reason],
            'category' => 'tenant_management',
            'severity' => 'high',
            'status' => 'success',
        ]);
    }

    /**
     * تسجيل إلغاء تعليق مستأجر
     */
    public static function logTenantUnsuspended(SuperAdmin $admin, Tenant $tenant): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'tenant_id' => $tenant->id,
            'action' => 'unsuspend',
            'model_type' => 'Tenant',
            'model_id' => $tenant->id,
            'description' => "إلغاء تعليق المستأجر: {$tenant->name}",
            'category' => 'tenant_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);
    }

    /**
     * تسجيل تجديد ترخيص
     */
    public static function logLicenseRenewed(SuperAdmin $admin, Tenant $tenant, int $months): self
    {
        return self::createLog([
            'super_admin_id' => $admin->id,
            'tenant_id' => $tenant->id,
            'action' => 'renew_license',
            'model_type' => 'Tenant',
            'model_id' => $tenant->id,
            'description' => "تجديد ترخيص المستأجر: {$tenant->name} لمدة {$months} شهر",
            'metadata' => ['months' => $months],
            'category' => 'license_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);
    }

    /**
     * Scopes
     */
    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('super_admin_id', $adminId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }
}
