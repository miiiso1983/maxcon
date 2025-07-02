<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\License;
use App\Models\TenantUser;
use App\Models\User;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class TenantController extends Controller
{
    /**
     * عرض قائمة المستأجرين
     */
    public function index(Request $request)
    {
        $query = Tenant::with(['users']);

        // تطبيق الفلاتر
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $tenants = $query->paginate(15);

        // إحصائيات
        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'pending' => Tenant::where('status', 'pending')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
        ];

        return view('super-admin.tenants.index', compact('tenants', 'stats'));
    }

    /**
     * عرض بيانات دخول المستأجرين
     */
    public function credentials()
    {
        $tenants = Tenant::with(['tenantUsers' => function($query) {
            $query->where('role', 'admin');
        }])->get();

        return view('super-admin.tenants.credentials', compact('tenants'));
    }

    /**
     * عرض نموذج إنشاء مستأجر جديد
     */
    public function create()
    {
        return view('super-admin.tenants.create');
    }

    /**
     * حفظ مستأجر جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'license_type' => 'required|in:basic,premium,enterprise',
            'license_duration' => 'required|integer|min:1|max:36',
            
            // بيانات مدير المستأجر
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:tenant_users,email',
            'manager_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'manager_phone' => 'nullable|string|max:20',
        ], [
            'manager_password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'manager_password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'manager_password.mixed' => 'كلمة المرور يجب أن تحتوي على أحرف كبيرة وصغيرة.',
            'manager_password.numbers' => 'كلمة المرور يجب أن تحتوي على أرقام.',
            'manager_password.symbols' => 'كلمة المرور يجب أن تحتوي على رموز.',
        ]);

        DB::beginTransaction();
        
        try {
            // إنشاء المستأجر
            $tenant = Tenant::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'contact_person' => $request->contact_person,
                'contact_email' => $request->manager_email, // استخدام بريد المدير كبريد الاتصال
                'contact_phone' => $request->manager_phone,
                'address' => $request->address,
                'license_type' => $request->license_type,
                'license_start_date' => now()->toDateString(),
                'license_end_date' => now()->addMonths((int)$request->license_duration)->toDateString(),
                'max_users' => $this->getLicenseLimits($request->license_type)['max_users'],
                'max_storage_gb' => $this->getLicenseLimits($request->license_type)['max_storage_gb'],
                'features' => $this->getLicenseFeatures($request->license_type),
                'monthly_fee' => $this->getLicensePrice($request->license_type),
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'next_billing_date' => now()->addMonths(1)->toDateString(),
                'status' => 'active',
                'is_active' => true,
                'settings' => [
                    'timezone' => 'Asia/Riyadh',
                    'currency' => 'SAR',
                    'language' => 'ar',
                ],
            ]);

            // إنشاء الترخيص (إذا كان جدول منفصل مطلوب)
            $licenseData = $this->getLicenseData($request->license_type);
            $license = License::create([
                'license_type' => $request->license_type,
                'name' => $licenseData['name'],
                'description' => $licenseData['description'],
                'max_users' => $licenseData['max_users'],
                'max_storage_gb' => $licenseData['max_storage_gb'],
                'max_tenants' => 1,
                'max_api_calls_per_month' => $licenseData['max_api_calls'],
                'features' => $licenseData['features'],
                'modules' => $licenseData['modules'],
                'duration_months' => (int)$request->license_duration,
                'price_monthly' => $licenseData['price_monthly'],
                'price_yearly' => $licenseData['price_yearly'],
                'currency' => 'SAR',
                'support_level' => $licenseData['support_level'],
                'status' => 'active',
            ]);

            // إنشاء مدير المستأجر في جدول tenant_users
            $manager = TenantUser::create([
                'tenant_id' => $tenant->id,
                'name' => $request->manager_name,
                'email' => $request->manager_email,
                'password' => Hash::make($request->manager_password),
                'phone' => $request->manager_phone,
                'role' => 'admin',
                'status' => 'active',
                'permissions' => ['*'], // جميع الصلاحيات
            ]);

            // إنشاء نفس المستخدم في جدول users العادي للدخول
            User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->manager_name,
                'email' => $request->manager_email,
                'password' => Hash::make($request->manager_password),
                'phone' => $request->manager_phone,
                'user_type' => 'admin',
                'status' => 'active',
            ]);

            // تسجيل العملية في سجل التدقيق
            AdminAuditLog::createLog([
                'super_admin_id' => Auth::guard('super_admin')->id(),
                'tenant_id' => $tenant->id,
                'action' => 'tenant_created',
                'description' => "تم إنشاء مستأجر جديد: {$tenant->name}",
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name,
                    'license_type' => $request->license_type,
                    'license_duration' => $request->license_duration,
                    'manager_email' => $request->manager_email,
                ],
                'category' => 'tenant_management',
                'severity' => 'medium',
                'status' => 'success',
            ]);

            DB::commit();

            return redirect()->route('super-admin.tenants.index')
                ->with('success', 'تم إنشاء المستأجر بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            
            // تسجيل الخطأ
            AdminAuditLog::createLog([
                'super_admin_id' => Auth::guard('super_admin')->id(),
                'action' => 'tenant_creation_failed',
                'description' => "فشل في إنشاء مستأجر: {$request->name}",
                'metadata' => [
                    'error' => $e->getMessage(),
                    'tenant_name' => $request->name,
                ],
                'category' => 'tenant_management',
                'severity' => 'high',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المستأجر: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل مستأجر
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['users']);
        
        // إحصائيات المستأجر
        $stats = [
            'users_count' => $tenant->users()->count(),
            'active_users' => $tenant->users()->where('status', 'active')->count(),
            'last_login' => null, // سيتم إضافة هذا لاحقاً عند إضافة حقل last_login_at
        ];

        return view('super-admin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * عرض نموذج تعديل مستأجر
     */
    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    /**
     * تحديث بيانات مستأجر
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'status' => 'required|in:active,suspended,pending',
        ]);

        $oldValues = $tenant->toArray();

        $tenant->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        // تسجيل العملية
        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'tenant_id' => $tenant->id,
            'action' => 'tenant_updated',
            'description' => "تم تحديث بيانات المستأجر: {$tenant->name}",
            'metadata' => [
                'tenant_id' => $tenant->id,
                'old_values' => $oldValues,
                'new_values' => $tenant->fresh()->toArray(),
            ],
            'category' => 'tenant_management',
            'severity' => 'low',
            'status' => 'success',
        ]);

        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('success', 'تم تحديث بيانات المستأجر بنجاح');
    }

    /**
     * حذف مستأجر
     */
    public function destroy(Tenant $tenant)
    {
        if ($tenant->status === 'active') {
            return back()->with('error', 'لا يمكن حذف مستأجر نشط. يجب تعليقه أولاً.');
        }

        $tenantName = $tenant->name;
        $tenantId = $tenant->id;

        // حذف المستأجر (سيحذف المستخدمين والترخيص تلقائياً)
        $tenant->delete();

        // تسجيل العملية
        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'action' => 'tenant_deleted',
            'description' => "تم حذف المستأجر: {$tenantName}",
            'metadata' => [
                'tenant_id' => $tenantId,
                'tenant_name' => $tenantName,
            ],
            'category' => 'tenant_management',
            'severity' => 'high',
            'status' => 'success',
        ]);

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'تم حذف المستأجر بنجاح');
    }

    /**
     * تفعيل مستأجر
     */
    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);

        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'tenant_id' => $tenant->id,
            'action' => 'tenant_activated',
            'description' => "تم تفعيل المستأجر: {$tenant->name}",
            'category' => 'tenant_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return back()->with('success', 'تم تفعيل المستأجر بنجاح');
    }

    /**
     * تعطيل مستأجر
     */
    public function deactivate(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);

        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'tenant_id' => $tenant->id,
            'action' => 'tenant_deactivated',
            'description' => "تم تعطيل المستأجر: {$tenant->name}",
            'category' => 'tenant_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return back()->with('success', 'تم تعطيل المستأجر بنجاح');
    }

    /**
     * الحصول على مميزات الترخيص حسب النوع
     */
    private function getLicenseFeatures(string $type): array
    {
        $features = [
            'basic' => [
                'inventory_management',
                'sales_management',
                'basic_reports',
            ],
            'premium' => [
                'inventory_management',
                'sales_management',
                'financial_reports',
                'customer_management',
                'employee_management',
            ],
            'enterprise' => [
                'inventory_management',
                'sales_management',
                'financial_reports',
                'customer_management',
                'employee_management',
                'api_access',
                'advanced_reports',
                'multi_branch',
            ],
        ];

        return $features[$type] ?? $features['basic'];
    }

    /**
     * الحصول على حدود الترخيص حسب النوع
     */
    private function getLicenseLimits(string $type): array
    {
        $limits = [
            'basic' => [
                'max_users' => 5,
                'max_storage_gb' => 2,
                'max_branches' => 1,
            ],
            'premium' => [
                'max_users' => 20,
                'max_storage_gb' => 10,
                'max_branches' => 5,
            ],
            'enterprise' => [
                'max_users' => 1000,
                'max_storage_gb' => 100,
                'max_branches' => 100,
            ],
        ];

        return $limits[$type] ?? $limits['basic'];
    }

    /**
     * الحصول على سعر الترخيص حسب النوع
     */
    private function getLicensePrice(string $type): float
    {
        $prices = [
            'basic' => 99.00,
            'premium' => 299.00,
            'enterprise' => 999.00,
        ];

        return $prices[$type] ?? $prices['basic'];
    }

    /**
     * الحصول على بيانات الترخيص الكاملة حسب النوع
     */
    private function getLicenseData(string $type): array
    {
        $licenseData = [
            'basic' => [
                'name' => 'الترخيص الأساسي',
                'description' => 'ترخيص أساسي للصيدليات الصغيرة',
                'max_users' => 5,
                'max_storage_gb' => 2,
                'max_api_calls' => 1000,
                'features' => ['inventory_management', 'sales_management', 'basic_reports'],
                'modules' => ['inventory', 'sales', 'reports'],
                'price_monthly' => 99.00,
                'price_yearly' => 990.00,
                'support_level' => 'basic',
            ],
            'premium' => [
                'name' => 'الترخيص المميز',
                'description' => 'ترخيص متقدم للصيدليات المتوسطة',
                'max_users' => 20,
                'max_storage_gb' => 10,
                'max_api_calls' => 10000,
                'features' => ['inventory_management', 'sales_management', 'financial_reports', 'customer_management', 'employee_management'],
                'modules' => ['inventory', 'sales', 'reports', 'customers', 'employees'],
                'price_monthly' => 299.00,
                'price_yearly' => 2990.00,
                'support_level' => 'standard',
            ],
            'enterprise' => [
                'name' => 'الترخيص المؤسسي',
                'description' => 'ترخيص شامل للصيدليات الكبيرة والسلاسل',
                'max_users' => 1000,
                'max_storage_gb' => 100,
                'max_api_calls' => 100000,
                'features' => ['inventory_management', 'sales_management', 'financial_reports', 'customer_management', 'employee_management', 'api_access', 'advanced_reports', 'multi_branch'],
                'modules' => ['inventory', 'sales', 'reports', 'customers', 'employees', 'api', 'analytics', 'branches'],
                'price_monthly' => 999.00,
                'price_yearly' => 9990.00,
                'support_level' => 'enterprise',
            ],
        ];

        return $licenseData[$type] ?? $licenseData['basic'];
    }
}
