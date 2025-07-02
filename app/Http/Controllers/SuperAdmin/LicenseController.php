<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Tenant;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LicenseController extends Controller
{
    /**
     * عرض قائمة التراخيص
     */
    public function index(Request $request)
    {
        $query = License::with('tenant');

        // تطبيق الفلاتر
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $licenses = $query->paginate(15);

        // إحصائيات
        $stats = [
            'total' => License::count(),
            'active' => License::where('status', 'active')->count(),
            'expired' => Tenant::where('license_end_date', '<', now())->count(),
            'expiring_soon' => Tenant::where('license_end_date', '>', now())
                ->where('license_end_date', '<=', now()->addDays(30))
                ->count(),
        ];

        return view('super-admin.licenses.index', compact('licenses', 'stats'));
    }

    /**
     * عرض نموذج إنشاء ترخيص جديد
     */
    public function create()
    {
        $tenants = Tenant::where('status', 'active')->get();
        return view('super-admin.licenses.create', compact('tenants'));
    }

    /**
     * حفظ ترخيص جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'type' => 'required|in:basic,premium,enterprise',
            'starts_at' => 'required|date',
            'duration_months' => 'required|integer|min:1|max:36',
            'features' => 'array',
            'max_users' => 'required|integer|min:1',
            'max_storage_gb' => 'required|integer|min:1',
            'max_branches' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            // تحديث بيانات المستأجر بدلاً من إنشاء ترخيص منفصل
            $tenant = Tenant::findOrFail($request->tenant_id);
            $tenant->update([
                'license_type' => $request->type,
                'license_start_date' => $request->starts_at,
                'license_end_date' => now()->parse($request->starts_at)->addMonths((int)$request->duration_months)->toDateString(),
                'max_users' => $request->max_users,
                'max_storage_gb' => $request->max_storage_gb,
                'features' => $request->features ?? [],
            ]);

            $license = $tenant; // للتوافق مع باقي الكود

            // تسجيل العملية
            AdminAuditLog::createLog([
                'super_admin_id' => Auth::guard('super_admin')->id(),
                'tenant_id' => $request->tenant_id,
                'action' => 'license_created',
                'description' => "تم إنشاء ترخيص جديد: {$request->type}",
                'metadata' => [
                    'license_id' => $license->id,
                    'tenant_id' => $request->tenant_id,
                    'license_type' => $request->type,
                    'duration_months' => $request->duration_months,
                ],
                'category' => 'license_management',
                'severity' => 'medium',
                'status' => 'success',
            ]);

            DB::commit();

            return redirect()->route('super-admin.licenses.index')
                ->with('success', 'تم إنشاء الترخيص بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            
            AdminAuditLog::createLog([
                'super_admin_id' => Auth::guard('super_admin')->id(),
                'action' => 'license_creation_failed',
                'description' => "فشل في إنشاء ترخيص",
                'metadata' => [
                    'error' => $e->getMessage(),
                    'tenant_id' => $request->tenant_id,
                ],
                'category' => 'license_management',
                'severity' => 'high',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الترخيص: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل ترخيص
     */
    public function show(License $license)
    {
        $license->load('tenant');
        return view('super-admin.licenses.show', compact('license'));
    }

    /**
     * عرض نموذج تعديل ترخيص
     */
    public function edit(License $license)
    {
        $tenants = Tenant::where('status', 'active')->get();
        return view('super-admin.licenses.edit', compact('license', 'tenants'));
    }

    /**
     * تحديث ترخيص
     */
    public function update(Request $request, License $license)
    {
        $request->validate([
            'type' => 'required|in:basic,premium,enterprise',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'status' => 'required|in:active,expired,suspended',
            'features' => 'array',
            'max_users' => 'required|integer|min:1',
            'max_storage_gb' => 'required|integer|min:1',
            'max_branches' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldValues = $license->toArray();

        $license->update([
            'type' => $request->type,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'features' => $request->features ?? [],
            'limits' => [
                'max_users' => $request->max_users,
                'max_storage_gb' => $request->max_storage_gb,
                'max_branches' => $request->max_branches,
            ],
            'notes' => $request->notes,
        ]);

        // تسجيل العملية
        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'tenant_id' => $license->tenant_id,
            'action' => 'license_updated',
            'description' => "تم تحديث الترخيص: {$license->type}",
            'metadata' => [
                'license_id' => $license->id,
                'old_values' => $oldValues,
                'new_values' => $license->fresh()->toArray(),
            ],
            'category' => 'license_management',
            'severity' => 'low',
            'status' => 'success',
        ]);

        return redirect()->route('super-admin.licenses.show', $license)
            ->with('success', 'تم تحديث الترخيص بنجاح');
    }

    /**
     * حذف ترخيص
     */
    public function destroy(License $license)
    {
        if ($license->status === 'active') {
            return back()->with('error', 'لا يمكن حذف ترخيص نشط. يجب تعليقه أولاً.');
        }

        $licenseData = $license->toArray();
        $license->delete();

        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'tenant_id' => $license->tenant_id,
            'action' => 'license_deleted',
            'description' => "تم حذف الترخيص: {$license->type}",
            'metadata' => [
                'license_data' => $licenseData,
            ],
            'category' => 'license_management',
            'severity' => 'high',
            'status' => 'success',
        ]);

        return redirect()->route('super-admin.licenses.index')
            ->with('success', 'تم حذف الترخيص بنجاح');
    }

    /**
     * تجديد ترخيص
     */
    public function renew(Request $request, $tenantId)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:36',
        ]);

        $tenant = Tenant::findOrFail($tenantId);
        $oldExpiryDate = $tenant->license_end_date;
        $newExpiryDate = now()->parse($tenant->license_end_date)->addMonths((int)$request->months)->toDateString();

        $tenant->update([
            'license_end_date' => $newExpiryDate,
            'status' => 'active',
        ]);

        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'tenant_id' => $tenant->id,
            'action' => 'license_renewed',
            'description' => "تم تجديد الترخيص لـ {$request->months} شهر",
            'metadata' => [
                'tenant_id' => $tenant->id,
                'old_expiry_date' => $oldExpiryDate,
                'new_expiry_date' => $newExpiryDate,
                'months_added' => $request->months,
            ],
            'category' => 'license_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تجديد الترخيص بنجاح'
        ]);
    }

    /**
     * تعليق ترخيص
     */
    public function suspend(License $license)
    {
        $license->update(['status' => 'suspended']);

        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'tenant_id' => $license->tenant_id,
            'action' => 'license_suspended',
            'description' => "تم تعليق الترخيص: {$license->type}",
            'metadata' => [
                'license_id' => $license->id,
            ],
            'category' => 'license_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعليق الترخيص بنجاح'
        ]);
    }

    /**
     * عرض التراخيص المنتهية الصلاحية
     */
    public function expired()
    {
        $tenants = Tenant::where('license_end_date', '<', now())->paginate(15);
        return view('super-admin.licenses.expired', compact('tenants'));
    }

    /**
     * عرض التراخيص التي تنتهي قريباً
     */
    public function expiringSoon()
    {
        $tenants = Tenant::where('license_end_date', '>', now())
            ->where('license_end_date', '<=', now()->addDays(30))
            ->paginate(15);
        return view('super-admin.licenses.expiring-soon', compact('tenants'));
    }
}
