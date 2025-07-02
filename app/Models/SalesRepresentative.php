<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRepresentative extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'employee_code',
        'name',
        'email',
        'phone',
        'address',
        'national_id',
        'hire_date',
        'base_salary',
        'commission_rate',
        'assigned_areas',
        'status',
        'notes',
    ];

    protected $casts = [
        'assigned_areas' => 'array',
        'hire_date' => 'date',
        'base_salary' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    // العلاقات
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visits()
    {
        return $this->hasMany(CustomerVisit::class);
    }

    public function latestVisits()
    {
        return $this->hasMany(CustomerVisit::class)->orderBy('visit_date', 'desc');
    }

    public function orders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function latestOrders()
    {
        return $this->hasMany(SalesOrder::class)->orderBy('order_date', 'desc');
    }

    public function collections()
    {
        return $this->hasMany(PaymentCollection::class);
    }

    public function latestCollections()
    {
        return $this->hasMany(PaymentCollection::class)->orderBy('collection_date', 'desc');
    }

    public function reminders()
    {
        return $this->hasMany(CollectionReminder::class);
    }

    public function latestReminders()
    {
        return $this->hasMany(CollectionReminder::class)->orderBy('reminder_date', 'desc');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Accessors & Mutators
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }
}
