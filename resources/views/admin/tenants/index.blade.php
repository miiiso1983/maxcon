@extends('admin.layouts.app')

@section('title', 'إدارة المستأجرين')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">إدارة المستأجرين</h1>
                    <p class="text-muted">إدارة جميع المستأجرين في النظام</p>
                </div>
                <div>
                    @can('create_tenants')
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إضافة مستأجر جديد
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المستأجرين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                المستأجرين النشطين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['active']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                التراخيص المنتهية
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['expired']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                النسخ التجريبية
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['trial']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- فلاتر البحث -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلاتر البحث</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tenants.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">البحث</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="اسم المستأجر أو البريد الإلكتروني">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="license_type">نوع الترخيص</label>
                            <select class="form-control" id="license_type" name="license_type">
                                <option value="">جميع الأنواع</option>
                                <option value="trial" {{ request('license_type') == 'trial' ? 'selected' : '' }}>تجريبي</option>
                                <option value="basic" {{ request('license_type') == 'basic' ? 'selected' : '' }}>أساسي</option>
                                <option value="professional" {{ request('license_type') == 'professional' ? 'selected' : '' }}>احترافي</option>
                                <option value="enterprise" {{ request('license_type') == 'enterprise' ? 'selected' : '' }}>مؤسسي</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="expiring_soon" name="expiring_soon" value="1" 
                                           {{ request('expiring_soon') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="expiring_soon">
                                        منتهي قريباً
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    بحث
                                </button>
                                <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول المستأجرين -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة المستأجرين</h6>
        </div>
        <div class="card-body">
            @if($tenants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>المستأجر</th>
                                <th>نوع الترخيص</th>
                                <th>الحالة</th>
                                <th>المستخدمين</th>
                                <th>التخزين</th>
                                <th>انتهاء الترخيص</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenants as $tenant)
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
                                                <small class="text-muted">{{ $tenant->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $tenant->license_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'suspended' ? 'warning' : 'danger') }}">
                                            {{ $tenant->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-{{ $tenant->current_users_count > $tenant->max_users ? 'danger' : 'success' }}">
                                            {{ $tenant->current_users_count }}/{{ $tenant->max_users }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-{{ $tenant->current_storage_gb > $tenant->max_storage_gb ? 'danger' : 'success' }}">
                                            {{ $tenant->current_storage_gb }}/{{ $tenant->max_storage_gb }} GB
                                        </span>
                                    </td>
                                    <td>
                                        @if($tenant->license_end_date)
                                            <span class="text-{{ $tenant->license_end_date->isPast() ? 'danger' : ($tenant->license_end_date->diffInDays() < 30 ? 'warning' : 'success') }}">
                                                {{ $tenant->license_end_date->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($tenant->status === 'active')
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="suspendTenant({{ $tenant->id }})">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @elseif($tenant->status === 'suspended')
                                                <form method="POST" action="{{ route('admin.tenants.unsuspend', $tenant) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $tenants->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد مستأجرين</h5>
                    <p class="text-muted">لم يتم العثور على أي مستأجرين مطابقين للفلاتر المحددة</p>
                    @can('create_tenants')
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إضافة مستأجر جديد
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal لتعليق المستأجر -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعليق المستأجر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="suspendForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason">سبب التعليق</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required 
                                  placeholder="يرجى توضيح سبب تعليق المستأجر..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">تعليق المستأجر</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function suspendTenant(tenantId) {
    const form = document.getElementById('suspendForm');
    form.action = `/admin/tenants/${tenantId}/suspend`;
    
    const modal = new bootstrap.Modal(document.getElementById('suspendModal'));
    modal.show();
}
</script>
@endpush
