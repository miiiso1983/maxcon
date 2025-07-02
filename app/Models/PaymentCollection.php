<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCollection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'collection_number',
        'sales_representative_id',
        'customer_id',
        'customer_visit_id',
        'invoice_id',
        'collection_date',
        'amount',
        'currency',
        'payment_method',
        'reference_number',
        'bank_name',
        'check_date',
        'status',
        'notes',
        'receipt_photos',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'check_date' => 'date',
        'amount' => 'decimal:2',
        'receipt_photos' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('collection_date', today());
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors & Mutators
    public function getFormattedAmountAttribute()
    {
        return number_format((float) $this->amount, 2) . ' ' . $this->currency;
    }

    public function getPaymentMethodTextAttribute()
    {
        $methods = [
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'credit_card' => 'بطاقة ائتمان',
            'other' => 'أخرى'
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'confirmed' => 'مؤكد',
            'deposited' => 'تم الإيداع',
            'bounced' => 'مرتد',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'deposited' => 'success',
            'bounced' => 'danger',
            'cancelled' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Methods
    public function generateCollectionNumber()
    {
        $prefix = 'COL-' . date('Y') . '-';
        $lastCollection = static::where('collection_number', 'like', $prefix . '%')
            ->orderBy('collection_number', 'desc')
            ->first();

        if ($lastCollection) {
            $lastNumber = intval(substr($lastCollection->collection_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
