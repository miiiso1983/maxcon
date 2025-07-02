<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'sales_representative_id',
        'customer_id',
        'visit_date',
        'latitude',
        'longitude',
        'location_address',
        'visit_type',
        'visit_status',
        'visit_purpose',
        'visit_notes',
        'customer_feedback',
        'visit_photos',
        'check_in_time',
        'check_out_time',
        'duration_minutes',
        'order_created',
        'payment_collected',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'visit_photos' => 'array',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'order_created' => 'boolean',
        'payment_collected' => 'boolean',
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

    public function orders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function collections()
    {
        return $this->hasMany(PaymentCollection::class);
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', today());
    }

    public function scopeCompleted($query)
    {
        return $query->where('visit_status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('visit_status', 'in_progress');
    }

    // Accessors & Mutators
    public function getFormattedVisitDateAttribute()
    {
        return $this->visit_date->format('Y-m-d H:i');
    }

    public function getVisitDurationAttribute()
    {
        if ($this->check_in_time && $this->check_out_time) {
            return $this->check_in_time->diffInMinutes($this->check_out_time);
        }
        return null;
    }

    public function getLocationCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude
            ];
        }
        return null;
    }
}
