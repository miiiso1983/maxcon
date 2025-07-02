<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;
use App\Models\License;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Services\TenantManager;

class MultiTenantSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 بدء إعداد نظام إدارة الصيدلية متعدد المستأجرين...');

        // 1. إنشاء السوبر أدمن
        $this->createSuperAdmins();

        // 2. إنشاء التراخيص
        $this->createLicenses();

        // 3. إنشاء مستأجرين تجريبيين
        $this->createDemoTenants();

        $this->command->info('✅ تم إعداد النظام بنجاح!');
        $this->displayLoginCredentials();
    }

    /**
     * إنشاء حسابات السوبر أدمن
     */
    private function createSuperAdmins(): void
    {
        $this->command->info('📝 إنشاء حسابات السوبر أدمن...');

        // السوبر أدمن الرئيسي
        SuperAdmin::firstOrCreate(
            ['email' => 'superadmin@pharmacy-erp.com'],
            [
                'name' => 'مدير النظام الرئيسي',
                'password' => Hash::make('SuperAdmin@2024'),
                'phone' => '+966501234567',
                'role' => 'super_admin',
                'status' => 'active',
                'two_factor_enabled' => false,
                'can_create_tenants' => true,
                'can_delete_tenants' => true,
                'can_manage_licenses' => true,
                'can_view_all_data' => true,
                'locale' => 'ar',
                'timezone' => 'Asia/Riyadh',
                'notes' => 'حساب السوبر أدمن الرئيسي للنظام - تم إنشاؤه تلقائياً',
            ]
        );

        // أدمن مساعد
        SuperAdmin::firstOrCreate(
            ['email' => 'admin@pharmacy-erp.com'],
            [
                'name' => 'مدير النظام المساعد',
                'password' => Hash::make('Admin@2024'),
                'phone' => '+966501234568',
                'role' => 'admin',
                'status' => 'active',
                'two_factor_enabled' => false,
                'can_create_tenants' => true,
                'can_delete_tenants' => false,
                'can_manage_licenses' => true,
                'can_view_all_data' => false,
                'locale' => 'ar',
                'timezone' => 'Asia/Riyadh',
                'notes' => 'حساب أدمن مساعد مع صلاحيات محدودة',
            ]
        );

        // فريق الدعم الفني
        SuperAdmin::firstOrCreate(
            ['email' => 'support@pharmacy-erp.com'],
            [
                'name' => 'فريق الدعم الفني',
                'password' => Hash::make('Support@2024'),
                'phone' => '+966501234569',
                'role' => 'support',
                'status' => 'active',
                'two_factor_enabled' => false,
                'can_create_tenants' => false,
                'can_delete_tenants' => false,
                'can_manage_licenses' => false,
                'can_view_all_data' => false,
                'permissions' => ['view_system_reports', 'audit_logs'],
                'locale' => 'ar',
                'timezone' => 'Asia/Riyadh',
                'notes' => 'حساب فريق الدعم الفني',
            ]
        );

        $this->command->info('✅ تم إنشاء حسابات السوبر أدمن');
    }

    /**
     * إنشاء التراخيص
     */
    private function createLicenses(): void
    {
        $this->command->info('🔑 إنشاء التراخيص...');

        $licenses = [
            [
                'license_type' => 'trial',
                'name' => 'النسخة التجريبية',
                'description' => 'نسخة تجريبية مجانية لمدة 30 يوم',
                'max_users' => 2,
                'max_storage_gb' => 1,
                'max_tenants' => 1,
                'max_api_calls_per_month' => 1000,
                'features' => ['inventory_management', 'sales_management'],
                'modules' => ['inventory', 'sales'],
                'integrations' => [],
                'duration_months' => 1,
                'is_renewable' => false,
                'auto_renewal' => false,
                'trial_available' => false,
                'trial_days' => 30,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'currency' => 'SAR',
                'discount_percentage' => 0,
                'support_level' => 'basic',
                'priority_support' => false,
                'phone_support' => false,
                'custom_training' => false,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => false,
                'sort_order' => 0,
            ],
            [
                'license_type' => 'basic',
                'name' => 'الباقة الأساسية',
                'description' => 'مناسبة للصيدليات الصغيرة والمتاجر الفردية',
                'max_users' => 5,
                'max_storage_gb' => 5,
                'max_tenants' => 1,
                'max_api_calls_per_month' => 10000,
                'features' => [
                    'inventory_management',
                    'sales_management',
                    'expiry_tracking',
                    'basic_reports'
                ],
                'modules' => ['inventory', 'sales', 'reports'],
                'integrations' => [],
                'duration_months' => 12,
                'is_renewable' => true,
                'auto_renewal' => false,
                'trial_available' => true,
                'trial_days' => 14,
                'price_monthly' => 299,
                'price_yearly' => 2990,
                'currency' => 'SAR',
                'discount_percentage' => 0,
                'support_level' => 'basic',
                'priority_support' => false,
                'phone_support' => false,
                'custom_training' => false,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => false,
                'sort_order' => 1,
            ],
            [
                'license_type' => 'professional',
                'name' => 'الباقة الاحترافية',
                'description' => 'مناسبة للصيدليات المتوسطة والشركات الناشئة',
                'max_users' => 15,
                'max_storage_gb' => 20,
                'max_tenants' => 1,
                'max_api_calls_per_month' => 50000,
                'features' => [
                    'inventory_management',
                    'sales_management',
                    'purchase_management',
                    'financial_reports',
                    'expiry_tracking',
                    'batch_tracking',
                    'barcode_scanning',
                    'prescription_management'
                ],
                'modules' => ['inventory', 'sales', 'purchases', 'reports', 'prescriptions'],
                'integrations' => ['stripe', 'paypal'],
                'duration_months' => 12,
                'is_renewable' => true,
                'auto_renewal' => false,
                'trial_available' => true,
                'trial_days' => 30,
                'price_monthly' => 799,
                'price_yearly' => 7990,
                'currency' => 'SAR',
                'discount_percentage' => 10,
                'support_level' => 'standard',
                'priority_support' => false,
                'phone_support' => true,
                'custom_training' => false,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => false,
                'sort_order' => 2,
            ],
            [
                'license_type' => 'enterprise',
                'name' => 'الباقة المؤسسية',
                'description' => 'مناسبة للسلاسل والمؤسسات الكبيرة',
                'max_users' => 100,
                'max_storage_gb' => 100,
                'max_tenants' => 10,
                'max_api_calls_per_month' => 200000,
                'features' => [
                    'inventory_management',
                    'sales_management',
                    'purchase_management',
                    'financial_reports',
                    'advanced_analytics',
                    'api_access',
                    'custom_branding',
                    'multi_location',
                    'expiry_tracking',
                    'batch_tracking',
                    'barcode_scanning',
                    'prescription_management',
                    'insurance_integration',
                    'loyalty_program',
                    'automated_reordering'
                ],
                'modules' => [
                    'inventory', 'sales', 'purchases', 'reports', 
                    'analytics', 'api', 'multi_tenant', 'accounting', 
                    'hr', 'crm', 'prescriptions'
                ],
                'integrations' => [
                    'stripe', 'paypal', 'quickbooks', 'xero',
                    'mailchimp', 'twilio', 'slack', 'zapier'
                ],
                'duration_months' => 12,
                'is_renewable' => true,
                'auto_renewal' => true,
                'trial_available' => true,
                'trial_days' => 30,
                'price_monthly' => 1999,
                'price_yearly' => 19990,
                'currency' => 'SAR',
                'discount_percentage' => 15,
                'support_level' => 'enterprise',
                'priority_support' => true,
                'phone_support' => true,
                'custom_training' => true,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($licenses as $licenseData) {
            License::firstOrCreate(
                ['license_type' => $licenseData['license_type']],
                $licenseData
            );
        }

        $this->command->info('✅ تم إنشاء التراخيص');
    }

    /**
     * إنشاء مستأجرين تجريبيين
     */
    private function createDemoTenants(): void
    {
        $this->command->info('🏢 إنشاء مستأجرين تجريبيين...');

        $tenantManager = new TenantManager();

        // مستأجر تجريبي 1 - صيدلية النور
        $tenant1Data = [
            'name' => 'صيدلية النور',
            'email' => 'info@alnoor-pharmacy.com',
            'phone' => '+966501111111',
            'address' => 'شارع الملك فهد، الرياض، المملكة العربية السعودية',
            'contact_person' => 'أحمد محمد النور',
            'contact_email' => 'ahmed@alnoor-pharmacy.com',
            'contact_phone' => '+966501111111',
            'license_type' => 'professional',
            'max_users' => 15,
            'max_storage_gb' => 20,
            'features' => [
                'inventory_management', 'sales_management', 'purchase_management',
                'financial_reports', 'expiry_tracking', 'batch_tracking',
                'barcode_scanning', 'prescription_management'
            ],
            'license_start_date' => now(),
            'license_end_date' => now()->addYear(),
            'is_trial' => false,
            'monthly_fee' => 799,
            'currency' => 'SAR',
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'is_active' => true,
        ];

        $manager1Data = [
            'name' => 'أحمد محمد النور',
            'email' => 'ahmed@alnoor-pharmacy.com',
            'password' => 'Manager@2024',
            'phone' => '+966501111111',
        ];

        try {
            $tenant1 = $tenantManager->createTenant($tenant1Data, $manager1Data);
            $this->command->info("✅ تم إنشاء المستأجر: {$tenant1->name}");
        } catch (\Exception $e) {
            $this->command->error("❌ فشل في إنشاء المستأجر الأول: " . $e->getMessage());
        }

        // مستأجر تجريبي 2 - صيدلية الشفاء
        $tenant2Data = [
            'name' => 'صيدلية الشفاء',
            'email' => 'info@alshifa-pharmacy.com',
            'phone' => '+966502222222',
            'address' => 'شارع العليا، الرياض، المملكة العربية السعودية',
            'contact_person' => 'فاطمة أحمد الشفاء',
            'contact_email' => 'fatima@alshifa-pharmacy.com',
            'contact_phone' => '+966502222222',
            'license_type' => 'basic',
            'max_users' => 5,
            'max_storage_gb' => 5,
            'features' => [
                'inventory_management', 'sales_management',
                'expiry_tracking', 'basic_reports'
            ],
            'license_start_date' => now(),
            'license_end_date' => now()->addYear(),
            'is_trial' => false,
            'monthly_fee' => 299,
            'currency' => 'SAR',
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'is_active' => true,
        ];

        $manager2Data = [
            'name' => 'فاطمة أحمد الشفاء',
            'email' => 'fatima@alshifa-pharmacy.com',
            'password' => 'Manager@2024',
            'phone' => '+966502222222',
        ];

        try {
            $tenant2 = $tenantManager->createTenant($tenant2Data, $manager2Data);
            $this->command->info("✅ تم إنشاء المستأجر: {$tenant2->name}");
        } catch (\Exception $e) {
            $this->command->error("❌ فشل في إنشاء المستأجر الثاني: " . $e->getMessage());
        }

        $this->command->info('✅ تم إنشاء المستأجرين التجريبيين');
    }

    /**
     * عرض بيانات تسجيل الدخول
     */
    private function displayLoginCredentials(): void
    {
        $this->command->info('');
        $this->command->info('🔐 بيانات تسجيل الدخول:');
        $this->command->info('================================');
        $this->command->info('');
        
        $this->command->info('👑 السوبر أدمن الرئيسي:');
        $this->command->info('   البريد الإلكتروني: superadmin@pharmacy-erp.com');
        $this->command->info('   كلمة المرور: SuperAdmin@2024');
        $this->command->info('   الرابط: ' . url('/admin/login'));
        $this->command->info('');
        
        $this->command->info('👤 الأدمن المساعد:');
        $this->command->info('   البريد الإلكتروني: admin@pharmacy-erp.com');
        $this->command->info('   كلمة المرور: Admin@2024');
        $this->command->info('');
        
        $this->command->info('🛠️ فريق الدعم:');
        $this->command->info('   البريد الإلكتروني: support@pharmacy-erp.com');
        $this->command->info('   كلمة المرور: Support@2024');
        $this->command->info('');
        
        $this->command->info('🏢 مديري المستأجرين:');
        $this->command->info('   صيدلية النور: ahmed@alnoor-pharmacy.com / Manager@2024');
        $this->command->info('   صيدلية الشفاء: fatima@alshifa-pharmacy.com / Manager@2024');
        $this->command->info('');
        
        $this->command->warn('⚠️  تأكد من تغيير كلمات المرور الافتراضية في بيئة الإنتاج!');
    }
}
