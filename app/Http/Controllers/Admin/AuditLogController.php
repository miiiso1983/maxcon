<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\SuperAdmin;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditLogController extends Controller
{
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * عرض قائمة سجلات التدقيق
     */
    public function index(Request $request)
    {
        $query = AdminAuditLog::with(['superAdmin', 'tenant']);

        // تطبيق الفلاتر
        if ($request->filled('super_admin_id')) {
            $query->where('super_admin_id', $request->super_admin_id);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $auditLogs = $query->paginate(25);

        // بيانات للفلاتر
        $superAdmins = SuperAdmin::select('id', 'name')->get();
        $tenants = Tenant::select('id', 'name')->get();
        
        $actions = AdminAuditLog::distinct()->pluck('action')->filter()->sort();
        $categories = AdminAuditLog::distinct()->pluck('category')->filter()->sort();

        // إحصائيات سريعة
        $stats = [
            'total' => AdminAuditLog::count(),
            'today' => AdminAuditLog::today()->count(),
            'this_week' => AdminAuditLog::thisWeek()->count(),
            'this_month' => AdminAuditLog::thisMonth()->count(),
            'critical' => AdminAuditLog::bySeverity('critical')->count(),
            'high' => AdminAuditLog::bySeverity('high')->count(),
            'failed' => AdminAuditLog::where('status', 'failed')->count(),
        ];

        return view('admin.audit-logs.index', compact(
            'auditLogs', 
            'superAdmins', 
            'tenants', 
            'actions', 
            'categories', 
            'stats'
        ));
    }

    /**
     * عرض تفاصيل سجل تدقيق
     */
    public function show(AdminAuditLog $auditLog)
    {
        $auditLog->load(['superAdmin', 'tenant']);

        // سجلات مشابهة
        $relatedLogs = AdminAuditLog::where('id', '!=', $auditLog->id)
            ->where(function ($query) use ($auditLog) {
                $query->where('super_admin_id', $auditLog->super_admin_id)
                      ->orWhere('ip_address', $auditLog->ip_address)
                      ->orWhere('action', $auditLog->action);
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.audit-logs.show', compact('auditLog', 'relatedLogs'));
    }

    /**
     * حذف سجل تدقيق
     */
    public function destroy(AdminAuditLog $auditLog)
    {
        // تسجيل عملية الحذف
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'delete_audit_log',
            'description' => "حذف سجل تدقيق: {$auditLog->description}",
            'metadata' => ['deleted_log_id' => $auditLog->id],
            'category' => 'system_config',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        $auditLog->delete();

        return back()->with('success', 'تم حذف سجل التدقيق بنجاح');
    }

    /**
     * تنظيف سجلات التدقيق القديمة
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days_old' => 'required|integer|min:30|max:365',
            'confirm' => 'required|accepted',
        ]);

        $daysOld = $request->days_old;
        $cutoffDate = now()->subDays($daysOld);
        
        $deletedCount = AdminAuditLog::where('created_at', '<', $cutoffDate)->delete();

        // تسجيل عملية التنظيف
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'cleanup_audit_logs',
            'description' => "تنظيف سجلات التدقيق الأقدم من {$daysOld} يوم",
            'metadata' => [
                'days_old' => $daysOld,
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate->toISOString(),
            ],
            'category' => 'system_config',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return back()->with('success', "تم حذف {$deletedCount} سجل تدقيق قديم");
    }

    /**
     * تصدير سجلات التدقيق
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,json,excel',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = AdminAuditLog::with(['superAdmin', 'tenant']);

        // تطبيق فلاتر التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->orderBy('created_at', 'desc')->get();

        // تسجيل عملية التصدير
        AdminAuditLog::createLog([
            'super_admin_id' => auth('super_admin')->id(),
            'action' => 'export_audit_logs',
            'description' => "تصدير سجلات التدقيق بصيغة {$request->format}",
            'metadata' => [
                'format' => $request->format,
                'records_count' => $auditLogs->count(),
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ],
            'category' => 'system_config',
            'severity' => 'low',
            'status' => 'success',
        ]);

        switch ($request->format) {
            case 'csv':
                return $this->exportToCsv($auditLogs);
            case 'json':
                return $this->exportToJson($auditLogs);
            case 'excel':
                return $this->exportToExcel($auditLogs);
            default:
                return back()->with('error', 'صيغة التصدير غير مدعومة');
        }
    }

    /**
     * تصدير إلى CSV
     */
    private function exportToCsv($auditLogs)
    {
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($auditLogs) {
            $file = fopen('php://output', 'w');
            
            // كتابة العناوين
            fputcsv($file, [
                'ID',
                'التاريخ',
                'المستخدم',
                'المستأجر',
                'العملية',
                'الوصف',
                'الفئة',
                'الخطورة',
                'الحالة',
                'IP Address',
                'User Agent',
            ]);

            // كتابة البيانات
            foreach ($auditLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->superAdmin->name ?? 'غير محدد',
                    $log->tenant->name ?? '-',
                    $log->action,
                    $log->description,
                    $log->category,
                    $log->severity,
                    $log->status,
                    $log->ip_address,
                    $log->user_agent,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * تصدير إلى JSON
     */
    private function exportToJson($auditLogs)
    {
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        $data = [
            'exported_at' => now()->toISOString(),
            'total_records' => $auditLogs->count(),
            'logs' => $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'created_at' => $log->created_at->toISOString(),
                    'super_admin' => $log->superAdmin->name ?? null,
                    'tenant' => $log->tenant->name ?? null,
                    'action' => $log->action,
                    'description' => $log->description,
                    'category' => $log->category,
                    'severity' => $log->severity,
                    'status' => $log->status,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'url' => $log->url,
                    'method' => $log->method,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'metadata' => $log->metadata,
                ];
            }),
        ];

        return Response::json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * تصدير إلى Excel
     */
    private function exportToExcel($auditLogs)
    {
        // يتطلب تثبيت Laravel Excel package
        // composer require maatwebsite/excel
        
        return back()->with('info', 'تصدير Excel غير متاح حالياً. يرجى استخدام CSV أو JSON.');
    }

    /**
     * الحصول على إحصائيات مفصلة
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 30); // آخر 30 يوم
        $startDate = now()->subDays($period);

        $stats = [
            'activities_by_day' => AdminAuditLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'activities_by_action' => AdminAuditLog::selectRaw('action, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            
            'activities_by_severity' => AdminAuditLog::selectRaw('severity, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('severity')
                ->get(),
            
            'activities_by_category' => AdminAuditLog::selectRaw('category, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('category')
                ->get(),
            
            'top_users' => AdminAuditLog::selectRaw('super_admin_id, COUNT(*) as count')
                ->with('superAdmin:id,name')
                ->where('created_at', '>=', $startDate)
                ->whereNotNull('super_admin_id')
                ->groupBy('super_admin_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }
}
