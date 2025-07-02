<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollectionReminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'sales_representative_id',
        'customer_id',
        'invoice_id',
        'reminder_title',
        'reminder_description',
        'reminder_date',
        'reminder_time',
        'amount_to_collect',
        'priority',
        'status',
        'notified_at',
        'completed_at',
        'completion_notes',
        'notification_methods',
        'auto_created',
    ];

    protected $casts = [
        'reminder_date' => 'datetime',
        'due_date' => 'datetime',
        'notified_at' => 'datetime',
        'completed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'reminder_time' => 'datetime:H:i',
        'amount_to_collect' => 'decimal:2',
        'notified_at' => 'datetime',
        'completed_at' => 'datetime',
        'notification_methods' => 'array',
        'auto_created' => 'boolean',
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('reminder_date', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('reminder_date', '<', today())
                    ->where('status', 'pending');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Accessors & Mutators
    public function getPriorityTextAttribute()
    {
        $priorities = [
            'low' => 'منخفضة',
            'normal' => 'عادية',
            'high' => 'عالية',
            'urgent' => 'عاجلة'
        ];

        return $priorities[$this->priority] ?? $this->priority;
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'secondary',
            'normal' => 'info',
            'high' => 'warning',
            'urgent' => 'danger'
        ];

        return $colors[$this->priority] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'notified' => 'تم التنبيه',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getIsOverdueAttribute()
    {
        return $this->reminder_date < today() && $this->status === 'pending';
    }

    public function getDaysUntilDueAttribute()
    {
        return today()->diffInDays($this->reminder_date, false);
    }

    // Methods
    public function markAsNotified()
    {
        $this->update([
            'status' => 'notified',
            'notified_at' => now()
        ]);
    }

    public function markAsCompleted($notes = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $notes
        ]);
    }

    public function snooze($days = 1)
    {
        $this->update([
            'reminder_date' => $this->reminder_date->addDays($days)
        ]);
    }
}
