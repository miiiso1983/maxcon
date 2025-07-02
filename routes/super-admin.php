<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| هذه المسارات مخصصة للسوبر أدمن فقط
| منفصلة تماماً عن النظام العادي
|
*/

// مسارات تسجيل الدخول (غير محمية)
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    
    // صفحة تسجيل الدخول
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    
    // إعادة توجيه الصفحة الرئيسية للسوبر أدمن
    Route::get('/', function () {
        return redirect()->route('super-admin.login');
    });
});

// مسارات محمية بـ middleware السوبر أدمن
Route::prefix('super-admin')->name('super-admin.')->middleware(['super_admin'])->group(function () {
    
    // لوحة التحكم الرئيسية
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/stats', [DashboardController::class, 'getDetailedStats'])->name('dashboard.stats');
    Route::get('dashboard/export', [DashboardController::class, 'exportReport'])->name('dashboard.export');
    Route::get('search', [DashboardController::class, 'search'])->name('search');
    
    // تسجيل الخروج
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // إدارة المستأجرين
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\TenantController::class, 'index'])->name('index');
        Route::get('credentials', [App\Http\Controllers\SuperAdmin\TenantController::class, 'credentials'])->name('credentials');
        Route::get('create', [App\Http\Controllers\SuperAdmin\TenantController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SuperAdmin\TenantController::class, 'store'])->name('store');
        Route::get('{tenant}', [App\Http\Controllers\SuperAdmin\TenantController::class, 'show'])->name('show');
        Route::get('{tenant}/edit', [App\Http\Controllers\SuperAdmin\TenantController::class, 'edit'])->name('edit');
        Route::put('{tenant}', [App\Http\Controllers\SuperAdmin\TenantController::class, 'update'])->name('update');
        Route::delete('{tenant}', [App\Http\Controllers\SuperAdmin\TenantController::class, 'destroy'])->name('destroy');

        // إجراءات إضافية للمستأجرين
        Route::post('{tenant}/activate', [App\Http\Controllers\SuperAdmin\TenantController::class, 'activate'])->name('activate');
        Route::post('{tenant}/deactivate', [App\Http\Controllers\SuperAdmin\TenantController::class, 'deactivate'])->name('deactivate');
        Route::post('{tenant}/suspend', [App\Http\Controllers\SuperAdmin\TenantController::class, 'suspend'])->name('suspend');
        Route::get('{tenant}/users', [App\Http\Controllers\SuperAdmin\TenantController::class, 'users'])->name('users');
        Route::get('{tenant}/stats', [App\Http\Controllers\SuperAdmin\TenantController::class, 'stats'])->name('stats');
    });
    
    // إدارة التراخيص
    Route::prefix('licenses')->name('licenses.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'index'])->name('index');
        Route::get('create', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'store'])->name('store');
        Route::get('{license}', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'show'])->name('show');
        Route::get('{license}/edit', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'edit'])->name('edit');
        Route::put('{license}', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'update'])->name('update');
        Route::delete('{license}', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'destroy'])->name('destroy');

        // إجراءات إضافية للتراخيص
        Route::post('{license}/renew', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'renew'])->name('renew');
        Route::post('{license}/suspend', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'suspend'])->name('suspend');
        Route::get('expired', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'expired'])->name('expired');
        Route::get('expiring-soon', [App\Http\Controllers\SuperAdmin\LicenseController::class, 'expiringSoon'])->name('expiring-soon');
    });
    
    // سجلات التدقيق
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'index'])->name('index');
        Route::get('{auditLog}', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'show'])->name('show');
        Route::delete('{auditLog}', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'destroy'])->name('destroy');
        Route::post('cleanup', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'cleanup'])->name('cleanup');
        Route::get('export', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'export'])->name('export');
        Route::get('statistics', [App\Http\Controllers\SuperAdmin\AuditLogController::class, 'statistics'])->name('statistics');

    });
    
    // إدارة السوبر أدمن
    Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', function() { return view('super-admin.admins.index'); })->name('index');
        Route::get('create', function() { return view('super-admin.admins.create'); })->name('create');
    });
    
    // الملف الشخصي والإعدادات
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AuthController::class, 'profile'])->name('index');
        Route::put('/', [AuthController::class, 'updateProfile'])->name('update');
        Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
        Route::put('change-password', [AuthController::class, 'changePassword'])->name('change-password.update');
    });
    
    // إعدادات النظام
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function() { return view('super-admin.settings.index'); })->name('index');

    });

    // التقارير المتقدمة
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function() { return view('super-admin.reports.index'); })->name('index');
    });
});
