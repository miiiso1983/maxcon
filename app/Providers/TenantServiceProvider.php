<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // تسجيل خدمات المستأجر
        $this->app->singleton('tenant.manager', function ($app) {
            return new \App\Services\TenantManager();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // مشاركة بيانات المستأجر مع جميع Views
        View::composer('*', function ($view) {
            if (app()->has('current_tenant')) {
                $tenant = app('current_tenant');
                $view->with([
                    'currentTenant' => $tenant,
                    'tenantSettings' => $tenant ? $tenant->settings : [],
                    'tenantFeatures' => $tenant ? $tenant->features : [],
                ]);
            }
        });

        // إعداد قاعدة البيانات الديناميكية للمستأجر (إذا كان يستخدم قواعد بيانات منفصلة)
        $this->configureTenantDatabase();

        // إعداد التخزين الخاص بالمستأجر
        $this->configureTenantStorage();

        // إعداد البريد الإلكتروني الخاص بالمستأجر
        $this->configureTenantMail();
    }

    /**
     * إعداد قاعدة البيانات الخاصة بالمستأجر
     */
    private function configureTenantDatabase(): void
    {
        // في هذا التطبيق نستخدم قاعدة بيانات واحدة مع tenant_id
        // لكن يمكن تطوير هذا لاستخدام قواعد بيانات منفصلة
        
        if (app()->has('current_tenant')) {
            $tenant = app('current_tenant');
            
            // يمكن إضافة منطق لتغيير اتصال قاعدة البيانات
            // Config::set('database.connections.tenant', [
            //     'driver' => 'mysql',
            //     'host' => env('DB_HOST', '127.0.0.1'),
            //     'port' => env('DB_PORT', '3306'),
            //     'database' => $tenant->database_name,
            //     'username' => env('DB_USERNAME', 'forge'),
            //     'password' => env('DB_PASSWORD', ''),
            //     'charset' => 'utf8mb4',
            //     'collation' => 'utf8mb4_unicode_ci',
            //     'prefix' => '',
            //     'strict' => true,
            //     'engine' => null,
            // ]);
        }
    }

    /**
     * إعداد التخزين الخاص بالمستأجر
     */
    private function configureTenantStorage(): void
    {
        if (app()->has('current_tenant')) {
            $tenant = app('current_tenant');
            
            // إعداد مسار تخزين خاص بالمستأجر
            Config::set('filesystems.disks.tenant', [
                'driver' => 'local',
                'root' => storage_path("app/tenants/{$tenant->tenant_code}"),
                'url' => env('APP_URL')."/storage/tenants/{$tenant->tenant_code}",
                'visibility' => 'public',
            ]);

            // إعداد مسار التخزين العام للمستأجر
            Config::set('filesystems.disks.tenant_public', [
                'driver' => 'local',
                'root' => storage_path("app/public/tenants/{$tenant->tenant_code}"),
                'url' => env('APP_URL')."/storage/tenants/{$tenant->tenant_code}",
                'visibility' => 'public',
            ]);
        }
    }

    /**
     * إعداد البريد الإلكتروني الخاص بالمستأجر
     */
    private function configureTenantMail(): void
    {
        if (app()->has('current_tenant')) {
            $tenant = app('current_tenant');
            $settings = $tenant->settings ?? [];
            
            // إعداد البريد الإلكتروني إذا كان للمستأجر إعدادات خاصة
            if (isset($settings['mail'])) {
                $mailSettings = $settings['mail'];
                
                Config::set('mail.mailers.tenant_smtp', [
                    'transport' => 'smtp',
                    'host' => $mailSettings['host'] ?? env('MAIL_HOST'),
                    'port' => $mailSettings['port'] ?? env('MAIL_PORT'),
                    'username' => $mailSettings['username'] ?? env('MAIL_USERNAME'),
                    'password' => $mailSettings['password'] ?? env('MAIL_PASSWORD'),
                    'encryption' => $mailSettings['encryption'] ?? env('MAIL_ENCRYPTION'),
                ]);

                Config::set('mail.from', [
                    'address' => $mailSettings['from_address'] ?? env('MAIL_FROM_ADDRESS'),
                    'name' => $mailSettings['from_name'] ?? $tenant->name,
                ]);
            }
        }
    }
}
