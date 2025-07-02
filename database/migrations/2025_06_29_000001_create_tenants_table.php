<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_code')->unique(); // رمز المستأجر الفريد
            $table->string('name'); // اسم الشركة/المستأجر
            $table->string('domain')->unique()->nullable(); // النطاق المخصص
            $table->string('subdomain')->unique()->nullable(); // النطاق الفرعي
            $table->string('database_name')->unique(); // اسم قاعدة البيانات المخصصة
            $table->string('email')->unique(); // البريد الإلكتروني للمدير
            $table->string('phone')->nullable(); // رقم الهاتف
            $table->text('address')->nullable(); // العنوان
            $table->string('contact_person'); // الشخص المسؤول
            $table->string('contact_email'); // بريد الشخص المسؤول
            $table->string('contact_phone')->nullable(); // هاتف الشخص المسؤول
            
            // إعدادات الترخيص
            $table->string('license_type')->default('basic'); // نوع الترخيص
            $table->integer('max_users')->default(10); // الحد الأقصى للمستخدمين
            $table->integer('max_storage_gb')->default(5); // الحد الأقصى للتخزين بالجيجابايت
            $table->json('features')->nullable(); // المميزات المتاحة
            $table->date('license_start_date'); // تاريخ بداية الترخيص
            $table->date('license_end_date'); // تاريخ انتهاء الترخيص
            $table->boolean('is_trial')->default(false); // هل هو ترخيص تجريبي
            $table->integer('trial_days')->default(30); // عدد أيام التجربة
            
            // إحصائيات الاستخدام
            $table->integer('current_users_count')->default(0); // عدد المستخدمين الحالي
            $table->decimal('current_storage_gb', 8, 2)->default(0); // التخزين المستخدم حالياً
            $table->timestamp('last_activity_at')->nullable(); // آخر نشاط
            $table->integer('total_logins')->default(0); // إجمالي تسجيلات الدخول
            
            // الحالة والإعدادات
            $table->enum('status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // إعدادات إضافية
            $table->text('notes')->nullable(); // ملاحظات
            
            // معلومات الفوترة
            $table->decimal('monthly_fee', 10, 2)->default(0); // الرسوم الشهرية
            $table->string('currency', 3)->default('USD'); // العملة
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->date('next_billing_date')->nullable(); // تاريخ الفاتورة التالية
            
            $table->timestamps();
            $table->softDeletes(); // للحذف الآمن
            
            // فهارس للأداء
            $table->index(['status', 'is_active']);
            $table->index('license_end_date');
            $table->index('tenant_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
