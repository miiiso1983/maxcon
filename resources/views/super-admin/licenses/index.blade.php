@extends('super-admin.layouts.app')

@section('title', 'إدارة التراخيص')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-key me-2"></i>
                إدارة التراخيص
            </h1>
            <p class="text-muted">إدارة وتتبع تراخيص المستأجرين</p>
        </div>
        <div>
            <a href="{{ route('super-admin.licenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة ترخيص جديد
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
                                التراخيص النشطة
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
                                تنتهي قريباً
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expiring_soon'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
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
                                منتهية الصلاحية
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expired'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
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
                                إجمالي التراخيص
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-info"></i>
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
                تصفية التراخيص
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.licenses.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status" class="form-label">الحالة</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label">نوع الترخيص</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <option value="basic" {{ request('type') == 'basic' ? 'selected' : '' }}>أساسي</option>
                            <option value="premium" {{ request('type') == 'premium' ? 'selected' : '' }}>مميز</option>
                            <option value="enterprise" {{ request('type') == 'enterprise' ? 'selected' : '' }}>مؤسسي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">البحث</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="اسم المستأجر أو البريد الإلكتروني" value="{{ request('search') }}">
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

    <!-- Licenses Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                قائمة التراخيص
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="licensesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>المستأجر</th>
                            <th>نوع الترخيص</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($licenses ?? [] as $license)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            {{ substr($license->tenant->name ?? 'N/A', 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $license->tenant->name ?? 'غير محدد' }}</h6>
                                        <small class="text-muted">{{ $license->tenant->email ?? 'غير محدد' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $license->type ?? 'غير محدد' }}</span>
                            </td>
                            <td>{{ $license->starts_at ? $license->starts_at->format('Y-m-d') : 'غير محدد' }}</td>
                            <td>
                                @if($license->tenant && $license->tenant->license_end_date)
                                    {{ \Carbon\Carbon::parse($license->tenant->license_end_date)->format('Y-m-d') }}
                                @else
                                    غير محدد
                                @endif
                            </td>
                            <td>
                                @if($license->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($license->status == 'expired')
                                    <span class="badge bg-danger">منتهي الصلاحية</span>
                                @elseif($license->status == 'suspended')
                                    <span class="badge bg-warning">معلق</span>
                                @else
                                    <span class="badge bg-secondary">{{ $license->status ?? 'غير محدد' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('super-admin.licenses.show', $license->id) }}" 
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.licenses.edit', $license->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($license->status == 'active')
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="suspendLicense({{ $license->id }})" title="تعليق">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @endif
                                    @if($license->tenant && $license->tenant->license_end_date && \Carbon\Carbon::parse($license->tenant->license_end_date)->isPast())
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="renewLicense({{ $license->id }})" title="تجديد">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد تراخيص</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($licenses) && method_exists($licenses, 'links'))
                <div class="d-flex justify-content-center">
                    {{ $licenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals and Scripts -->
<script>
function suspendLicense(licenseId) {
    if (confirm('هل أنت متأكد من تعليق هذا الترخيص؟')) {
        fetch(`/super-admin/licenses/${licenseId}/suspend`, {
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

function renewLicense(licenseId) {
    const months = prompt('كم شهر تريد تجديد الترخيص؟', '12');
    if (months && !isNaN(months) && months > 0) {
        fetch(`/super-admin/licenses/${licenseId}/renew`, {
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
    }
}
</script>
@endsection
