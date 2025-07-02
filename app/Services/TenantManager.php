<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TenantManager
{
    /**
     * إنشاء مستأجر جديد مع الإعداد الكامل
     */
    public function createTenant(array $tenantData, array $managerData): Tenant
    {
        DB::beginTransaction();
        
        try {
            // إنشاء المستأجر
            $tenant = Tenant::create($tenantData);
            
            // إنشاء مدير النظام
            $manager = TenantUser::create([
                'tenant_id' => $tenant->id,
                'name' => $managerData['name'],
                'email' => $managerData['email'],
                'password' => Hash::make($managerData['password']),
                'phone' => $managerData['phone'] ?? null,
                'role' => 'system_manager',
                'status' => 'active',
            ]);
            
            // إعداد البيئة الأساسية للمستأجر
            $this->setupTenantEnvironment($tenant);
            
            // إنشاء البيانات الافتراضية
            $this->createDefaultData($tenant);
            
            DB::commit();
            
            return $tenant;
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * إعداد البيئة الأساسية للمستأجر
     */
    private function setupTenantEnvironment(Tenant $tenant): void
    {
        // إنشاء مجلدات التخزين
        $this->createTenantDirectories($tenant);
        
        // إعداد الإعدادات الافتراضية
        $this->setDefaultSettings($tenant);
        
        // إعداد الصلاحيات الافتراضية
        $this->setupDefaultPermissions($tenant);
    }

    /**
     * إنشاء مجلدات التخزين للمستأجر
     */
    private function createTenantDirectories(Tenant $tenant): void
    {
        $directories = [
            "tenants/{$tenant->tenant_code}",
            "tenants/{$tenant->tenant_code}/uploads",
            "tenants/{$tenant->tenant_code}/documents",
            "tenants/{$tenant->tenant_code}/images",
            "tenants/{$tenant->tenant_code}/reports",
            "tenants/{$tenant->tenant_code}/backups",
        ];

        foreach ($directories as $directory) {
            Storage::disk('local')->makeDirectory($directory);
            Storage::disk('public')->makeDirectory($directory);
        }
    }

    /**
     * إعداد الإعدادات الافتراضية
     */
    private function setDefaultSettings(Tenant $tenant): void
    {
        $defaultSettings = [
            'locale' => 'ar',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'theme' => 'default',
            'notifications' => [
                'email' => true,
                'sms' => false,
                'push' => true,
            ],
            'security' => [
                'two_factor_required' => false,
                'password_expiry_days' => 90,
                'session_timeout_minutes' => 120,
                'max_login_attempts' => 5,
            ],
            'features' => [
                'inventory_management' => true,
                'sales_management' => true,
                'reports' => true,
            ],
        ];

        $tenant->update(['settings' => $defaultSettings]);
    }

    /**
     * إعداد الصلاحيات الافتراضية
     */
    private function setupDefaultPermissions(Tenant $tenant): void
    {
        // يمكن إضافة منطق إنشاء الأدوار والصلاحيات الافتراضية هنا
        // باستخدام Spatie Permission package
    }

    /**
     * إنشاء البيانات الافتراضية
     */
    private function createDefaultData(Tenant $tenant): void
    {
        // تعيين المستأجر الحالي مؤقتاً
        app()->instance('current_tenant', $tenant);
        
        try {
            // إنشاء حسابات افتراضية
            $this->createDefaultAccounts($tenant);
            
            // إنشاء فئات افتراضية
            $this->createDefaultCategories($tenant);
            
        } finally {
            // إزالة المستأجر المؤقت
            app()->forgetInstance('current_tenant');
        }
    }

    /**
     * إنشاء الحسابات الافتراضية
     */
    private function createDefaultAccounts(Tenant $tenant): void
    {
        $defaultAccounts = [
            [
                'account_name' => 'الصندوق',
                'account_code' => 'T' . $tenant->id . '-1001',
                'account_type' => 'asset',
                'account_category' => 'current_assets',
                'balance_type' => 'debit',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
            ],
            [
                'account_name' => 'البنك',
                'account_code' => 'T' . $tenant->id . '-1002',
                'account_type' => 'asset',
                'account_category' => 'current_assets',
                'balance_type' => 'debit',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
            ],
            [
                'account_name' => 'المبيعات',
                'account_code' => 'T' . $tenant->id . '-4001',
                'account_type' => 'revenue',
                'account_category' => 'sales_revenue',
                'balance_type' => 'credit',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
            ],
            [
                'account_name' => 'تكلفة البضاعة المباعة',
                'account_code' => 'T' . $tenant->id . '-5001',
                'account_type' => 'expense',
                'account_category' => 'cost_of_goods_sold',
                'balance_type' => 'debit',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($defaultAccounts as $accountData) {
            if (class_exists(\App\Models\Account::class)) {
                \App\Models\Account::create(array_merge($accountData, [
                    'tenant_id' => $tenant->id
                ]));
            }
        }
    }

    /**
     * إنشاء الفئات الافتراضية
     */
    private function createDefaultCategories(Tenant $tenant): void
    {
        $defaultCategories = [
            'أدوية',
            'مستحضرات تجميل',
            'مكملات غذائية',
            'أدوات طبية',
            'منتجات العناية الشخصية',
        ];

        foreach ($defaultCategories as $categoryName) {
            if (class_exists(\App\Models\Category::class)) {
                \App\Models\Category::create([
                    'name' => $categoryName,
                    'tenant_id' => $tenant->id,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * تحديث إحصائيات استخدام المستأجر
     */
    public function updateTenantUsageStats(Tenant $tenant): void
    {
        $stats = [
            'current_users_count' => TenantUser::where('tenant_id', $tenant->id)->count(),
            'current_storage_gb' => $this->calculateStorageUsage($tenant),
            'last_activity_at' => now(),
        ];

        $tenant->update($stats);
    }

    /**
     * حساب استخدام التخزين
     */
    private function calculateStorageUsage(Tenant $tenant): float
    {
        $tenantPath = "tenants/{$tenant->tenant_code}";
        $totalSize = 0;

        try {
            $files = Storage::disk('local')->allFiles($tenantPath);
            foreach ($files as $file) {
                $totalSize += Storage::disk('local')->size($file);
            }

            $files = Storage::disk('public')->allFiles($tenantPath);
            foreach ($files as $file) {
                $totalSize += Storage::disk('public')->size($file);
            }
        } catch (\Exception $e) {
            \Log::warning("فشل في حساب استخدام التخزين للمستأجر {$tenant->tenant_code}: " . $e->getMessage());
        }

        // تحويل من bytes إلى GB
        return round($totalSize / (1024 * 1024 * 1024), 2);
    }

    /**
     * التحقق من حدود الاستخدام
     */
    public function checkUsageLimits(Tenant $tenant): array
    {
        $checks = [
            'users_limit_exceeded' => $tenant->isUserLimitExceeded(),
            'storage_limit_exceeded' => $tenant->isStorageLimitExceeded(),
            'license_expired' => !$tenant->isLicenseValid(),
            'license_expiring_soon' => $tenant->isLicenseExpiringSoon(),
        ];

        return $checks;
    }

    /**
     * تطبيق ترقية الترخيص
     */
    public function upgradeLicense(Tenant $tenant, string $newLicenseType): bool
    {
        $license = \App\Models\License::where('license_type', $newLicenseType)->first();
        
        if (!$license) {
            throw new \Exception("نوع الترخيص غير موجود: {$newLicenseType}");
        }

        $tenant->update([
            'license_type' => $license->license_type,
            'max_users' => $license->max_users,
            'max_storage_gb' => $license->max_storage_gb,
            'features' => $license->features,
            'monthly_fee' => $license->getPrice('monthly'),
        ]);

        return true;
    }

    /**
     * إنشاء نسخة احتياطية للمستأجر
     */
    public function createBackup(Tenant $tenant): string
    {
        $backupService = new TenantDeletionService();
        return $backupService->createBackupBeforeDeletion($tenant);
    }

    /**
     * استعادة المستأجر من نسخة احتياطية
     */
    public function restoreFromBackup(string $backupPath): Tenant
    {
        if (!Storage::disk('local')->exists($backupPath)) {
            throw new \Exception("ملف النسخة الاحتياطية غير موجود: {$backupPath}");
        }

        $backupData = json_decode(Storage::disk('local')->get($backupPath), true);
        
        if (!$backupData || !isset($backupData['tenant'])) {
            throw new \Exception("ملف النسخة الاحتياطية تالف أو غير صالح");
        }

        DB::beginTransaction();
        
        try {
            // استعادة المستأجر
            $tenant = Tenant::create($backupData['tenant']);
            
            // استعادة البيانات
            foreach ($backupData['data'] as $modelType => $records) {
                $this->restoreModelData($modelType, $records, $tenant->id);
            }
            
            DB::commit();
            
            return $tenant;
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * استعادة بيانات نموذج معين
     */
    private function restoreModelData(string $modelType, array $records, int $tenantId): void
    {
        $modelMap = [
            'tenant_users' => TenantUser::class,
            'users' => \App\Models\User::class,
            'customers' => \App\Models\Customer::class,
            'companies' => \App\Models\Company::class,
            'accounts' => \App\Models\Account::class,
        ];

        if (!isset($modelMap[$modelType]) || !class_exists($modelMap[$modelType])) {
            return;
        }

        $modelClass = $modelMap[$modelType];
        
        foreach ($records as $record) {
            // تحديث tenant_id وإزالة المعرف الأصلي
            $record['tenant_id'] = $tenantId;
            unset($record['id'], $record['created_at'], $record['updated_at']);
            
            $modelClass::create($record);
        }
    }
}
