<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\SuperAdmin;

class SuperAdminGateProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // تعريف Gates للسوبر أدمن
        Gate::define('create_tenants', function (SuperAdmin $admin) {
            return $admin->hasPermission('create_tenants');
        });

        Gate::define('delete_tenants', function (SuperAdmin $admin) {
            return $admin->hasPermission('delete_tenants');
        });

        Gate::define('manage_licenses', function (SuperAdmin $admin) {
            return $admin->hasPermission('manage_licenses');
        });

        Gate::define('view_all_data', function (SuperAdmin $admin) {
            return $admin->hasPermission('view_all_data');
        });

        Gate::define('manage_super_admins', function (SuperAdmin $admin) {
            return $admin->isSuperAdmin();
        });

        Gate::define('system_settings', function (SuperAdmin $admin) {
            return $admin->hasPermission('system_settings') || $admin->isSuperAdmin();
        });

        Gate::define('audit_logs', function (SuperAdmin $admin) {
            return $admin->hasPermission('audit_logs') || $admin->isSuperAdmin();
        });

        // Gates خاصة بالمستأجرين
        Gate::define('suspend_tenant', function (SuperAdmin $admin) {
            return $admin->hasPermission('manage_licenses') || $admin->isSuperAdmin();
        });

        Gate::define('renew_license', function (SuperAdmin $admin) {
            return $admin->hasPermission('manage_licenses') || $admin->isSuperAdmin();
        });

        Gate::define('view_tenant_data', function (SuperAdmin $admin) {
            return $admin->hasPermission('view_all_data') || $admin->isSuperAdmin();
        });

        // Gates للتقارير والإحصائيات
        Gate::define('view_system_reports', function (SuperAdmin $admin) {
            return $admin->isAdmin();
        });

        Gate::define('export_data', function (SuperAdmin $admin) {
            return $admin->hasPermission('view_all_data') || $admin->isSuperAdmin();
        });
    }
}
