<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Tenant;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LicenseController extends Controller
{
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * عرض قائمة التراخيص
     */
    public function index(Request $request)
    {
        $query = License::query();

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
                  ->orWhere('license_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $licenses = $query->paginate(15);

        // إحصائيات سريعة
        $stats = [
            'total' => License::count(),
            'active' => License::active()->count(),
            'public' => License::public()->count(),
            'with_trial' => License::where('trial_available', true)->count(),
        ];

        return view('admin.licenses.index', compact('licenses', 'stats'));
    }

    /**
     * عرض تفاصيل ترخيص
     */
    public function show(License $license)
    {
        $license->load(['tenants' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // إحصائيات الترخيص
        $stats = [
            'active_tenants' => $license->tenants()->active()->count(),
            'total_tenants' => $license->tenants()->count(),
            'total_revenue' => $license->tenants()->sum('monthly_fee'),
            'avg_usage' => $license->tenants()->avg('current_users_count'),
        ];

        return view('admin.licenses.show', compact('license', 'stats'));
    }

    /**
     * عرض نموذج إنشاء ترخيص جديد
     */
    public function create()
    {
        $availableFeatures = $this->getAvailableFeatures();
        $availableModules = $this->getAvailableModules();
        $availableIntegrations = $this->getAvailableIntegrations();

        return view('admin.licenses.create', compact(
            'availableFeatures',
            'availableModules', 
            'availableIntegrations'
        ));
    }

    /**
     * حفظ ترخيص جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_type' => 'required|string|unique:licenses,license_type',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_users' => 'required|integer|min:1|max:1000',
            'max_storage_gb' => 'required|integer|min:1|max:1000',
            'max_tenants' => 'required|integer|min:1|max:100',
            'max_api_calls_per_month' => 'required|integer|min:1000',
            'features' => 'required|array|min:1',
            'modules' => 'required|array|min:1',
            'integrations' => 'nullable|array',
            'duration_months' => 'required|integer|min:1|max:60',
            'is_renewable' => 'boolean',
            'auto_renewal' => 'boolean',
            'trial_available' => 'boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'support_level' => 'required|in:basic,standard,premium,enterprise',
            'priority_support' => 'boolean',
            'phone_support' => 'boolean',
            'custom_training' => 'boolean',
            'status' => 'required|in:active,inactive,deprecated',
            'is_public' => 'boolean',
            'requires_approval' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'terms_and_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $license = License::create($validated);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'create',
            'model_type' => 'License',
            'model_id' => $license->id,
            'description' => "إنشاء ترخيص جديد: {$license->name}",
            'new_values' => $license->toArray(),
            'category' => 'license_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return redirect()->route('admin.licenses.show', $license)
                       ->with('success', 'تم إنشاء الترخيص بنجاح');
    }

    /**
     * عرض نموذج تعديل ترخيص
     */
    public function edit(License $license)
    {
        $availableFeatures = $this->getAvailableFeatures();
        $availableModules = $this->getAvailableModules();
        $availableIntegrations = $this->getAvailableIntegrations();

        return view('admin.licenses.edit', compact(
            'license',
            'availableFeatures',
            'availableModules',
            'availableIntegrations'
        ));
    }

    /**
     * تحديث بيانات ترخيص
     */
    public function update(Request $request, License $license)
    {
        $validated = $request->validate([
            'license_type' => ['required', 'string', Rule::unique('licenses')->ignore($license->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_users' => 'required|integer|min:1|max:1000',
            'max_storage_gb' => 'required|integer|min:1|max:1000',
            'max_tenants' => 'required|integer|min:1|max:100',
            'max_api_calls_per_month' => 'required|integer|min:1000',
            'features' => 'required|array|min:1',
            'modules' => 'required|array|min:1',
            'integrations' => 'nullable|array',
            'duration_months' => 'required|integer|min:1|max:60',
            'is_renewable' => 'boolean',
            'auto_renewal' => 'boolean',
            'trial_available' => 'boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'support_level' => 'required|in:basic,standard,premium,enterprise',
            'priority_support' => 'boolean',
            'phone_support' => 'boolean',
            'custom_training' => 'boolean',
            'status' => 'required|in:active,inactive,deprecated',
            'is_public' => 'boolean',
            'requires_approval' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'terms_and_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $license->toArray();
        $license->update($validated);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'update',
            'model_type' => 'License',
            'model_id' => $license->id,
            'description' => "تحديث بيانات الترخيص: {$license->name}",
            'old_values' => $oldValues,
            'new_values' => $license->toArray(),
            'category' => 'license_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return redirect()->route('admin.licenses.show', $license)
                       ->with('success', 'تم تحديث بيانات الترخيص بنجاح');
    }

    /**
     * حذف ترخيص
     */
    public function destroy(License $license)
    {
        // التحقق من عدم وجود مستأجرين يستخدمون هذا الترخيص
        $tenantsCount = $license->tenants()->count();
        if ($tenantsCount > 0) {
            return back()->with('error', "لا يمكن حذف الترخيص لأنه مستخدم من قبل {$tenantsCount} مستأجر");
        }

        // تسجيل العملية في سجل التدقيق قبل الحذف
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'delete',
            'model_type' => 'License',
            'model_id' => $license->id,
            'description' => "حذف الترخيص: {$license->name}",
            'old_values' => $license->toArray(),
            'category' => 'license_management',
            'severity' => 'high',
            'status' => 'success',
        ]);

        $license->delete();

        return redirect()->route('admin.licenses.index')
                       ->with('success', 'تم حذف الترخيص بنجاح');
    }

    /**
     * تفعيل/إلغاء تفعيل ترخيص
     */
    public function toggleStatus(License $license)
    {
        $newStatus = $license->status === 'active' ? 'inactive' : 'active';
        $license->update(['status' => $newStatus]);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'status_change',
            'model_type' => 'License',
            'model_id' => $license->id,
            'description' => "تغيير حالة الترخيص: {$license->name} إلى {$newStatus}",
            'metadata' => ['old_status' => $license->status, 'new_status' => $newStatus],
            'category' => 'license_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        $message = $newStatus === 'active' ? 'تم تفعيل الترخيص' : 'تم إلغاء تفعيل الترخيص';
        return back()->with('success', $message);
    }

    /**
     * إنشاء التراخيص الافتراضية
     */
    public function createDefaults()
    {
        $defaultLicenses = License::getDefaultLicenses();
        $created = 0;

        foreach ($defaultLicenses as $licenseData) {
            if (!License::where('license_type', $licenseData['license_type'])->exists()) {
                License::create($licenseData);
                $created++;
            }
        }

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'create_defaults',
            'description' => "إنشاء {$created} ترخيص افتراضي",
            'metadata' => ['created_count' => $created],
            'category' => 'license_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return back()->with('success', "تم إنشاء {$created} ترخيص افتراضي");
    }

    /**
     * الحصول على المميزات المتاحة
     */
    private function getAvailableFeatures(): array
    {
        return [
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
    }

    /**
     * الحصول على الوحدات المتاحة
     */
    private function getAvailableModules(): array
    {
        return [
            'inventory' => 'المخزون',
            'sales' => 'المبيعات',
            'purchases' => 'المشتريات',
            'reports' => 'التقارير',
            'analytics' => 'التحليلات',
            'api' => 'واجهة برمجة التطبيقات',
            'multi_tenant' => 'متعدد المستأجرين',
            'accounting' => 'المحاسبة',
            'hr' => 'الموارد البشرية',
            'crm' => 'إدارة علاقات العملاء',
        ];
    }

    /**
     * الحصول على التكاملات المتاحة
     */
    private function getAvailableIntegrations(): array
    {
        return [
            'quickbooks' => 'QuickBooks',
            'xero' => 'Xero',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'mailchimp' => 'MailChimp',
            'twilio' => 'Twilio',
            'slack' => 'Slack',
            'zapier' => 'Zapier',
        ];
    }
}
