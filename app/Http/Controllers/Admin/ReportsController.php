<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * عرض صفحة التقارير الرئيسية
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * تقرير المستأجرين
     */
    public function tenants(Request $request)
    {
        $tenants = Tenant::with(['tenantUsers'])
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->license_type, function($query, $type) {
                return $query->where('license_type', $type);
            })
            ->paginate(20);

        return view('admin.reports.tenants', compact('tenants'));
    }

    /**
     * تقرير الإيرادات
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', '30');
        
        $revenueData = Tenant::selectRaw('
            license_type,
            COUNT(*) as tenant_count,
            SUM(monthly_fee) as total_revenue
        ')
        ->where('created_at', '>=', now()->subDays($period))
        ->groupBy('license_type')
        ->get();

        return view('admin.reports.revenue', compact('revenueData', 'period'));
    }

    /**
     * تقرير الاستخدام
     */
    public function usage(Request $request)
    {
        $usageStats = Tenant::selectRaw('
            name,
            current_users_count,
            max_users,
            current_storage_gb,
            max_storage_gb,
            (current_users_count / max_users * 100) as users_percentage,
            (current_storage_gb / max_storage_gb * 100) as storage_percentage
        ')
        ->where('status', 'active')
        ->get();

        return view('admin.reports.usage', compact('usageStats'));
    }

    /**
     * تقرير الأمان
     */
    public function security(Request $request)
    {
        $period = $request->get('period', '7');
        
        $securityLogs = AdminAuditLog::with('superAdmin')
            ->where('created_at', '>=', now()->subDays($period))
            ->where('severity', 'high')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.reports.security', compact('securityLogs', 'period'));
    }

    /**
     * إنشاء تقرير مخصص
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:tenants,revenue,usage,security',
            'period' => 'required|integer|min:1|max:365',
            'format' => 'required|in:html,pdf,excel'
        ]);

        // TODO: تنفيذ منطق إنشاء التقارير
        
        return response()->json([
            'success' => true,
            'message' => 'سيتم إنشاء التقرير قريباً'
        ]);
    }

    /**
     * تحميل تقرير
     */
    public function download($reportId)
    {
        // TODO: تنفيذ منطق تحميل التقارير
        
        return response()->json([
            'success' => false,
            'message' => 'ميزة التحميل قيد التطوير'
        ]);
    }
}
