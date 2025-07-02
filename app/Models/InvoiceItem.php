<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScoped;

class InvoiceItem extends Model
{
    use TenantScoped;

    protected $fillable = [
        'invoice_id',
        'item_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'discount_percentage',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * العلاقة مع الفاتورة
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * العلاقة مع الصنف
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * حساب إجمالي السطر
     */
    public function calculateLineTotal(): float
    {
        $subtotal = $this->quantity * $this->unit_price;
        return $subtotal - $this->discount_amount;
    }

    /**
     * حساب مبلغ الخصم من النسبة المئوية
     */
    public function calculateDiscountFromPercentage(): void
    {
        if ($this->discount_percentage > 0) {
            $subtotal = $this->quantity * $this->unit_price;
            $this->discount_amount = $subtotal * ($this->discount_percentage / 100);
        }
    }

    /**
     * تحديث إجمالي السطر تلقائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoiceItem) {
            $invoiceItem->calculateDiscountFromPercentage();
            $invoiceItem->line_total = $invoiceItem->calculateLineTotal();
        });
    }
}
