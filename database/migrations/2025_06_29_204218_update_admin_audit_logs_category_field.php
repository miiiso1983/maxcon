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
        Schema::table('admin_audit_logs', function (Blueprint $table) {
            // تحديث حقل category ليكون أطول
            $table->string('category', 100)->change();

            // تحديث حقول أخرى قد تحتاج لمساحة أكبر
            $table->string('action', 100)->change();
            $table->string('severity', 50)->change();
            $table->string('status', 50)->change();
            $table->string('method', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_audit_logs', function (Blueprint $table) {
            // إعادة الحقول لحجمها الأصلي
            $table->string('category', 50)->change();
            $table->string('action', 50)->change();
            $table->string('severity', 20)->change();
            $table->string('status', 20)->change();
            $table->string('method', 10)->nullable()->change();
        });
    }
};
