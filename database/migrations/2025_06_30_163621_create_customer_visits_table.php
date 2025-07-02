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
        Schema::create('customer_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_representative_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->datetime('visit_date');
            $table->decimal('latitude', 10, 8)->nullable(); // GPS موقع
            $table->decimal('longitude', 11, 8)->nullable(); // GPS موقع
            $table->string('location_address')->nullable(); // عنوان الموقع
            $table->enum('visit_type', ['planned', 'unplanned', 'follow_up', 'collection'])->default('planned');
            $table->enum('visit_status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('visit_purpose')->nullable(); // غرض الزيارة
            $table->text('visit_notes')->nullable(); // ملاحظات الزيارة
            $table->text('customer_feedback')->nullable(); // ملاحظات العميل
            $table->json('visit_photos')->nullable(); // صور من الزيارة
            $table->time('check_in_time')->nullable(); // وقت الوصول
            $table->time('check_out_time')->nullable(); // وقت المغادرة
            $table->integer('duration_minutes')->nullable(); // مدة الزيارة بالدقائق
            $table->boolean('order_created')->default(false); // هل تم إنشاء طلب
            $table->boolean('payment_collected')->default(false); // هل تم تحصيل دفعة
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'visit_date']);
            $table->index(['sales_representative_id', 'visit_date']);
            $table->index(['customer_id', 'visit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_visits');
    }
};
