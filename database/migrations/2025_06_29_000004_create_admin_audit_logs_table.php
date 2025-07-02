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
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('super_admin_id')->nullable()->constrained('super_admins')->onDelete('set null');
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            
            // معلومات العملية
            $table->string('action'); // نوع العملية (create, update, delete, login, etc.)
            $table->string('model_type')->nullable(); // نوع النموذج المتأثر
            $table->unsignedBigInteger('model_id')->nullable(); // معرف النموذج
            $table->string('description'); // وصف العملية
            
            // البيانات المتغيرة
            $table->json('old_values')->nullable(); // القيم القديمة
            $table->json('new_values')->nullable(); // القيم الجديدة
            $table->json('metadata')->nullable(); // معلومات إضافية
            
            // معلومات الطلب
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            
            // التصنيف والأهمية
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('category', ['authentication', 'tenant_management', 'user_management', 'license_management', 'system_config', 'data_access'])->default('system_config');
            
            // الحالة
            $table->enum('status', ['success', 'failed', 'warning'])->default('success');
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            
            // فهارس للأداء
            $table->index(['super_admin_id', 'created_at']);
            $table->index(['tenant_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};
