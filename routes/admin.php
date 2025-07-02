<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ToolsController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the super admin panel
|
*/

// مسارات المصادقة (بدون middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    // تسجيل الدخول والخروج
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// مسارات محمية بـ middleware
Route::prefix('admin')->name('admin.')->middleware(['super_admin'])->group(function () {
    
    // لوحة التحكم الرئيسية
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/stats', [DashboardController::class, 'getDetailedStats'])->name('dashboard.stats');
    Route::get('dashboard/export', [DashboardController::class, 'exportReport'])->name('dashboard.export');
    
    // إدارة الملف الشخصي
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::put('change-password', [AuthController::class, 'changePassword'])->name('change-password.update');
    
    // إدارة المستأجرين
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/unsuspend', [TenantController::class, 'unsuspend'])->name('tenants.unsuspend');
    Route::post('tenants/{tenant}/renew-license', [TenantController::class, 'renewLicense'])->name('tenants.renew-license');
    
    // إدارة التراخيص
    Route::resource('licenses', LicenseController::class);
    Route::post('licenses/{license}/toggle-status', [LicenseController::class, 'toggleStatus'])->name('licenses.toggle-status');
    Route::post('licenses/create-defaults', [LicenseController::class, 'createDefaults'])->name('licenses.create-defaults');
    
    // إدارة السوبر أدمن (محدود للسوبر أدمن فقط)
    Route::middleware(['super_admin.permission:manage_super_admins'])->group(function () {
        Route::resource('super-admins', SuperAdminController::class);
        Route::post('super-admins/{superAdmin}/toggle-status', [SuperAdminController::class, 'toggleStatus'])->name('super-admins.toggle-status');
        Route::post('super-admins/{superAdmin}/reset-password', [SuperAdminController::class, 'resetPassword'])->name('super-admins.reset-password');
        Route::post('super-admins/{superAdmin}/unlock', [SuperAdminController::class, 'unlock'])->name('super-admins.unlock');
    });
    
    // سجلات التدقيق
    Route::middleware(['super_admin.permission:audit_logs'])->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
        Route::delete('audit-logs/{auditLog}', [AuditLogController::class, 'destroy'])->name('audit-logs.destroy');
        Route::post('audit-logs/cleanup', [AuditLogController::class, 'cleanup'])->name('audit-logs.cleanup');
        Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    });
    
    // إعدادات النظام - معطل مؤقتاً
    // Route::middleware(['super_admin.permission:system_settings'])->group(function () {
    //     Route::get('settings', [SystemSettingsController::class, 'index'])->name('settings.index');
    //     Route::put('settings', [SystemSettingsController::class, 'update'])->name('settings.update');
    //     Route::post('settings/backup', [SystemSettingsController::class, 'createBackup'])->name('settings.backup');
    //     Route::post('settings/maintenance', [SystemSettingsController::class, 'toggleMaintenance'])->name('settings.maintenance');
    //     Route::get('settings/system-info', [SystemSettingsController::class, 'systemInfo'])->name('settings.system-info');
    // });
    
    // API للبيانات الديناميكية
    Route::prefix('api')->name('api.')->group(function () {
        // إحصائيات المستأجرين
        Route::get('tenants/stats', [TenantController::class, 'getStats'])->name('tenants.stats');
        Route::get('tenants/{tenant}/usage', [TenantController::class, 'getUsage'])->name('tenants.usage');
        Route::get('tenants/expiring', [TenantController::class, 'getExpiring'])->name('tenants.expiring');
        
        // إحصائيات التراخيص
        Route::get('licenses/stats', [LicenseController::class, 'getStats'])->name('licenses.stats');
        Route::get('licenses/usage', [LicenseController::class, 'getUsage'])->name('licenses.usage');
        
        // إحصائيات النظام
        Route::get('system/health', [DashboardController::class, 'systemHealth'])->name('system.health');
        Route::get('system/performance', [DashboardController::class, 'performance'])->name('system.performance');
        
        // البحث العام
        Route::get('search', [DashboardController::class, 'search'])->name('search');
    });
    
    // مسارات المساعدة والدعم
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [HelpController::class, 'index'])->name('index');
        Route::get('documentation', [HelpController::class, 'documentation'])->name('documentation');
        Route::get('api-docs', [HelpController::class, 'apiDocs'])->name('api-docs');
        Route::get('changelog', [HelpController::class, 'changelog'])->name('changelog');
        Route::get('support', [HelpController::class, 'support'])->name('support');
    });
    
    // مسارات التقارير المتقدمة
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('tenants', [ReportsController::class, 'tenants'])->name('tenants');
        Route::get('revenue', [ReportsController::class, 'revenue'])->name('revenue');
        Route::get('usage', [ReportsController::class, 'usage'])->name('usage');
        Route::get('security', [ReportsController::class, 'security'])->name('security');
        Route::post('generate', [ReportsController::class, 'generate'])->name('generate');
        Route::get('download/{report}', [ReportsController::class, 'download'])->name('download');
    });
    
    // مسارات الإشعارات
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('mark-read/{notification}', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('settings', [NotificationController::class, 'settings'])->name('settings');
        Route::put('settings', [NotificationController::class, 'updateSettings'])->name('settings.update');
    });
    
    // مسارات الأدوات المساعدة
    Route::prefix('tools')->name('tools.')->group(function () {
        Route::get('/', [ToolsController::class, 'index'])->name('index');
        Route::get('database-manager', [ToolsController::class, 'databaseManager'])->name('database-manager');
        Route::get('cache-manager', [ToolsController::class, 'cacheManager'])->name('cache-manager');
        Route::post('clear-cache', [ToolsController::class, 'clearCache'])->name('clear-cache');
        Route::get('log-viewer', [ToolsController::class, 'logViewer'])->name('log-viewer');
        Route::get('queue-monitor', [ToolsController::class, 'queueMonitor'])->name('queue-monitor');
    });
});
