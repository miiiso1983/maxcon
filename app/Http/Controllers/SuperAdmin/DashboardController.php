<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\License;
use App\Models\TenantUser;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * عرض لوحة التحكم الرئيسية
     */
    public function index()
    {
        // التأكد من تسجيل الدخول
        if (!Auth::guard('super_admin')->check()) {
            return redirect()->route('super-admin.login');
        }

        // جمع الإحصائيات
        $stats = $this->getStats();

        // تسجيل زيارة لوحة التحكم
        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'action' => 'dashboard_view',
            'description' => 'عرض لوحة التحكم الرئيسية',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'category' => 'navigation',
            'severity' => 'low',
            'status' => 'success',
        ]);

        return view('super-admin.dashboard', compact('stats'));
    }

    /**
     * جمع إحصائيات النظام
     */
    private function getStats()
    {
        try {
            $stats = [
                'tenants' => Tenant::where('status', 'active')->count(),
                'licenses' => License::where('status', 'active')->count(),
                'users' => TenantUser::where('status', 'active')->count(),
                'logs' => AdminAuditLog::whereDate('created_at', today())->count(),
            ];

            // إحصائيات إضافية
            $stats['total_tenants'] = Tenant::count();
            $stats['expired_licenses'] = Tenant::where('license_end_date', '<', now())->count();
            $stats['pending_tenants'] = Tenant::where('status', 'pending')->count();
            $stats['recent_logins'] = AdminAuditLog::where('action', 'super_admin_login')
                ->whereDate('created_at', today())
                ->count();

            return $stats;
        } catch (\Exception $e) {
            // في حالة وجود خطأ، إرجاع قيم افتراضية
            return [
                'tenants' => 0,
                'licenses' => 0,
                'users' => 0,
                'logs' => 0,
                'total_tenants' => 0,
                'expired_licenses' => 0,
                'pending_tenants' => 0,
                'recent_logins' => 0,
            ];
        }
    }

    /**
     * الحصول على إحصائيات مفصلة عبر AJAX
     */
    public function getDetailedStats(Request $request)
    {
        if (!Auth::guard('super_admin')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $stats = $this->getStats();

            // إحصائيات المستأجرين حسب الحالة
            $tenantsByStatus = Tenant::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // إحصائيات التراخيص حسب النوع
            $licensesByType = License::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();

            // آخر الأنشطة
            $recentActivities = AdminAuditLog::with('superAdmin')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        'description' => $log->description,
                        'admin_name' => $log->superAdmin->name ?? 'غير محدد',
                        'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                        'severity' => $log->severity,
                        'status' => $log->status,
                    ];
                });

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'tenants_by_status' => $tenantsByStatus,
                'licenses_by_type' => $licensesByType,
                'recent_activities' => $recentActivities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في جمع الإحصائيات'
            ], 500);
        }
    }

    /**
     * تصدير تقرير شامل
     */
    public function exportReport(Request $request)
    {
        if (!Auth::guard('super_admin')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $stats = $this->getStats();
            
            // تسجيل عملية التصدير
            AdminAuditLog::createLog([
                'super_admin_id' => Auth::guard('super_admin')->id(),
                'action' => 'export_dashboard_report',
                'description' => 'تصدير تقرير لوحة التحكم',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'category' => 'export',
                'severity' => 'medium',
                'status' => 'success',
            ]);

            $reportData = [
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'generated_by' => Auth::guard('super_admin')->user()->name,
                'statistics' => $stats,
                'tenants' => Tenant::get()->map(function ($tenant) {
                    return [
                        'name' => $tenant->name,
                        'status' => $tenant->status,
                        'created_at' => $tenant->created_at->format('Y-m-d'),
                        'license_type' => $tenant->license_type ?? 'غير محدد',
                        'license_expires_at' => $tenant->license_end_date ?? 'غير محدد',
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'report' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في تصدير التقرير'
            ], 500);
        }
    }

    /**
     * البحث في النظام
     */
    public function search(Request $request)
    {
        if (!Auth::guard('super_admin')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال كلمة البحث'
            ]);
        }

        try {
            $results = [
                'tenants' => Tenant::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'status']),
                
                'users' => TenantUser::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->with('tenant:id,name')
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'tenant_id', 'status']),
                
                'logs' => AdminAuditLog::where('description', 'LIKE', "%{$query}%")
                    ->orWhere('action', 'LIKE', "%{$query}%")
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'action', 'description', 'created_at', 'severity']),
            ];

            return response()->json([
                'success' => true,
                'results' => $results,
                'query' => $query
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في البحث'
            ], 500);
        }
    }
}
