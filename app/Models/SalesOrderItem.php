<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'sales_order_id',
        'item_id',
        'item_name',
        'item_code',
        'item_description',
        'quantity',
        'unit',
        'unit_price',
        'discount_amount',
        'discount_percentage',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // العلاقات
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Methods
    public function calculateLineTotal()
    {
        $subtotal = $this->quantity * $this->unit_price;
        $this->line_total = (float) ($subtotal - $this->discount_amount);
        return $this->line_total;
    }

    public function applyPercentageDiscount()
    {
        if ($this->discount_percentage > 0) {
            $subtotal = $this->quantity * $this->unit_price;
            $this->discount_amount = (float) ($subtotal * ($this->discount_percentage / 100));
            $this->calculateLineTotal();
        }
    }
}
