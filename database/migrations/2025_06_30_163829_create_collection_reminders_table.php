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
        Schema::create('collection_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_representative_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('reminder_title');
            $table->text('reminder_description')->nullable();
            $table->date('reminder_date'); // تاريخ التذكير
            $table->time('reminder_time')->nullable(); // وقت التذكير
            $table->decimal('amount_to_collect', 12, 2)->nullable(); // المبلغ المطلوب تحصيله
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'notified', 'completed', 'cancelled'])->default('pending');
            $table->datetime('notified_at')->nullable(); // وقت إرسال التنبيه
            $table->datetime('completed_at')->nullable(); // وقت إكمال التحصيل
            $table->text('completion_notes')->nullable(); // ملاحظات الإكمال
            $table->json('notification_methods')->nullable(); // طرق التنبيه (email, sms, push)
            $table->boolean('auto_created')->default(false); // تم إنشاؤه تلقائياً
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'reminder_date']);
            $table->index(['sales_representative_id', 'reminder_date']);
            $table->index(['customer_id', 'reminder_date']);
            $table->index(['status', 'reminder_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_reminders');
    }
};
