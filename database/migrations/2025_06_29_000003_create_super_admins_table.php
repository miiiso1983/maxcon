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
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'support'])->default('admin');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            
            // إعدادات الأمان المتقدمة
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->json('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            
            // معلومات تسجيل الدخول
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            
            // صلاحيات خاصة
            $table->json('permissions')->nullable(); // صلاحيات مخصصة
            $table->boolean('can_create_tenants')->default(true);
            $table->boolean('can_delete_tenants')->default(false);
            $table->boolean('can_manage_licenses')->default(true);
            $table->boolean('can_view_all_data')->default(false);
            
            // إعدادات إضافية
            $table->string('locale', 5)->default('ar');
            $table->string('timezone')->default('UTC');
            $table->json('preferences')->nullable();
            $table->text('notes')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // فهارس
            $table->index(['status', 'role']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};
