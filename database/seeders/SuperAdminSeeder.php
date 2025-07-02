<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء السوبر أدمن الرئيسي
        SuperAdmin::firstOrCreate(
            ['email' => 'superadmin@pharmacy-erp.com'],
            [
                'name' => 'مدير النظام الرئيسي',
                'password' => Hash::make('SuperAdmin@123'),
                'phone' => '+1234567890',
                'role' => 'super_admin',
                'status' => 'active',
                'two_factor_enabled' => false,
                'can_create_tenants' => true,
                'can_delete_tenants' => true,
                'can_manage_licenses' => true,
                'can_view_all_data' => true,
                'locale' => 'ar',
                'timezone' => 'UTC',
                'notes' => 'حساب السوبر أدمن الرئيسي للنظام',
            ]
        );

        // إنشاء أدمن مساعد
        SuperAdmin::firstOrCreate(
            ['email' => 'admin@pharmacy-erp.com'],
            [
                'name' => 'مدير النظام المساعد',
                'password' => Hash::make('Admin@123'),
                'phone' => '+1234567891',
                'role' => 'admin',
                'status' => 'active',
                'two_factor_enabled' => false,
                'can_create_tenants' => true,
                'can_delete_tenants' => false,
                'can_manage_licenses' => true,
                'can_view_all_data' => false,
                'locale' => 'ar',
                'timezone' => 'UTC',
                'notes' => 'حساب أدمن مساعد مع صلاحيات محدودة',
            ]
        );

        // إنشاء حساب دعم فني
        SuperAdmin::firstOrCreate(
            ['email' => 'support@pharmacy-erp.com'],
            [
                'name' => 'فريق الدعم الفني',
                'password' => Hash::make('Support@123'),
                'phone' => '+1234567892',
                'role' => 'support',
                'status' => 'active',
                'two_factor_enabled' => false,
                'can_create_tenants' => false,
                'can_delete_tenants' => false,
                'can_manage_licenses' => false,
                'can_view_all_data' => false,
                'permissions' => ['view_system_reports', 'audit_logs'],
                'locale' => 'ar',
                'timezone' => 'UTC',
                'notes' => 'حساب فريق الدعم الفني',
            ]
        );

        $this->command->info('تم إنشاء حسابات السوبر أدمن بنجاح');
        $this->command->info('بيانات تسجيل الدخول:');
        $this->command->info('السوبر أدمن: superadmin@pharmacy-erp.com / SuperAdmin@123');
        $this->command->info('الأدمن: admin@pharmacy-erp.com / Admin@123');
        $this->command->info('الدعم: support@pharmacy-erp.com / Support@123');
    }
}
