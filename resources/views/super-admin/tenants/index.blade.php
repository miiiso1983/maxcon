@extends('super-admin.layouts.app')

@section('title', 'إدارة المستأجرين')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                إدارة المستأجرين
            </h1>
            <p class="text-muted">إدارة وتتبع جميع المستأجرين في النظام</p>
        </div>
        <div>
            <a href="{{ route('super-admin.tenants.credentials') }}" class="btn btn-info me-2">
                <i class="fas fa-key me-2"></i>
                بيانات الدخول
            </a>
            <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة مستأجر جديد
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                المستأجرين النشطين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                في الانتظار
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                معلقين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['suspended'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                إجمالي المستأجرين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>
                تصفية المستأجرين
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.tenants.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status" class="form-label">الحالة</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label">البحث</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="اسم المستأجر، البريد الإلكتروني، أو رقم الهاتف" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                بحث
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                قائمة المستأجرين
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tenantsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>المستأجر</th>
                            <th>معلومات الاتصال</th>
                            <th>الترخيص</th>
                            <th>المستخدمين</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants ?? [] as $tenant)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            {{ substr($tenant->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $tenant->name }}</h6>
                                        <small class="text-muted">{{ Str::limit($tenant->address, 30) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-envelope me-1"></i>
                                    <small>{{ $tenant->email }}</small>
                                </div>
                                <div>
                                    <i class="fas fa-phone me-1"></i>
                                    <small>{{ $tenant->phone }}</small>
                                </div>
                            </td>
                            <td>
                                @if($tenant->license_type)
                                    <div>
                                        <span class="badge bg-info">{{ $tenant->license_type }}</span>
                                    </div>
                                    <small class="text-muted">
                                        ينتهي: {{ $tenant->license_end_date ? \Carbon\Carbon::parse($tenant->license_end_date)->format('Y-m-d') : 'غير محدد' }}
                                    </small>
                                @else
                                    <span class="badge bg-secondary">لا يوجد ترخيص</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-users me-1"></i>
                                    {{ $tenant->users->count() }} مستخدم
                                </div>
                                <small class="text-muted">
                                    نشط: {{ $tenant->users->where('status', 'active')->count() }}
                                </small>
                            </td>
                            <td>
                                @if($tenant->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($tenant->status == 'pending')
                                    <span class="badge bg-warning">في الانتظار</span>
                                @elseif($tenant->status == 'suspended')
                                    <span class="badge bg-danger">معلق</span>
                                @else
                                    <span class="badge bg-secondary">{{ $tenant->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $tenant->created_at->format('Y-m-d') }}</div>
                                <small class="text-muted">{{ $tenant->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('super-admin.tenants.show', $tenant->id) }}" 
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.tenants.edit', $tenant->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($tenant->status == 'active')
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="suspendTenant({{ $tenant->id }})" title="تعليق">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @elseif($tenant->status == 'suspended')
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="activateTenant({{ $tenant->id }})" title="تفعيل">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    @if($tenant->status != 'active')
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteTenant({{ $tenant->id }})" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد مستأجرين</p>
                                <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    إضافة مستأجر جديد
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($tenants) && method_exists($tenants, 'links'))
                <div class="d-flex justify-content-center">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Scripts -->
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
