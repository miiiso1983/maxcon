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
        Schema::create('payment_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('collection_number')->unique();
            $table->foreignId('sales_representative_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_visit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->date('collection_date');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('SAR');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'other'])->default('cash');
            $table->string('reference_number')->nullable(); // رقم الشيك أو التحويل
            $table->string('bank_name')->nullable(); // اسم البنك
            $table->date('check_date')->nullable(); // تاريخ الشيك
            $table->enum('status', ['pending', 'confirmed', 'deposited', 'bounced', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('receipt_photos')->nullable(); // صور الإيصالات
            $table->decimal('latitude', 10, 8)->nullable(); // موقع التحصيل
            $table->decimal('longitude', 11, 8)->nullable(); // موقع التحصيل
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'collection_date'], 'pc_tenant_date_idx');
            $table->index(['sales_representative_id', 'collection_date'], 'pc_rep_date_idx');
            $table->index(['customer_id', 'collection_date'], 'pc_customer_date_idx');
            $table->index(['collection_number'], 'pc_number_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_collections');
    }
};
