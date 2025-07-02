<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\License;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $licenses = [
            [
                'license_type' => 'basic',
                'name' => 'الباقة الأساسية',
                'description' => 'مناسبة للصيدليات الصغيرة والمتاجر الفردية',
                'max_users' => 3,
                'max_storage_gb' => 2,
                'max_tenants' => 1,
                'max_api_calls_per_month' => 5000,
                'features' => [
                    'inventory_management',
                    'sales_management',
                    'basic_reports',
                    'expiry_tracking'
                ],
                'modules' => [
                    'inventory',
                    'sales'
                ],
                'integrations' => [],
                'duration_months' => 12,
                'is_renewable' => true,
                'auto_renewal' => false,
                'trial_available' => true,
                'trial_days' => 14,
                'price_monthly' => 29.99,
                'price_yearly' => 299.99,
                'currency' => 'USD',
                'discount_percentage' => 0,
                'support_level' => 'basic',
                'priority_support' => false,
                'phone_support' => false,
                'custom_training' => false,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => false,
                'sort_order' => 1,
                'terms_and_conditions' => 'شروط وأحكام الباقة الأساسية...',
                'notes' => 'باقة مناسبة للبداية',
            ],
            [
                'license_type' => 'standard',
                'name' => 'الباقة المعيارية',
                'description' => 'مناسبة للصيدليات المتوسطة والشركات الناشئة',
                'max_users' => 10,
                'max_storage_gb' => 10,
                'max_tenants' => 1,
                'max_api_calls_per_month' => 15000,
                'features' => [
                    'inventory_management',
                    'sales_management',
                    'purchase_management',
                    'financial_reports',
                    'expiry_tracking',
                    'batch_tracking',
                    'barcode_scanning'
                ],
                'modules' => [
                    'inventory',
                    'sales',
                    'purchases',
                    'reports'
                ],
                'integrations' => [
                    'stripe',
                    'paypal'
                ],
                'duration_months' => 12,
                'is_renewable' => true,
                'auto_renewal' => false,
                'trial_available' => true,
                'trial_days' => 30,
                'price_monthly' => 79.99,
                'price_yearly' => 799.99,
                'currency' => 'USD',
                'discount_percentage' => 0,
                'support_level' => 'standard',
                'priority_support' => false,
                'phone_support' => false,
                'custom_training' => false,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => false,
                'sort_order' => 2,
                'terms_and_conditions' => 'شروط وأحكام الباقة المعيارية...',
                'notes' => 'الباقة الأكثر شعبية',
            ],
            [
                'license_type' => 'premium',
                'name' => 'الباقة المميزة',
                'description' => 'مناسبة للصيدليات الكبيرة والشركات المتقدمة',
                'max_users' => 25,
                'max_storage_gb' => 50,
                'max_tenants' => 1,
                'max_api_calls_per_month' => 50000,
                'features' => [
                    'inventory_management',
                    'sales_management',
                    'purchase_management',
                    'financial_reports',
                    'advanced_analytics',
                    'api_access',
                    'expiry_tracking',
                    'batch_tracking',
                    'barcode_scanning',
                    'prescription_management',
                    'loyalty_program',
                    'automated_reordering'
                ],
                'modules' => [
                    'inventory',
                    'sales',
                    'purchases',
                    'reports',
                    'analytics',
                    'api',
                    'crm'
                ],
                'integrations' => [
                    'stripe',
                    'paypal',
                    'quickbooks',
                    'mailchimp',
                    'twilio'
                ],
                'duration_months' => 12,
                'is_renewable' => true,
                'auto_renewal' => true,
                'trial_available' => true,
                'trial_days' => 30,
                'price_monthly' => 149.99,
                'price_yearly' => 1499.99,
                'currency' => 'USD',
                'discount_percentage' => 10,
                'support_level' => 'premium',
                'priority_support' => true,
                'phone_support' => true,
                'custom_training' => false,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => false,
                'sort_order' => 3,
                'terms_and_conditions' => 'شروط وأحكام الباقة المميزة...',
                'notes' => 'باقة متقدمة مع مميزات إضافية',
            ],
            [
                'license_type' => 'enterprise',
                'name' => 'الباقة المؤسسية',
                'description' => 'مناسبة للسلاسل والمؤسسات الكبيرة',
                'max_users' => 100,
                'max_storage_gb' => 200,
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
                    'inventory',
                    'sales',
                    'purchases',
                    'reports',
                    'analytics',
                    'api',
                    'multi_tenant',
                    'accounting',
                    'hr',
                    'crm'
                ],
                'integrations' => [
                    'stripe',
                    'paypal',
                    'quickbooks',
                    'xero',
                    'mailchimp',
                    'twilio',
                    'slack',
                    'zapier'
                ],
                'duration_months' => 12,
                'is_renewable' => true,
                'auto_renewal' => true,
                'trial_available' => true,
                'trial_days' => 30,
                'price_monthly' => 299.99,
                'price_yearly' => 2999.99,
                'currency' => 'USD',
                'discount_percentage' => 15,
                'support_level' => 'enterprise',
                'priority_support' => true,
                'phone_support' => true,
                'custom_training' => true,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => true,
                'sort_order' => 4,
                'terms_and_conditions' => 'شروط وأحكام الباقة المؤسسية...',
                'notes' => 'باقة شاملة للمؤسسات الكبيرة',
            ],
            [
                'license_type' => 'trial',
                'name' => 'النسخة التجريبية',
                'description' => 'نسخة تجريبية مجانية لمدة محدودة',
                'max_users' => 2,
                'max_storage_gb' => 1,
                'max_tenants' => 1,
                'max_api_calls_per_month' => 1000,
                'features' => [
                    'inventory_management',
                    'sales_management'
                ],
                'modules' => [
                    'inventory',
                    'sales'
                ],
                'integrations' => [],
                'duration_months' => 1,
                'is_renewable' => false,
                'auto_renewal' => false,
                'trial_available' => false,
                'trial_days' => 30,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'currency' => 'USD',
                'discount_percentage' => 0,
                'support_level' => 'basic',
                'priority_support' => false,
                'phone_support' => false,
                'custom_training' => false,
                'status' => 'active',
                'is_public' => true,
                'requires_approval' => false,
                'sort_order' => 0,
                'terms_and_conditions' => 'شروط وأحكام النسخة التجريبية...',
                'notes' => 'نسخة تجريبية مجانية',
            ],
        ];

        foreach ($licenses as $licenseData) {
            License::firstOrCreate(
                ['license_type' => $licenseData['license_type']],
                $licenseData
            );
        }

        $this->command->info('تم إنشاء التراخيص الافتراضية بنجاح');
    }
}
