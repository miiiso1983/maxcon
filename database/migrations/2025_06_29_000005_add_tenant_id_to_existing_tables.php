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
        // إضافة tenant_id لجدول المستخدمين
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'user_type']);
        });

        // إضافة tenant_id لجدول الموردين
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول العناصر
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول الطلبات
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول الفواتير
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول التحصيلات
        if (Schema::hasTable('collections')) {
            Schema::table('collections', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول المرتجعات
        if (Schema::hasTable('returns')) {
            Schema::table('returns', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول العملاء
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول الشركات
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // إضافة tenant_id لجدول الحسابات
        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['users', 'suppliers', 'items', 'orders', 'invoices', 'collections', 'returns', 'customers', 'companies', 'accounts'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
