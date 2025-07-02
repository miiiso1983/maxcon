<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Tenant;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * عرض قائمة سجلات التدقيق
     */
    public function index(Request $request)
    {
        $query = AdminAuditLog::with(['superAdmin', 'tenant']);

        // تطبيق الفلاتر
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('super_admin_id')) {
            $query->where('super_admin_id', $request->super_admin_id);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات
        $stats = [
            'total' => AdminAuditLog::count(),
            'today' => AdminAuditLog::whereDate('created_at', today())->count(),
            'this_week' => AdminAuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'failed' => AdminAuditLog::where('status', 'failed')->count(),
        ];

        // بيانات للفلاتر
        $categories = AdminAuditLog::distinct()->pluck('category')->filter();
        $superAdmins = SuperAdmin::select('id', 'name')->get();
        $tenants = Tenant::select('id', 'name')->get();

        return view('super-admin.audit-logs.index', compact('logs', 'stats', 'categories', 'superAdmins', 'tenants'));
    }

    /**
     * عرض تفاصيل سجل تدقيق
     */
    public function show(AdminAuditLog $auditLog)
    {
        $auditLog->load(['superAdmin', 'tenant']);
        return view('super-admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * حذف سجل تدقيق
     */
    public function destroy(AdminAuditLog $auditLog)
    {
        $auditLog->delete();

        // تسجيل عملية الحذف
        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'action' => 'audit_log_deleted',
            'description' => "تم حذف سجل تدقيق: {$auditLog->action}",
            'metadata' => [
                'deleted_log_id' => $auditLog->id,
                'deleted_log_action' => $auditLog->action,
            ],
            'category' => 'system_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return redirect()->route('super-admin.audit-logs.index')
            ->with('success', 'تم حذف سجل التدقيق بنجاح');
    }

    /**
     * حذف سجلات قديمة
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365',
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = AdminAuditLog::where('created_at', '<', $cutoffDate)->delete();

        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'action' => 'audit_logs_cleanup',
            'description' => "تم حذف {$deletedCount} سجل تدقيق أقدم من {$request->days} يوم",
            'metadata' => [
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate,
                'days' => $request->days,
            ],
            'category' => 'system_management',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return back()->with('success', "تم حذف {$deletedCount} سجل تدقيق بنجاح");
    }

    /**
     * تصدير سجلات التدقيق
     */
    public function export(Request $request)
    {
        $query = AdminAuditLog::with(['superAdmin', 'tenant']);

        // تطبيق نفس الفلاتر
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_logs_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");
            
            // العناوين
            fputcsv($file, [
                'التاريخ',
                'السوبر أدمن',
                'المستأجر',
                'الإجراء',
                'الوصف',
                'الفئة',
                'الخطورة',
                'الحالة',
                'عنوان IP',
            ]);

            // البيانات
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->superAdmin->name ?? 'غير محدد',
                    $log->tenant->name ?? 'غير محدد',
                    $log->action,
                    $log->description,
                    $log->category,
                    $log->severity,
                    $log->status,
                    $log->ip_address,
                ]);
            }

            fclose($file);
        };

        // تسجيل عملية التصدير
        AdminAuditLog::createLog([
            'super_admin_id' => Auth::guard('super_admin')->id(),
            'action' => 'audit_logs_exported',
            'description' => "تم تصدير {$logs->count()} سجل تدقيق",
            'metadata' => [
                'exported_count' => $logs->count(),
                'filename' => $filename,
                'filters' => $request->only(['category', 'severity', 'status', 'date_from', 'date_to']),
            ],
            'category' => 'system_management',
            'severity' => 'low',
            'status' => 'success',
        ]);

        return response()->stream($callback, 200, $headers);
    }

    /**
     * عرض إحصائيات مفصلة
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => AdminAuditLog::count(),
            'logs_today' => AdminAuditLog::whereDate('created_at', today())->count(),
            'logs_this_week' => AdminAuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'logs_this_month' => AdminAuditLog::whereMonth('created_at', now()->month)->count(),
            
            'by_category' => AdminAuditLog::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
                
            'by_severity' => AdminAuditLog::selectRaw('severity, COUNT(*) as count')
                ->groupBy('severity')
                ->pluck('count', 'severity'),
                
            'by_status' => AdminAuditLog::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
                
            'by_super_admin' => AdminAuditLog::with('superAdmin')
                ->selectRaw('super_admin_id, COUNT(*) as count')
                ->groupBy('super_admin_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->superAdmin->name ?? 'غير محدد' => $item->count];
                }),
                
            'recent_activities' => AdminAuditLog::with(['superAdmin', 'tenant'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('super-admin.audit-logs.statistics', compact('stats'));
    }
}
