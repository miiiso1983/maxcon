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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->unique(); // مفتاح الترخيص الفريد
            $table->string('license_type'); // نوع الترخيص
            $table->string('name'); // اسم الترخيص
            $table->text('description')->nullable(); // وصف الترخيص
            
            // إعدادات الحدود
            $table->integer('max_users')->default(10); // الحد الأقصى للمستخدمين
            $table->integer('max_storage_gb')->default(5); // الحد الأقصى للتخزين
            $table->integer('max_tenants')->default(1); // الحد الأقصى للمستأجرين (للتراخيص المؤسسية)
            $table->integer('max_api_calls_per_month')->default(10000); // الحد الأقصى لاستدعاءات API
            
            // المميزات المتاحة
            $table->json('features'); // قائمة المميزات المتاحة
            $table->json('modules'); // الوحدات المتاحة
            $table->json('integrations')->nullable(); // التكاملات المتاحة
            
            // إعدادات الفترة الزمنية
            $table->integer('duration_months')->default(12); // مدة الترخيص بالأشهر
            $table->boolean('is_renewable')->default(true); // هل يمكن تجديده
            $table->boolean('auto_renewal')->default(false); // التجديد التلقائي
            
            // إعدادات التجربة
            $table->boolean('trial_available')->default(true); // هل التجربة متاحة
            $table->integer('trial_days')->default(30); // عدد أيام التجربة
            
            // إعدادات السعر
            $table->decimal('price_monthly', 10, 2)->default(0); // السعر الشهري
            $table->decimal('price_yearly', 10, 2)->default(0); // السعر السنوي
            $table->string('currency', 3)->default('USD'); // العملة
            $table->decimal('discount_percentage', 5, 2)->default(0); // نسبة الخصم
            
            // إعدادات الدعم
            $table->enum('support_level', ['basic', 'standard', 'premium', 'enterprise'])->default('basic');
            $table->boolean('priority_support')->default(false); // الدعم المميز
            $table->boolean('phone_support')->default(false); // الدعم الهاتفي
            $table->boolean('custom_training')->default(false); // التدريب المخصص
            
            // الحالة والإعدادات
            $table->enum('status', ['active', 'inactive', 'deprecated'])->default('active');
            $table->boolean('is_public')->default(true); // هل متاح للعامة
            $table->boolean('requires_approval')->default(false); // يتطلب موافقة
            $table->integer('sort_order')->default(0); // ترتيب العرض
            
            // معلومات إضافية
            $table->json('metadata')->nullable(); // معلومات إضافية
            $table->text('terms_and_conditions')->nullable(); // الشروط والأحكام
            $table->text('notes')->nullable(); // ملاحظات
            
            $table->timestamps();
            
            // فهارس
            $table->index(['status', 'is_public']);
            $table->index('license_type');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
