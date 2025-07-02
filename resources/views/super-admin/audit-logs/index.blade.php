@extends('super-admin.layouts.app')

@section('title', 'سجلات التدقيق')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-alt me-2"></i>
                سجلات التدقيق
            </h1>
            <p class="text-muted">مراقبة وتتبع جميع أنشطة النظام</p>
        </div>
        <div>
            <a href="{{ route('super-admin.audit-logs.export') }}" class="btn btn-success me-2">
                <i class="fas fa-download me-2"></i>
                تصدير CSV
            </a>
            <a href="{{ route('super-admin.audit-logs.statistics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar me-2"></i>
                الإحصائيات
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                إجمالي السجلات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                اليوم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                هذا الأسبوع
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['this_week'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-success"></i>
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
                                العمليات الفاشلة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['failed'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
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
                تصفية السجلات
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.audit-logs.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <label for="category" class="form-label">الفئة</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">جميع الفئات</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="severity" class="form-label">الخطورة</label>
                        <select name="severity" id="severity" class="form-select">
                            <option value="">جميع المستويات</option>
                            <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>منخفضة</option>
                            <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>عالية</option>
                            <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>حرجة</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">الحالة</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>نجح</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                بحث
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">البحث</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="البحث في الإجراء، الوصف، أو عنوان IP" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="super_admin_id" class="form-label">السوبر أدمن</label>
                        <select name="super_admin_id" id="super_admin_id" class="form-select">
                            <option value="">جميع المديرين</option>
                            @foreach($superAdmins ?? [] as $admin)
                                <option value="{{ $admin->id }}" {{ request('super_admin_id') == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tenant_id" class="form-label">المستأجر</label>
                        <select name="tenant_id" id="tenant_id" class="form-select">
                            <option value="">جميع المستأجرين</option>
                            @foreach($tenants ?? [] as $tenant)
                                <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                سجلات التدقيق
            </h6>
            <div>
                <button type="button" class="btn btn-warning btn-sm" onclick="showCleanupModal()">
                    <i class="fas fa-trash me-2"></i>
                    تنظيف السجلات القديمة
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>التاريخ والوقت</th>
                            <th>السوبر أدمن</th>
                            <th>المستأجر</th>
                            <th>الإجراء</th>
                            <th>الوصف</th>
                            <th>الفئة</th>
                            <th>الخطورة</th>
                            <th>الحالة</th>
                            <th>عنوان IP</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs ?? [] as $log)
                        <tr>
                            <td>
                                <div>{{ $log->created_at->format('Y-m-d') }}</div>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div>{{ $log->superAdmin->name ?? 'غير محدد' }}</div>
                                <small class="text-muted">{{ $log->superAdmin->email ?? '' }}</small>
                            </td>
                            <td>
                                @if($log->tenant)
                                    <div>{{ $log->tenant->name }}</div>
                                    <small class="text-muted">{{ $log->tenant->email }}</small>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                <code>{{ $log->action }}</code>
                            </td>
                            <td>
                                <div title="{{ $log->description }}">
                                    {{ Str::limit($log->description, 50) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $log->category }}</span>
                            </td>
                            <td>
                                @if($log->severity == 'low')
                                    <span class="badge bg-info">منخفضة</span>
                                @elseif($log->severity == 'medium')
                                    <span class="badge bg-warning">متوسطة</span>
                                @elseif($log->severity == 'high')
                                    <span class="badge bg-danger">عالية</span>
                                @elseif($log->severity == 'critical')
                                    <span class="badge bg-dark">حرجة</span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->severity }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->status == 'success')
                                    <span class="badge bg-success">نجح</span>
                                @elseif($log->status == 'failed')
                                    <span class="badge bg-danger">فشل</span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $log->ip_address }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('super-admin.audit-logs.show', $log->id) }}" 
                                       class="btn btn-sm btn-outline-info" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteLog({{ $log->id }})" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد سجلات تدقيق</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($logs) && method_exists($logs, 'links'))
                <div class="d-flex justify-content-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تنظيف السجلات القديمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.audit-logs.cleanup') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="days" class="form-label">حذف السجلات الأقدم من (بالأيام)</label>
                        <input type="number" name="days" id="days" class="form-control" 
                               min="30" max="365" value="90" required>
                        <small class="form-text text-muted">
                            سيتم حذف جميع السجلات الأقدم من العدد المحدد من الأيام
                        </small>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        تحذير: هذه العملية لا يمكن التراجع عنها!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حذف السجلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCleanupModal() {
    new bootstrap.Modal(document.getElementById('cleanupModal')).show();
}

function deleteLog(logId) {
    if (confirm('هل أنت متأكد من حذف هذا السجل؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/super-admin/audit-logs/${logId}`;
        
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
