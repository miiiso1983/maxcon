<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'order_number',
        'sales_representative_id',
        'customer_id',
        'customer_visit_id',
        'order_date',
        'delivery_date',
        'status',
        'subtotal',
        'discount_amount',
        'discount_percentage',
        'tax_amount',
        'tax_percentage',
        'shipping_cost',
        'total_amount',
        'currency',
        'payment_terms',
        'notes',
        'internal_notes',
        'delivery_address',
        'priority',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_address' => 'array',
        'order_date' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    // العلاقات
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function salesRepresentative()
    {
        return $this->belongsTo(SalesRepresentative::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerVisit()
    {
        return $this->belongsTo(CustomerVisit::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function collections()
    {
        return $this->hasMany(PaymentCollection::class, 'invoice_id');
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('order_date', today());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByRepresentative($query, $repId)
    {
        return $query->where('sales_representative_id', $repId);
    }

    // Accessors & Mutators
    public function getFormattedOrderDateAttribute()
    {
        return $this->order_date->format('Y-m-d');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'مسودة',
            'confirmed' => 'مؤكد',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'confirmed' => 'info',
            'processing' => 'warning',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Methods
    public function calculateTotals()
    {
        $this->subtotal = (float) $this->items()->sum('line_total');
        $this->tax_amount = (float) ($this->subtotal * ($this->tax_percentage / 100));
        $this->total_amount = (float) ($this->subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_cost);
        $this->save();
    }

    public function generateOrderNumber()
    {
        $prefix = 'SO-' . date('Y') . '-';
        $lastOrder = static::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
