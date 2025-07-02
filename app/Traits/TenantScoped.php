<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait TenantScoped
{
    /**
     * Boot the trait
     */
    protected static function bootTenantScoped()
    {
        // تطبيق نطاق المستأجر تلقائياً على جميع الاستعلامات
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (static::shouldApplyTenantScope()) {
                $tenantId = static::getCurrentTenantId();
                if ($tenantId) {
                    $builder->where(static::getTenantColumn(), $tenantId);
                }
            }
        });

        // تعيين tenant_id تلقائياً عند إنشاء نموذج جديد
        static::creating(function (Model $model) {
            if (static::shouldApplyTenantScope() && !$model->getAttribute(static::getTenantColumn())) {
                $tenantId = static::getCurrentTenantId();
                if ($tenantId) {
                    $model->setAttribute(static::getTenantColumn(), $tenantId);
                }
            }
        });
    }

    /**
     * العلاقة مع المستأجر
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant::class, static::getTenantColumn());
    }

    /**
     * الحصول على اسم عمود المستأجر
     */
    public static function getTenantColumn(): string
    {
        return 'tenant_id';
    }

    /**
     * الحصول على معرف المستأجر الحالي
     */
    public static function getCurrentTenantId(): ?int
    {
        // محاولة الحصول على المستأجر من التطبيق
        if (app()->has('current_tenant')) {
            $tenant = app('current_tenant');
            return $tenant ? $tenant->id : null;
        }

        // محاولة الحصول على المستأجر من المستخدم المسجل دخوله
        if (auth()->guard('tenant')->check()) {
            $user = auth()->guard('tenant')->user();
            return $user->tenant_id ?? null;
        }

        // محاولة الحصول على المستأجر من الجلسة
        if (session()->has('current_tenant_id')) {
            return session('current_tenant_id');
        }

        return null;
    }

    /**
     * تحديد ما إذا كان يجب تطبيق نطاق المستأجر
     */
    public static function shouldApplyTenantScope(): bool
    {
        // عدم تطبيق النطاق في حالة تسجيل دخول السوبر أدمن
        if (auth()->guard('super_admin')->check()) {
            return false;
        }

        // عدم تطبيق النطاق في وحدة التحكم
        if (app()->runningInConsole()) {
            return false;
        }

        // عدم تطبيق النطاق في الاختبارات (اختياري)
        if (app()->environment('testing')) {
            return config('app.apply_tenant_scope_in_tests', false);
        }

        return true;
    }

    /**
     * استعلام بدون نطاق المستأجر
     */
    public static function withoutTenantScope()
    {
        return static::withoutGlobalScope('tenant');
    }

    /**
     * استعلام لمستأجر محدد
     */
    public static function forTenant($tenantId)
    {
        return static::withoutTenantScope()->where(static::getTenantColumn(), $tenantId);
    }

    /**
     * استعلام لجميع المستأجرين
     */
    public static function allTenants()
    {
        return static::withoutTenantScope();
    }

    /**
     * تعيين المستأجر الحالي مؤقتاً
     */
    public static function withTenant($tenantId, callable $callback)
    {
        $originalTenantId = static::getCurrentTenantId();
        
        // تعيين المستأجر الجديد
        app()->instance('current_tenant', \App\Models\Tenant::find($tenantId));
        
        try {
            return $callback();
        } finally {
            // استعادة المستأجر الأصلي
            if ($originalTenantId) {
                app()->instance('current_tenant', \App\Models\Tenant::find($originalTenantId));
            } else {
                app()->forgetInstance('current_tenant');
            }
        }
    }

    /**
     * التحقق من انتماء النموذج للمستأجر الحالي
     */
    public function belongsToCurrentTenant(): bool
    {
        $currentTenantId = static::getCurrentTenantId();
        return $currentTenantId && $this->getAttribute(static::getTenantColumn()) == $currentTenantId;
    }

    /**
     * التحقق من انتماء النموذج لمستأجر محدد
     */
    public function belongsToTenant($tenantId): bool
    {
        return $this->getAttribute(static::getTenantColumn()) == $tenantId;
    }

    /**
     * نسخ النموذج لمستأجر آخر
     */
    public function copyToTenant($tenantId, array $overrides = [])
    {
        $attributes = $this->getAttributes();
        
        // إزالة المعرف الأساسي والطوابع الزمنية
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
        
        // تعيين المستأجر الجديد
        $attributes[static::getTenantColumn()] = $tenantId;
        
        // تطبيق التعديلات
        $attributes = array_merge($attributes, $overrides);
        
        return static::withTenant($tenantId, function () use ($attributes) {
            return static::create($attributes);
        });
    }

    /**
     * نقل النموذج لمستأجر آخر
     */
    public function moveToTenant($tenantId)
    {
        return $this->update([static::getTenantColumn() => $tenantId]);
    }

    /**
     * الحصول على عدد السجلات لكل مستأجر
     */
    public static function countByTenant()
    {
        return static::withoutTenantScope()
            ->selectRaw(static::getTenantColumn() . ', COUNT(*) as count')
            ->groupBy(static::getTenantColumn())
            ->pluck('count', static::getTenantColumn());
    }

    /**
     * حذف جميع البيانات لمستأجر محدد
     */
    public static function deleteAllForTenant($tenantId)
    {
        return static::withoutTenantScope()
            ->where(static::getTenantColumn(), $tenantId)
            ->delete();
    }

    /**
     * حذف جميع البيانات لمستأجر محدد نهائياً
     */
    public static function forceDeleteAllForTenant($tenantId)
    {
        return static::withoutTenantScope()
            ->where(static::getTenantColumn(), $tenantId)
            ->forceDelete();
    }

    /**
     * تصدير البيانات لمستأجر محدد
     */
    public static function exportForTenant($tenantId, array $columns = ['*'])
    {
        return static::withoutTenantScope()
            ->where(static::getTenantColumn(), $tenantId)
            ->get($columns);
    }

    /**
     * إحصائيات المستأجر
     */
    public static function getTenantStats($tenantId)
    {
        return [
            'total_records' => static::forTenant($tenantId)->count(),
            'created_today' => static::forTenant($tenantId)
                ->whereDate('created_at', today())
                ->count(),
            'created_this_week' => static::forTenant($tenantId)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'created_this_month' => static::forTenant($tenantId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * التحقق من وجود بيانات للمستأجر
     */
    public static function hasTenantData($tenantId): bool
    {
        return static::forTenant($tenantId)->exists();
    }

    /**
     * تنظيف البيانات القديمة للمستأجر
     */
    public static function cleanupOldDataForTenant($tenantId, int $daysOld = 365)
    {
        $cutoffDate = now()->subDays($daysOld);
        
        return static::withoutTenantScope()
            ->where(static::getTenantColumn(), $tenantId)
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }
}
