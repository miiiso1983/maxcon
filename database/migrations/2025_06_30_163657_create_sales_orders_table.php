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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->foreignId('sales_representative_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_visit_id')->nullable()->constrained()->onDelete('set null');
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('SAR');
            $table->enum('payment_terms', ['cash', 'credit_30', 'credit_60', 'credit_90'])->default('cash');
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable(); // ملاحظات داخلية
            $table->json('delivery_address')->nullable(); // عنوان التسليم
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'order_date']);
            $table->index(['sales_representative_id', 'order_date']);
            $table->index(['customer_id', 'order_date']);
            $table->index(['order_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
