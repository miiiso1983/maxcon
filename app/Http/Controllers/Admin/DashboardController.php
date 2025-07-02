<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\SuperAdmin;
use App\Models\License;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Middleware is handled in routes

    /**
     * عرض لوحة التحكم الرئيسية
     */
    public function index(Request $request)
    {
        // الإحصائيات العامة
        $stats = $this->getGeneralStats();
        
        // إحصائيات الإيرادات
        $revenueStats = $this->getRevenueStats();
        
        // المستأجرين الجدد
        $newTenants = $this->getNewTenants();
        
        // التراخيص المنتهية قريباً
        $expiringLicenses = $this->getExpiringLicenses();
        
        // أحدث الأنشطة
        $recentActivities = $this->getRecentActivities();
        
        // إحصائيات الاستخدام
        $usageStats = $this->getUsageStats();
        
        // بيانات الرسوم البيانية
        $chartData = $this->getChartData($request);

        return view('admin.dashboard.index', compact(
            'stats',
            'revenueStats',
            'newTenants',
            'expiringLicenses',
            'recentActivities',
            'usageStats',
            'chartData'
        ));
    }

    /**
     * الحصول على الإحصائيات العامة
     */
    private function getGeneralStats(): array
    {
        return [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::active()->count(),
            'trial_tenants' => Tenant::trial()->count(),
            'expired_tenants' => Tenant::expired()->count(),
            'total_users' => TenantUser::count(),
            'active_users' => TenantUser::active()->count(),
            'total_licenses' => License::count(),
            'active_licenses' => License::active()->count(),
            'total_admins' => SuperAdmin::count(),
            'active_admins' => SuperAdmin::active()->count(),
        ];
    }

    /**
     * الحصول على إحصائيات الإيرادات
     */
    private function getRevenueStats(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        $currentMonthRevenue = Tenant::active()
            ->whereDate('created_at', '>=', $currentMonth)
            ->sum('monthly_fee');
            
        $lastMonthRevenue = Tenant::active()
            ->whereBetween('created_at', [$lastMonth, $currentMonth])
            ->sum('monthly_fee');
            
        $totalRevenue = Tenant::active()->sum('monthly_fee');
        $averageRevenue = Tenant::active()->avg('monthly_fee');
        
        $growth = $lastMonthRevenue > 0 
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        return [
            'total_revenue' => $totalRevenue,
            'current_month_revenue' => $currentMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'average_revenue' => $averageRevenue,
            'growth_percentage' => round($growth, 2),
        ];
    }

    /**
     * الحصول على المستأجرين الجدد
     */
    private function getNewTenants(): \Illuminate\Database\Eloquent\Collection
    {
        return Tenant::with(['systemManager'])
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * الحصول على التراخيص المنتهية قريباً
     */
    private function getExpiringLicenses(): \Illuminate\Database\Eloquent\Collection
    {
        return Tenant::with(['systemManager'])
            ->expiringSoon(30)
            ->orderBy('license_end_date')
            ->limit(10)
            ->get();
    }

    /**
     * الحصول على أحدث الأنشطة
     */
    private function getRecentActivities(): \Illuminate\Database\Eloquent\Collection
    {
        return AdminAuditLog::with(['superAdmin', 'tenant'])
            ->latest()
            ->limit(10)
            ->get();
    }

    /**
     * الحصول على إحصائيات الاستخدام
     */
    private function getUsageStats(): array
    {
        $totalStorage = Tenant::sum('current_storage_gb');
        $maxStorage = Tenant::sum('max_storage_gb');
        $storageUsagePercentage = $maxStorage > 0 ? ($totalStorage / $maxStorage) * 100 : 0;
        
        $totalUsers = Tenant::sum('current_users_count');
        $maxUsers = Tenant::sum('max_users');
        $userUsagePercentage = $maxUsers > 0 ? ($totalUsers / $maxUsers) * 100 : 0;
        
        $totalLogins = Tenant::sum('total_logins');
        $averageLoginsPerTenant = Tenant::count() > 0 ? $totalLogins / Tenant::count() : 0;

        return [
            'total_storage_used' => $totalStorage,
            'total_storage_available' => $maxStorage,
            'storage_usage_percentage' => round($storageUsagePercentage, 2),
            'total_users' => $totalUsers,
            'max_users_allowed' => $maxUsers,
            'user_usage_percentage' => round($userUsagePercentage, 2),
            'total_logins' => $totalLogins,
            'average_logins_per_tenant' => round($averageLoginsPerTenant, 2),
        ];
    }

    /**
     * الحصول على بيانات الرسوم البيانية
     */
    private function getChartData(Request $request): array
    {
        $period = $request->get('period', '30'); // آخر 30 يوم افتراضياً
        $startDate = now()->subDays($period);
        
        // بيانات المستأجرين الجدد
        $newTenantsData = Tenant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
            
        // بيانات الإيرادات
        $revenueData = Tenant::selectRaw('DATE(created_at) as date, SUM(monthly_fee) as revenue')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();
            
        // بيانات تسجيلات الدخول
        $loginData = AdminAuditLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('action', 'login')
            ->where('status', 'success')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
            
        // توزيع أنواع التراخيص
        $licenseDistribution = Tenant::selectRaw('license_type, COUNT(*) as count')
            ->groupBy('license_type')
            ->get()
            ->pluck('count', 'license_type')
            ->toArray();

        return [
            'new_tenants' => $newTenantsData,
            'revenue' => $revenueData,
            'logins' => $loginData,
            'license_distribution' => $licenseDistribution,
            'period' => $period,
        ];
    }

    /**
     * الحصول على إحصائيات مفصلة
     */
    public function getDetailedStats(Request $request)
    {
        $type = $request->get('type', 'tenants');
        $period = $request->get('period', '30');
        
        switch ($type) {
            case 'tenants':
                return $this->getTenantStats($period);
            case 'revenue':
                return $this->getDetailedRevenueStats($period);
            case 'usage':
                return $this->getDetailedUsageStats($period);
            case 'activities':
                return $this->getDetailedActivityStats($period);
            default:
                return response()->json(['error' => 'Invalid stats type'], 400);
        }
    }

    /**
     * إحصائيات المستأجرين المفصلة
     */
    private function getTenantStats(int $period): array
    {
        $startDate = now()->subDays($period);
        
        return [
            'new_tenants_by_day' => Tenant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'tenants_by_license_type' => Tenant::selectRaw('license_type, COUNT(*) as count')
                ->groupBy('license_type')
                ->get(),
            'tenants_by_status' => Tenant::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
            'top_tenants_by_users' => Tenant::orderBy('current_users_count', 'desc')
                ->limit(10)
                ->get(['name', 'current_users_count', 'max_users']),
        ];
    }

    /**
     * إحصائيات الإيرادات المفصلة
     */
    private function getDetailedRevenueStats(int $period): array
    {
        $startDate = now()->subDays($period);
        
        return [
            'revenue_by_day' => Tenant::selectRaw('DATE(created_at) as date, SUM(monthly_fee) as revenue')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'revenue_by_license_type' => Tenant::selectRaw('license_type, SUM(monthly_fee) as revenue, COUNT(*) as count')
                ->groupBy('license_type')
                ->get(),
            'top_revenue_tenants' => Tenant::orderBy('monthly_fee', 'desc')
                ->limit(10)
                ->get(['name', 'monthly_fee', 'license_type']),
        ];
    }

    /**
     * إحصائيات الاستخدام المفصلة
     */
    private function getDetailedUsageStats(int $period): array
    {
        return [
            'storage_usage_by_tenant' => Tenant::selectRaw('name, current_storage_gb, max_storage_gb, (current_storage_gb/max_storage_gb)*100 as usage_percentage')
                ->orderBy('usage_percentage', 'desc')
                ->limit(10)
                ->get(),
            'user_usage_by_tenant' => Tenant::selectRaw('name, current_users_count, max_users, (current_users_count/max_users)*100 as usage_percentage')
                ->orderBy('usage_percentage', 'desc')
                ->limit(10)
                ->get(),
            'login_activity' => Tenant::selectRaw('name, total_logins, last_activity_at')
                ->orderBy('total_logins', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * إحصائيات الأنشطة المفصلة
     */
    private function getDetailedActivityStats(int $period): array
    {
        $startDate = now()->subDays($period);
        
        return [
            'activities_by_day' => AdminAuditLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'activities_by_action' => AdminAuditLog::selectRaw('action, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->get(),
            'activities_by_severity' => AdminAuditLog::selectRaw('severity, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('severity')
                ->get(),
        ];
    }

    /**
     * تصدير التقارير
     */
    public function exportReport(Request $request)
    {
        $type = $request->get('type', 'tenants');
        $format = $request->get('format', 'csv');
        
        // هنا يمكن إضافة منطق تصدير التقارير
        // باستخدام مكتبات مثل Laravel Excel
        
        return response()->json(['message' => 'Export functionality will be implemented']);
    }
}
