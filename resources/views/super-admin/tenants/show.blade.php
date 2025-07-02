@extends('super-admin.layouts.app')

@section('title', 'تفاصيل المستأجر')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                تفاصيل المستأجر: {{ $tenant->name }}
            </h1>
            <p class="text-muted">معلومات شاملة عن المستأجر وترخيصه</p>
        </div>
        <div>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('super-admin.tenants.edit', $tenant->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                تعديل
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Tenant Information -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        المعلومات الأساسية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">اسم المستأجر</label>
                                <div class="fw-bold">{{ $tenant->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">البريد الإلكتروني</label>
                                <div class="fw-bold">
                                    <a href="mailto:{{ $tenant->email }}">{{ $tenant->email }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">رقم الهاتف</label>
                                <div class="fw-bold">
                                    <a href="tel:{{ $tenant->phone }}">{{ $tenant->phone }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">شخص الاتصال</label>
                                <div class="fw-bold">{{ $tenant->contact_person }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label text-muted">العنوان</label>
                                <div class="fw-bold">{{ $tenant->address }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">الحالة</label>
                                <div>
                                    @if($tenant->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @elseif($tenant->status == 'pending')
                                        <span class="badge bg-warning">في الانتظار</span>
                                    @elseif($tenant->status == 'suspended')
                                        <span class="badge bg-danger">معلق</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $tenant->status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">تاريخ الإنشاء</label>
                                <div class="fw-bold">
                                    {{ $tenant->created_at->format('Y-m-d H:i') }}
                                    <small class="text-muted">({{ $tenant->created_at->diffForHumans() }})</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- License Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-key me-2"></i>
                        معلومات الترخيص
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">نوع الترخيص</label>
                                <div>
                                    @if($tenant->license_type == 'basic')
                                        <span class="badge bg-primary">أساسي</span>
                                    @elseif($tenant->license_type == 'premium')
                                        <span class="badge bg-success">مميز</span>
                                    @elseif($tenant->license_type == 'enterprise')
                                        <span class="badge bg-warning">مؤسسي</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $tenant->license_type ?? 'غير محدد' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">الرسوم الشهرية</label>
                                <div class="fw-bold">{{ $tenant->monthly_fee ?? 0 }} {{ $tenant->currency ?? 'SAR' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">تاريخ بداية الترخيص</label>
                                <div class="fw-bold">
                                    {{ $tenant->license_start_date ? \Carbon\Carbon::parse($tenant->license_start_date)->format('Y-m-d') : 'غير محدد' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">تاريخ انتهاء الترخيص</label>
                                <div class="fw-bold">
                                    @if($tenant->license_end_date)
                                        @php
                                            $endDate = \Carbon\Carbon::parse($tenant->license_end_date);
                                            $isExpired = $endDate->isPast();
                                            $isExpiringSoon = $endDate->isFuture() && $endDate->diffInDays() <= 30;
                                        @endphp
                                        <span class="badge bg-{{ $isExpired ? 'danger' : ($isExpiringSoon ? 'warning' : 'success') }}">
                                            {{ $endDate->format('Y-m-d') }}
                                        </span>
                                        <small class="d-block text-muted">
                                            @if($isExpired)
                                                انتهى منذ {{ $endDate->diffForHumans() }}
                                            @else
                                                ينتهي {{ $endDate->diffForHumans() }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">الحد الأقصى للمستخدمين</label>
                                <div class="fw-bold">{{ $tenant->max_users ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">مساحة التخزين</label>
                                <div class="fw-bold">{{ $tenant->max_storage_gb ?? 'غير محدد' }} جيجابايت</div>
                            </div>
                        </div>
                    </div>

                    @if($tenant->features)
                        <div class="mb-3">
                            <label class="form-label text-muted">المميزات المتاحة</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($tenant->features as $feature)
                                    <span class="badge bg-info">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($tenant->license_end_date && \Carbon\Carbon::parse($tenant->license_end_date)->isPast())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>تنبيه:</strong> انتهت صلاحية الترخيص! يجب تجديده فوراً.
                        </div>
                    @elseif($tenant->license_end_date && \Carbon\Carbon::parse($tenant->license_end_date)->diffInDays() <= 30)
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>تنبيه:</strong> سينتهي الترخيص خلال {{ \Carbon\Carbon::parse($tenant->license_end_date)->diffInDays() }} يوم.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Users Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-users me-2"></i>
                        المستخدمين ({{ $stats['users_count'] ?? 0 }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($tenant->users && $tenant->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenant->users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $user->role ?? 'مستخدم' }}</span>
                                        </td>
                                        <td>
                                            @if($user->status == 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>لا يوجد مستخدمين مسجلين</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics & Actions -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        إحصائيات سريعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 mb-0 text-primary">{{ $stats['users_count'] ?? 0 }}</div>
                                <small class="text-muted">إجمالي المستخدمين</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-success">{{ $stats['active_users'] ?? 0 }}</div>
                            <small class="text-muted">المستخدمين النشطين</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h6 mb-0 text-info">{{ $tenant->max_users ?? 'غير محدد' }}</div>
                                <small class="text-muted">الحد الأقصى</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h6 mb-0 text-warning">{{ $tenant->max_storage_gb ?? 'غير محدد' }} GB</div>
                            <small class="text-muted">مساحة التخزين</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        إجراءات سريعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($tenant->status == 'active')
                            <button type="button" class="btn btn-warning" onclick="suspendTenant({{ $tenant->id }})">
                                <i class="fas fa-pause me-2"></i>
                                تعليق المستأجر
                            </button>
                        @else
                            <button type="button" class="btn btn-success" onclick="activateTenant({{ $tenant->id }})">
                                <i class="fas fa-play me-2"></i>
                                تفعيل المستأجر
                            </button>
                        @endif
                        
                        @if($tenant->license_end_date && \Carbon\Carbon::parse($tenant->license_end_date)->isPast())
                            <button type="button" class="btn btn-info" onclick="renewLicense({{ $tenant->id }})">
                                <i class="fas fa-redo me-2"></i>
                                تجديد الترخيص
                            </button>
                        @endif
                        
                        <a href="{{ route('super-admin.tenants.edit', $tenant->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            تعديل البيانات
                        </a>
                        
                        @if($tenant->status != 'active')
                            <button type="button" class="btn btn-danger" onclick="deleteTenant({{ $tenant->id }})">
                                <i class="fas fa-trash me-2"></i>
                                حذف المستأجر
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-cog me-2"></i>
                        معلومات النظام
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">كود المستأجر:</small>
                        <div class="fw-bold">{{ $tenant->tenant_code ?? 'غير محدد' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">قاعدة البيانات:</small>
                        <div class="fw-bold">{{ $tenant->database_name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">دورة الفوترة:</small>
                        <div class="fw-bold">{{ $tenant->billing_cycle ?? 'شهرية' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">الفاتورة التالية:</small>
                        <div class="fw-bold">
                            {{ $tenant->next_billing_date ? \Carbon\Carbon::parse($tenant->next_billing_date)->format('Y-m-d') : 'غير محدد' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Renewal Modal -->
<div class="modal fade" id="renewalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تجديد الترخيص</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="renewalForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="months" class="form-label">عدد الأشهر للتجديد</label>
                        <select name="months" id="months" class="form-select" required>
                            <option value="1">شهر واحد</option>
                            <option value="3">3 أشهر</option>
                            <option value="6">6 أشهر</option>
                            <option value="12" selected>سنة واحدة</option>
                            <option value="24">سنتان</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تجديد الترخيص</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function activateTenant(tenantId) {
    if (confirm('هل أنت متأكد من تفعيل هذا المستأجر؟')) {
        fetch(`/super-admin/tenants/${tenantId}/activate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ غير متوقع');
        });
    }
}

function suspendTenant(tenantId) {
    if (confirm('هل أنت متأكد من تعليق هذا المستأجر؟ سيتم منع جميع المستخدمين من الوصول.')) {
        fetch(`/super-admin/tenants/${tenantId}/deactivate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ غير متوقع');
        });
    }
}

function renewLicense(tenantId) {
    new bootstrap.Modal(document.getElementById('renewalModal')).show();
    
    document.getElementById('renewalForm').onsubmit = function(e) {
        e.preventDefault();
        const months = document.getElementById('months').value;
        
        fetch(`/super-admin/licenses/${tenantId}/renew`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ months: parseInt(months) })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ غير متوقع');
        });
    };
}

function deleteTenant(tenantId) {
    if (confirm('هل أنت متأكد من حذف هذا المستأجر؟ سيتم حذف جميع البيانات المرتبطة به نهائياً!')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/super-admin/tenants/${tenantId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
