<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_key',
        'license_type',
        'name',
        'description',
        'max_users',
        'max_storage_gb',
        'max_tenants',
        'max_api_calls_per_month',
        'features',
        'modules',
        'integrations',
        'duration_months',
        'is_renewable',
        'auto_renewal',
        'trial_available',
        'trial_days',
        'price_monthly',
        'price_yearly',
        'currency',
        'discount_percentage',
        'support_level',
        'priority_support',
        'phone_support',
        'custom_training',
        'status',
        'is_public',
        'requires_approval',
        'sort_order',
        'metadata',
        'terms_and_conditions',
        'notes',
    ];

    protected $casts = [
        'features' => 'array',
        'modules' => 'array',
        'integrations' => 'array',
        'metadata' => 'array',
        'is_renewable' => 'boolean',
        'auto_renewal' => 'boolean',
        'trial_available' => 'boolean',
        'priority_support' => 'boolean',
        'phone_support' => 'boolean',
        'custom_training' => 'boolean',
        'is_public' => 'boolean',
        'requires_approval' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'max_users' => 'integer',
        'max_storage_gb' => 'integer',
        'max_tenants' => 'integer',
        'max_api_calls_per_month' => 'integer',
        'duration_months' => 'integer',
        'trial_days' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * العلاقة مع المستأجرين
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'license_type', 'license_type');
    }

    /**
     * التحقق من وجود ميزة معينة
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * التحقق من وجود وحدة معينة
     */
    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules ?? []);
    }

    /**
     * التحقق من وجود تكامل معين
     */
    public function hasIntegration(string $integration): bool
    {
        return in_array($integration, $this->integrations ?? []);
    }

    /**
     * الحصول على السعر بناءً على نوع الفوترة
     */
    public function getPrice(string $billingCycle = 'monthly'): float
    {
        $price = match ($billingCycle) {
            'yearly' => $this->price_yearly,
            'quarterly' => $this->price_monthly * 3,
            default => $this->price_monthly,
        };

        // تطبيق الخصم
        if ($this->discount_percentage > 0) {
            $price *= 1 - $this->discount_percentage / 100;
        }

        return round($price, 2);
    }

    /**
     * الحصول على السعر مع الخصم
     */
    public function getDiscountedPrice(string $billingCycle = 'monthly'): float
    {
        return $this->getPrice($billingCycle);
    }

    /**
     * الحصول على مبلغ الوفر السنوي
     */
    public function getYearlySavings(): float
    {
        $monthlyTotal = $this->price_monthly * 12;
        $yearlyPrice = $this->price_yearly;
        
        return max(0, $monthlyTotal - $yearlyPrice);
    }

    /**
     * الحصول على نسبة الوفر السنوي
     */
    public function getYearlySavingsPercentage(): float
    {
        $monthlyTotal = $this->price_monthly * 12;
        if ($monthlyTotal == 0) return 0;
        
        $savings = $this->getYearlySavings();
        return round(($savings / $monthlyTotal) * 100, 1);
    }

    /**
     * التحقق من إمكانية الترقية من ترخيص آخر
     */
    public function canUpgradeFrom(License $fromLicense): bool
    {
        // يمكن الترقية إذا كان الترخيص الجديد يوفر مميزات أكثر
        return $this->max_users >= $fromLicense->max_users &&
               $this->max_storage_gb >= $fromLicense->max_storage_gb &&
               $this->price_monthly >= $fromLicense->price_monthly;
    }

    /**
     * الحصول على قائمة المميزات المنسقة
     */
    public function getFormattedFeatures(): array
    {
        $featureLabels = [
            'inventory_management' => 'إدارة المخزون',
            'sales_management' => 'إدارة المبيعات',
            'purchase_management' => 'إدارة المشتريات',
            'financial_reports' => 'التقارير المالية',
            'advanced_analytics' => 'التحليلات المتقدمة',
            'api_access' => 'الوصول للـ API',
            'custom_branding' => 'العلامة التجارية المخصصة',
            'multi_location' => 'متعدد المواقع',
            'barcode_scanning' => 'مسح الباركود',
            'prescription_management' => 'إدارة الوصفات',
            'insurance_integration' => 'تكامل التأمين',
            'loyalty_program' => 'برنامج الولاء',
            'automated_reordering' => 'إعادة الطلب التلقائي',
            'expiry_tracking' => 'تتبع انتهاء الصلاحية',
            'batch_tracking' => 'تتبع الدفعات',
        ];

        $formatted = [];
        foreach ($this->features ?? [] as $feature) {
            $formatted[] = [
                'key' => $feature,
                'label' => $featureLabels[$feature] ?? $feature,
            ];
        }

        return $formatted;
    }

    /**
     * الحصول على مستوى الدعم المنسق
     */
    public function getFormattedSupportLevel(): string
    {
        return match ($this->support_level) {
            'basic' => 'أساسي',
            'standard' => 'معياري',
            'premium' => 'مميز',
            'enterprise' => 'مؤسسي',
            default => $this->support_level,
        };
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->public();
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('license_type', $type);
    }

    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('price_monthly', $direction);
    }

    public function scopeOrderByPopularity($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($license) {
            if (empty($license->license_key)) {
                $license->license_key = 'LIC-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * الحصول على التراخيص الافتراضية
     */
    public static function getDefaultLicenses(): array
    {
        return [
            [
                'license_type' => 'basic',
                'name' => 'الباقة الأساسية',
                'description' => 'مناسبة للصيدليات الصغيرة',
                'max_users' => 3,
                'max_storage_gb' => 2,
                'features' => ['inventory_management', 'sales_management', 'basic_reports'],
                'modules' => ['inventory', 'sales'],
                'price_monthly' => 29.99,
                'price_yearly' => 299.99,
                'trial_days' => 14,
            ],
            [
                'license_type' => 'standard',
                'name' => 'الباقة المعيارية',
                'description' => 'مناسبة للصيدليات المتوسطة',
                'max_users' => 10,
                'max_storage_gb' => 10,
                'features' => ['inventory_management', 'sales_management', 'purchase_management', 'financial_reports'],
                'modules' => ['inventory', 'sales', 'purchases', 'reports'],
                'price_monthly' => 79.99,
                'price_yearly' => 799.99,
                'trial_days' => 30,
            ],
            [
                'license_type' => 'premium',
                'name' => 'الباقة المميزة',
                'description' => 'مناسبة للصيدليات الكبيرة',
                'max_users' => 25,
                'max_storage_gb' => 50,
                'features' => ['inventory_management', 'sales_management', 'purchase_management', 'financial_reports', 'advanced_analytics', 'api_access'],
                'modules' => ['inventory', 'sales', 'purchases', 'reports', 'analytics', 'api'],
                'price_monthly' => 149.99,
                'price_yearly' => 1499.99,
                'support_level' => 'premium',
                'priority_support' => true,
                'trial_days' => 30,
            ],
            [
                'license_type' => 'enterprise',
                'name' => 'الباقة المؤسسية',
                'description' => 'مناسبة للسلاسل والمؤسسات الكبيرة',
                'max_users' => 100,
                'max_storage_gb' => 200,
                'max_tenants' => 10,
                'features' => ['inventory_management', 'sales_management', 'purchase_management', 'financial_reports', 'advanced_analytics', 'api_access', 'custom_branding', 'multi_location'],
                'modules' => ['inventory', 'sales', 'purchases', 'reports', 'analytics', 'api', 'multi_tenant'],
                'price_monthly' => 299.99,
                'price_yearly' => 2999.99,
                'support_level' => 'enterprise',
                'priority_support' => true,
                'phone_support' => true,
                'custom_training' => true,
                'trial_days' => 30,
            ],
        ];
    }
}
