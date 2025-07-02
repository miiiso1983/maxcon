<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use App\Models\AdminAuditLog;
use App\Services\PasswordPolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    protected $passwordPolicyService;

    public function __construct(PasswordPolicyService $passwordPolicyService)
    {
        // Middleware is handled in routes
        $this->passwordPolicyService = $passwordPolicyService;
    }

    /**
     * عرض قائمة السوبر أدمن
     */
    public function index(Request $request)
    {
        $query = SuperAdmin::query();

        // تطبيق الفلاتر
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $superAdmins = $query->paginate(15);

        // إحصائيات سريعة
        $stats = [
            'total' => SuperAdmin::count(),
            'active' => SuperAdmin::active()->count(),
            'super_admins' => SuperAdmin::superAdmins()->count(),
            'with_2fa' => SuperAdmin::where('two_factor_enabled', true)->count(),
        ];

        return view('admin.super-admins.index', compact('superAdmins', 'stats'));
    }

    /**
     * عرض تفاصيل سوبر أدمن
     */
    public function show(SuperAdmin $superAdmin)
    {
        $superAdmin->load(['auditLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // إحصائيات المستخدم
        $stats = [
            'total_logins' => AdminAuditLog::where('super_admin_id', $superAdmin->id)
                ->where('action', 'login')
                ->where('status', 'success')
                ->count(),
            'failed_logins' => AdminAuditLog::where('super_admin_id', $superAdmin->id)
                ->where('action', 'login_failed')
                ->count(),
            'last_activity' => $superAdmin->last_login_at,
            'account_age' => $superAdmin->created_at->diffInDays(now()),
        ];

        return view('admin.super-admins.show', compact('superAdmin', 'stats'));
    }

    /**
     * عرض نموذج إنشاء سوبر أدمن جديد
     */
    public function create()
    {
        $roles = [
            'super_admin' => 'سوبر أدمن',
            'admin' => 'أدمن',
            'support' => 'دعم فني',
        ];

        $permissions = [
            'create_tenants' => 'إنشاء مستأجرين',
            'delete_tenants' => 'حذف مستأجرين',
            'manage_licenses' => 'إدارة التراخيص',
            'view_all_data' => 'عرض جميع البيانات',
            'system_settings' => 'إعدادات النظام',
            'audit_logs' => 'سجلات التدقيق',
        ];

        return view('admin.super-admins.create', compact('roles', 'permissions'));
    }

    /**
     * حفظ سوبر أدمن جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:super_admins,email',
            'password' => $this->passwordPolicyService->getLaravelValidationRules(),
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:super_admin,admin,support',
            'status' => 'required|in:active,inactive,suspended',
            'can_create_tenants' => 'boolean',
            'can_delete_tenants' => 'boolean',
            'can_manage_licenses' => 'boolean',
            'can_view_all_data' => 'boolean',
            'permissions' => 'nullable|array',
            'locale' => 'required|in:ar,en',
            'timezone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // التحقق من سياسة كلمة المرور
        $passwordValidation = $this->passwordPolicyService->validatePassword(
            $validated['password'],
            ['email' => $validated['email'], 'name' => $validated['name']]
        );

        if (!$passwordValidation['valid']) {
            return back()->withInput()
                        ->withErrors(['password' => $passwordValidation['errors']]);
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['password_changed_at'] = now();

        $superAdmin = SuperAdmin::create($validated);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'create',
            'model_type' => 'SuperAdmin',
            'model_id' => $superAdmin->id,
            'description' => "إنشاء حساب سوبر أدمن جديد: {$superAdmin->name}",
            'new_values' => $superAdmin->toArray(),
            'category' => 'user_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return redirect()->route('admin.super-admins.show', $superAdmin)
                       ->with('success', 'تم إنشاء حساب السوبر أدمن بنجاح');
    }

    /**
     * عرض نموذج تعديل سوبر أدمن
     */
    public function edit(SuperAdmin $superAdmin)
    {
        // منع تعديل السوبر أدمن الرئيسي من قبل غير السوبر أدمن
        if ($superAdmin->role === 'super_admin' && !auth('super_admin')->user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بتعديل حساب السوبر أدمن');
        }

        $roles = [
            'super_admin' => 'سوبر أدمن',
            'admin' => 'أدمن',
            'support' => 'دعم فني',
        ];

        $permissions = [
            'create_tenants' => 'إنشاء مستأجرين',
            'delete_tenants' => 'حذف مستأجرين',
            'manage_licenses' => 'إدارة التراخيص',
            'view_all_data' => 'عرض جميع البيانات',
            'system_settings' => 'إعدادات النظام',
            'audit_logs' => 'سجلات التدقيق',
        ];

        return view('admin.super-admins.edit', compact('superAdmin', 'roles', 'permissions'));
    }

    /**
     * تحديث بيانات سوبر أدمن
     */
    public function update(Request $request, SuperAdmin $superAdmin)
    {
        // منع تعديل السوبر أدمن الرئيسي
        if ($superAdmin->role === 'super_admin' && !auth('super_admin')->user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بتعديل حساب السوبر أدمن');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('super_admins')->ignore($superAdmin->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:super_admin,admin,support',
            'status' => 'required|in:active,inactive,suspended',
            'can_create_tenants' => 'boolean',
            'can_delete_tenants' => 'boolean',
            'can_manage_licenses' => 'boolean',
            'can_view_all_data' => 'boolean',
            'permissions' => 'nullable|array',
            'locale' => 'required|in:ar,en',
            'timezone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $superAdmin->toArray();
        $superAdmin->update($validated);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'update',
            'model_type' => 'SuperAdmin',
            'model_id' => $superAdmin->id,
            'description' => "تحديث بيانات السوبر أدمن: {$superAdmin->name}",
            'old_values' => $oldValues,
            'new_values' => $superAdmin->toArray(),
            'category' => 'user_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return redirect()->route('admin.super-admins.show', $superAdmin)
                       ->with('success', 'تم تحديث بيانات السوبر أدمن بنجاح');
    }

    /**
     * تفعيل/إلغاء تفعيل سوبر أدمن
     */
    public function toggleStatus(SuperAdmin $superAdmin)
    {
        // منع تعطيل السوبر أدمن الرئيسي
        if ($superAdmin->role === 'super_admin' && !auth('super_admin')->user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بتعطيل حساب السوبر أدمن');
        }

        $newStatus = $superAdmin->status === 'active' ? 'inactive' : 'active';
        $superAdmin->update(['status' => $newStatus]);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'status_change',
            'model_type' => 'SuperAdmin',
            'model_id' => $superAdmin->id,
            'description' => "تغيير حالة السوبر أدمن: {$superAdmin->name} إلى {$newStatus}",
            'metadata' => ['old_status' => $superAdmin->status, 'new_status' => $newStatus],
            'category' => 'user_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        $message = $newStatus === 'active' ? 'تم تفعيل الحساب' : 'تم إلغاء تفعيل الحساب';
        return back()->with('success', $message);
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(Request $request, SuperAdmin $superAdmin)
    {
        $request->validate([
            'new_password' => $this->passwordPolicyService->getLaravelValidationRules(),
        ]);

        // التحقق من سياسة كلمة المرور
        $passwordValidation = $this->passwordPolicyService->validatePassword(
            $request->new_password,
            ['email' => $superAdmin->email, 'name' => $superAdmin->name]
        );

        if (!$passwordValidation['valid']) {
            return back()->withErrors(['new_password' => $passwordValidation['errors']]);
        }

        $superAdmin->update([
            'password' => Hash::make($request->new_password),
            'password_changed_at' => now(),
        ]);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'password_reset',
            'model_type' => 'SuperAdmin',
            'model_id' => $superAdmin->id,
            'description' => "إعادة تعيين كلمة مرور السوبر أدمن: {$superAdmin->name}",
            'category' => 'user_management',
            'severity' => 'high',
            'status' => 'success',
        ]);

        return back()->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
    }

    /**
     * إلغاء قفل الحساب
     */
    public function unlock(SuperAdmin $superAdmin)
    {
        $superAdmin->unlockAccount();

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'account_unlocked',
            'model_type' => 'SuperAdmin',
            'model_id' => $superAdmin->id,
            'description' => "إلغاء قفل حساب السوبر أدمن: {$superAdmin->name}",
            'category' => 'user_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return back()->with('success', 'تم إلغاء قفل الحساب بنجاح');
    }

    /**
     * حذف سوبر أدمن
     */
    public function destroy(SuperAdmin $superAdmin)
    {
        // منع حذف السوبر أدمن الرئيسي
        if ($superAdmin->role === 'super_admin') {
            return back()->with('error', 'لا يمكن حذف حساب السوبر أدمن الرئيسي');
        }

        // منع حذف النفس
        if ($superAdmin->id === auth('super_admin')->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        // تسجيل العملية في سجل التدقيق قبل الحذف
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'delete',
            'model_type' => 'SuperAdmin',
            'model_id' => $superAdmin->id,
            'description' => "حذف حساب السوبر أدمن: {$superAdmin->name}",
            'old_values' => $superAdmin->toArray(),
            'category' => 'user_management',
            'severity' => 'high',
            'status' => 'success',
        ]);

        $superAdmin->delete();

        return redirect()->route('admin.super-admins.index')
                       ->with('success', 'تم حذف حساب السوبر أدمن بنجاح');
    }
}
