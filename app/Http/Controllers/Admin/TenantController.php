<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\License;
use App\Models\AdminAuditLog;
use App\Models\SuperAdmin;
use App\Services\TenantDeletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * الحصول على Super Admin الحالي
     */
    private function getCurrentSuperAdmin(): ?SuperAdmin
    {
        $admin = auth('super_admin')->user();
        return $admin instanceof SuperAdmin ? $admin : null;
    }
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * عرض قائمة المستأجرين
     */
    public function index(Request $request)
    {
        $query = Tenant::with(['systemManager', 'tenantUsers']);

        // تطبيق الفلاتر
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('license_type')) {
            $query->where('license_type', $request->license_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('tenant_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('expiring_soon')) {
            $query->expiringSoon(30);
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $tenants = $query->paginate(15);

        // إحصائيات سريعة
        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::active()->count(),
            'expired' => Tenant::expired()->count(),
            'trial' => Tenant::trial()->count(),
            'expiring_soon' => Tenant::expiringSoon(30)->count(),
        ];

        return view('admin.tenants.index', compact('tenants', 'stats'));
    }

    /**
     * عرض تفاصيل مستأجر
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['systemManager', 'tenantUsers', 'auditLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // إحصائيات المستأجر
        $stats = [
            'users_count' => $tenant->tenantUsers()->count(),
            'active_users' => $tenant->tenantUsers()->active()->count(),
            'storage_used' => $tenant->current_storage_gb,
            'days_until_expiry' => $tenant->getDaysUntilExpiry(),
            'total_logins' => $tenant->total_logins,
        ];

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * عرض نموذج إنشاء مستأجر جديد
     */
    public function create()
    {
        $licenses = License::where('status', 'active')->orderBy('sort_order')->get();

        $licenseTypes = [
            'basic' => 'أساسي',
            'standard' => 'معياري',
            'premium' => 'مميز',
            'enterprise' => 'مؤسسي',
        ];

        $features = [
            'inventory_management' => 'إدارة المخزون',
            'sales_management' => 'إدارة المبيعات',
            'purchase_management' => 'إدارة المشتريات',
            'financial_reports' => 'التقارير المالية',
            'advanced_analytics' => 'التحليلات المتقدمة',
            'api_access' => 'الوصول للـ API',
            'custom_branding' => 'العلامة التجارية المخصصة',
        ];

        return view('admin.tenants.create', compact('licenses', 'licenseTypes', 'features'));
    }

    /**
     * حفظ مستأجر جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'license_type' => 'required|in:basic,standard,premium,enterprise',
            'max_users' => 'required|integer|min:1|max:1000',
            'max_storage_gb' => 'required|integer|min:1|max:1000',
            'features' => 'nullable|array',
            'license_start_date' => 'required|date',
            'license_end_date' => 'required|date|after:license_start_date',
            'is_trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'monthly_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            
            // بيانات المدير
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:tenant_users,email',
            'manager_password' => 'required|string|min:8|confirmed',
            'manager_phone' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // إنشاء المستأجر
            $tenant = Tenant::create($validated);

            // إنشاء مدير النظام
            $manager = TenantUser::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['manager_name'],
                'email' => $validated['manager_email'],
                'password' => Hash::make($validated['manager_password']),
                'phone' => $validated['manager_phone'] ?? null,
                'role' => 'system_manager',
                'status' => 'active',
            ]);

            // تسجيل العملية في سجل التدقيق
            $admin = $this->getCurrentSuperAdmin();
            if ($admin) {
                AdminAuditLog::logTenantCreated($admin, $tenant);
            }

            DB::commit();

            return redirect()->route('admin.tenants.show', $tenant)
                           ->with('success', 'تم إنشاء المستأجر بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'حدث خطأ أثناء إنشاء المستأجر: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تعديل مستأجر
     */
    public function edit(Tenant $tenant)
    {
        $licenseTypes = [
            'basic' => 'أساسي',
            'standard' => 'معياري',
            'premium' => 'مميز',
            'enterprise' => 'مؤسسي',
        ];

        $features = [
            'inventory_management' => 'إدارة المخزون',
            'sales_management' => 'إدارة المبيعات',
            'purchase_management' => 'إدارة المشتريات',
            'financial_reports' => 'التقارير المالية',
            'advanced_analytics' => 'التحليلات المتقدمة',
            'api_access' => 'الوصول للـ API',
            'custom_branding' => 'العلامة التجارية المخصصة',
        ];

        return view('admin.tenants.edit', compact('tenant', 'licenseTypes', 'features'));
    }

    /**
     * تحديث بيانات مستأجر
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('tenants')->ignore($tenant->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'license_type' => 'required|in:basic,standard,premium,enterprise',
            'max_users' => 'required|integer|min:1|max:1000',
            'max_storage_gb' => 'required|integer|min:1|max:1000',
            'features' => 'nullable|array',
            'license_start_date' => 'required|date',
            'license_end_date' => 'required|date|after:license_start_date',
            'is_trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'monthly_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'status' => 'required|in:active,suspended,expired,cancelled',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $tenant->toArray();
        
        $tenant->update($validated);

        // تسجيل العملية في سجل التدقيق
        $admin = $this->getCurrentSuperAdmin();
        if ($admin) {
            AdminAuditLog::logTenantUpdated($admin, $tenant, $oldValues);
        }

        return redirect()->route('admin.tenants.show', $tenant)
                       ->with('success', 'تم تحديث بيانات المستأجر بنجاح');
    }

    /**
     * تعليق مستأجر
     */
    public function suspend(Request $request, Tenant $tenant)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $tenant->suspend($request->reason);

        // تسجيل العملية في سجل التدقيق
        $admin = $this->getCurrentSuperAdmin();
        if ($admin) {
            AdminAuditLog::logTenantSuspended($admin, $tenant, $request->reason);
        }

        return back()->with('success', 'تم تعليق المستأجر بنجاح');
    }

    /**
     * إلغاء تعليق مستأجر
     */
    public function unsuspend(Tenant $tenant)
    {
        $tenant->unsuspend();

        // تسجيل العملية في سجل التدقيق
        $admin = $this->getCurrentSuperAdmin();
        if ($admin) {
            AdminAuditLog::logTenantUnsuspended($admin, $tenant);
        }

        return back()->with('success', 'تم إلغاء تعليق المستأجر بنجاح');
    }

    /**
     * تجديد ترخيص مستأجر
     */
    public function renewLicense(Request $request, Tenant $tenant)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:60',
        ]);

        $tenant->renewLicense($request->months);

        // تسجيل العملية في سجل التدقيق
        $admin = $this->getCurrentSuperAdmin();
        if ($admin) {
            AdminAuditLog::logLicenseRenewed($admin, $tenant, $request->months);
        }

        return back()->with('success', "تم تجديد الترخيص لمدة {$request->months} شهر");
    }

    /**
     * حذف مستأجر (حذف ناعم)
     */
    public function destroy(Tenant $tenant)
    {
        // تسجيل العملية في سجل التدقيق قبل الحذف
        $admin = $this->getCurrentSuperAdmin();
        if ($admin) {
            AdminAuditLog::logTenantDeleted($admin, $tenant);
        }

        $tenant->delete();

        return redirect()->route('admin.tenants.index')
                       ->with('success', 'تم حذف المستأجر بنجاح');
    }

    // TODO: إضافة دالة confirmDeletion مع authorization لاحقاً

    // TODO: إضافة دالة forceDelete مع authorization لاحقاً

    /**
     * عرض تقرير استخدام المستأجر
     */
    public function usageReport(Tenant $tenant, TenantDeletionService $deletionService)
    {
        $report = $deletionService->getTenantUsageReport($tenant);

        return response()->json($report);
    }
}
