<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Account;
use App\Models\AdminAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TenantDeletionService
{
    /**
     * حذف مستأجر مع جميع بياناته بشكل كامل
     */
    public function deleteTenantCompletely(Tenant $tenant, bool $forceDelete = false): array
    {
        $deletionReport = [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'started_at' => now(),
            'completed_at' => null,
            'success' => false,
            'deleted_records' => [],
            'errors' => [],
            'storage_cleaned' => false,
        ];

        DB::beginTransaction();
        
        try {
            Log::info("بدء حذف المستأجر: {$tenant->name} (ID: {$tenant->id})");

            // 1. حذف المستخدمين والبيانات المرتبطة
            $deletionReport['deleted_records']['tenant_users'] = $this->deleteTenantUsers($tenant->id);
            $deletionReport['deleted_records']['users'] = $this->deleteUsers($tenant->id);

            // 2. حذف بيانات العملاء
            $deletionReport['deleted_records']['customers'] = $this->deleteCustomers($tenant->id);

            // 3. حذف بيانات الشركات
            $deletionReport['deleted_records']['companies'] = $this->deleteCompanies($tenant->id);

            // 4. حذف الحسابات المالية
            $deletionReport['deleted_records']['accounts'] = $this->deleteAccounts($tenant->id);

            // 5. حذف البيانات التجارية (الطلبات، الفواتير، إلخ)
            $deletionReport['deleted_records'] = array_merge(
                $deletionReport['deleted_records'],
                $this->deleteBusinessData($tenant->id)
            );

            // 6. حذف الملفات والمرفقات
            $deletionReport['storage_cleaned'] = $this->cleanupTenantStorage($tenant);

            // 7. حذف سجلات التدقيق المرتبطة بالمستأجر
            $deletionReport['deleted_records']['audit_logs'] = $this->deleteAuditLogs($tenant->id);

            // 8. حذف المستأجر نفسه
            if ($forceDelete) {
                $tenant->forceDelete();
            } else {
                $tenant->delete();
            }

            $deletionReport['completed_at'] = now();
            $deletionReport['success'] = true;

            DB::commit();

            Log::info("تم حذف المستأجر بنجاح: {$tenant->name}", $deletionReport);

        } catch (\Exception $e) {
            DB::rollback();
            
            $deletionReport['errors'][] = $e->getMessage();
            $deletionReport['completed_at'] = now();
            
            Log::error("فشل في حذف المستأجر: {$tenant->name}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'report' => $deletionReport
            ]);
        }

        return $deletionReport;
    }

    /**
     * حذف مستخدمي المستأجر
     */
    private function deleteTenantUsers(int $tenantId): int
    {
        return TenantUser::where('tenant_id', $tenantId)->delete();
    }

    /**
     * حذف المستخدمين العاديين
     */
    private function deleteUsers(int $tenantId): int
    {
        return User::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->delete();
    }

    /**
     * حذف العملاء
     */
    private function deleteCustomers(int $tenantId): int
    {
        return Customer::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->delete();
    }

    /**
     * حذف الشركات
     */
    private function deleteCompanies(int $tenantId): int
    {
        return Company::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->delete();
    }

    /**
     * حذف الحسابات المالية
     */
    private function deleteAccounts(int $tenantId): int
    {
        return Account::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->delete();
    }

    /**
     * حذف البيانات التجارية
     */
    private function deleteBusinessData(int $tenantId): array
    {
        $deleted = [];

        // قائمة النماذج التي تحتوي على بيانات تجارية
        $models = [
            'suppliers' => \App\Models\Supplier::class,
            'items' => \App\Models\Item::class,
            'orders' => \App\Models\Order::class,
            'order_items' => \App\Models\OrderItem::class,
            'invoices' => \App\Models\Invoice::class,
            'collections' => \App\Models\Collection::class,
            'returns' => \App\Models\Return::class,
        ];

        foreach ($models as $key => $modelClass) {
            if (class_exists($modelClass)) {
                try {
                    $deleted[$key] = $modelClass::withoutTenantScope()
                        ->where('tenant_id', $tenantId)
                        ->delete();
                } catch (\Exception $e) {
                    Log::warning("فشل في حذف {$key} للمستأجر {$tenantId}: " . $e->getMessage());
                    $deleted[$key] = 0;
                }
            } else {
                $deleted[$key] = 0;
            }
        }

        return $deleted;
    }

    /**
     * تنظيف ملفات التخزين
     */
    private function cleanupTenantStorage(Tenant $tenant): bool
    {
        try {
            $tenantStoragePath = "tenants/{$tenant->tenant_code}";
            
            // حذف مجلد المستأجر من التخزين العام
            if (Storage::disk('public')->exists($tenantStoragePath)) {
                Storage::disk('public')->deleteDirectory($tenantStoragePath);
            }

            // حذف مجلد المستأجر من التخزين الخاص
            if (Storage::disk('local')->exists($tenantStoragePath)) {
                Storage::disk('local')->deleteDirectory($tenantStoragePath);
            }

            // حذف النسخ الاحتياطية إن وجدت
            $backupPath = "backups/{$tenant->tenant_code}";
            if (Storage::disk('local')->exists($backupPath)) {
                Storage::disk('local')->deleteDirectory($backupPath);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("فشل في تنظيف ملفات المستأجر {$tenant->tenant_code}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف سجلات التدقيق
     */
    private function deleteAuditLogs(int $tenantId): int
    {
        return AdminAuditLog::where('tenant_id', $tenantId)->delete();
    }

    /**
     * إنشاء نسخة احتياطية قبل الحذف
     */
    public function createBackupBeforeDeletion(Tenant $tenant): string
    {
        $backupData = [
            'tenant' => $tenant->toArray(),
            'created_at' => now()->toISOString(),
            'data' => []
        ];

        // تصدير بيانات المستأجر
        $models = [
            'tenant_users' => TenantUser::class,
            'users' => User::class,
            'customers' => Customer::class,
            'companies' => Company::class,
            'accounts' => Account::class,
        ];

        foreach ($models as $key => $modelClass) {
            if (class_exists($modelClass)) {
                try {
                    if (method_exists($modelClass, 'exportForTenant')) {
                        $backupData['data'][$key] = $modelClass::exportForTenant($tenant->id)->toArray();
                    } else {
                        $backupData['data'][$key] = $modelClass::withoutTenantScope()
                            ->where('tenant_id', $tenant->id)
                            ->get()
                            ->toArray();
                    }
                } catch (\Exception $e) {
                    Log::warning("فشل في تصدير {$key} للنسخة الاحتياطية: " . $e->getMessage());
                    $backupData['data'][$key] = [];
                }
            }
        }

        // حفظ النسخة الاحتياطية
        $backupFileName = "tenant_backup_{$tenant->tenant_code}_" . now()->format('Y-m-d_H-i-s') . '.json';
        $backupPath = "backups/{$tenant->tenant_code}/{$backupFileName}";
        
        Storage::disk('local')->put($backupPath, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $backupPath;
    }

    /**
     * التحقق من إمكانية حذف المستأجر
     */
    public function canDeleteTenant(Tenant $tenant): array
    {
        $checks = [
            'can_delete' => true,
            'warnings' => [],
            'blockers' => [],
            'data_summary' => []
        ];

        // فحص البيانات الموجودة
        $dataChecks = [
            'users' => User::forTenant($tenant->id)->count(),
            'customers' => Customer::forTenant($tenant->id)->count(),
            'companies' => Company::forTenant($tenant->id)->count(),
        ];

        foreach ($dataChecks as $type => $count) {
            $checks['data_summary'][$type] = $count;
            if ($count > 0) {
                $checks['warnings'][] = "يحتوي على {$count} من {$type}";
            }
        }

        // فحص الحالة المالية
        if ($tenant->monthly_fee > 0 && $tenant->status === 'active') {
            $checks['warnings'][] = 'المستأجر لديه اشتراك نشط مدفوع';
        }

        // فحص آخر نشاط
        if ($tenant->last_activity_at && $tenant->last_activity_at->isAfter(now()->subDays(7))) {
            $checks['warnings'][] = 'كان نشطاً خلال آخر 7 أيام';
        }

        return $checks;
    }

    /**
     * الحصول على تقرير استخدام المستأجر
     */
    public function getTenantUsageReport(Tenant $tenant): array
    {
        $report = [
            'tenant_info' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'code' => $tenant->tenant_code,
                'status' => $tenant->status,
                'created_at' => $tenant->created_at,
                'last_activity' => $tenant->last_activity_at,
            ],
            'usage_stats' => [
                'users_count' => $tenant->current_users_count,
                'storage_used' => $tenant->current_storage_gb,
                'total_logins' => $tenant->total_logins,
            ],
            'data_counts' => []
        ];

        // إحصائيات البيانات
        $models = [
            'users' => User::class,
            'customers' => Customer::class,
            'companies' => Company::class,
            'accounts' => Account::class,
        ];

        foreach ($models as $key => $modelClass) {
            if (class_exists($modelClass)) {
                try {
                    $report['data_counts'][$key] = $modelClass::forTenant($tenant->id)->count();
                } catch (\Exception $e) {
                    $report['data_counts'][$key] = 0;
                }
            }
        }

        return $report;
    }
}
