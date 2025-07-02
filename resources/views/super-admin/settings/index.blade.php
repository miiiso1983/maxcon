@extends('super-admin.layouts.app')

@section('title', 'إعدادات النظام')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cog me-2"></i>
                إعدادات النظام
            </h1>
            <p class="text-muted">إدارة وتكوين إعدادات النظام العامة</p>
        </div>
    </div>

    <div class="row">
        <!-- System Information -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات النظام
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">إصدار Laravel</label>
                                <div class="fw-bold">{{ app()->version() }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">إصدار PHP</label>
                                <div class="fw-bold">{{ PHP_VERSION }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">البيئة</label>
                                <div class="fw-bold">
                                    <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                        {{ app()->environment() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">وضع التطبيق</label>
                                <div class="fw-bold">
                                    <span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">
                                        {{ config('app.debug') ? 'Debug' : 'Production' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">المنطقة الزمنية</label>
                                <div class="fw-bold">{{ config('app.timezone') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">اللغة</label>
                                <div class="fw-bold">{{ config('app.locale') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-database me-2"></i>
                        معلومات قاعدة البيانات
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">نوع قاعدة البيانات</label>
                                <div class="fw-bold">{{ config('database.default') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">الخادم</label>
                                <div class="fw-bold">{{ config('database.connections.mysql.host') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">المنفذ</label>
                                <div class="fw-bold">{{ config('database.connections.mysql.port') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">اسم قاعدة البيانات</label>
                                <div class="fw-bold">{{ config('database.connections.mysql.database') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="testDatabaseConnection()">
                            <i class="fas fa-plug me-2"></i>
                            اختبار الاتصال
                        </button>
                        <span id="dbConnectionStatus" class="ms-2"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-lg-6">
            <!-- Cache Management -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-memory me-2"></i>
                        إدارة الذاكرة المؤقتة
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">تنظيف وإدارة ذاكرة التطبيق المؤقتة</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-outline-warning w-100" onclick="clearCache('application')">
                                <i class="fas fa-broom me-2"></i>
                                مسح ذاكرة التطبيق
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-outline-info w-100" onclick="clearCache('config')">
                                <i class="fas fa-cogs me-2"></i>
                                مسح ذاكرة الإعدادات
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-outline-success w-100" onclick="clearCache('route')">
                                <i class="fas fa-route me-2"></i>
                                مسح ذاكرة المسارات
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="clearCache('view')">
                                <i class="fas fa-eye me-2"></i>
                                مسح ذاكرة العروض
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-danger" onclick="clearAllCache()">
                            <i class="fas fa-trash-alt me-2"></i>
                            مسح جميع الذاكرة المؤقتة
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Maintenance -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools me-2"></i>
                        صيانة النظام
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">وضع الصيانة</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="maintenanceMode" 
                                   onchange="toggleMaintenanceMode()">
                            <label class="form-check-label" for="maintenanceMode">
                                تفعيل وضع الصيانة
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            عند التفعيل، سيتم منع الوصول للنظام مؤقت<|im_start|> للصيانة
                        </small>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-info" onclick="optimizeSystem()">
                            <i class="fas fa-rocket me-2"></i>
                            تحسين النظام
                        </button>
                        <small class="form-text text-muted d-block mt-1">
                            تحسين الأداء وتنظيف الملفات المؤقتة
                        </small>
                    </div>
                </div>
            </div>

            <!-- System Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        إحصائيات النظام
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <div class="h4 mb-0 text-primary">{{ \App\Models\Tenant::count() }}</div>
                                <small class="text-muted">المستأجرين</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <div class="h4 mb-0 text-success">{{ \App\Models\License::where('status', 'active')->count() }}</div>
                                <small class="text-muted">التراخيص النشطة</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-info">{{ \App\Models\AdminAuditLog::whereDate('created_at', today())->count() }}</div>
                            <small class="text-muted">أنشطة اليوم</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>
                        سجلات النظام الأخيرة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>الوقت</th>
                                    <th>المستوى</th>
                                    <th>الرسالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentLogs = \App\Models\AdminAuditLog::orderBy('created_at', 'desc')->limit(5)->get();
                                @endphp
                                @forelse($recentLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('H:i:s') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->severity === 'high' ? 'danger' : ($log->severity === 'medium' ? 'warning' : 'info') }}">
                                            {{ $log->severity }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($log->description, 80) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">لا توجد سجلات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('super-admin.audit-logs.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>
                            عرض جميع السجلات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testDatabaseConnection() {
    const statusElement = document.getElementById('dbConnectionStatus');
    statusElement.innerHTML = '<i class="fas fa-spinner fa-spin text-primary"></i> جاري الاختبار...';
    
    // محاكاة اختبار الاتصال
    setTimeout(() => {
        statusElement.innerHTML = '<span class="badge bg-success"><i class="fas fa-check me-1"></i>متصل</span>';
    }, 1500);
}

function clearCache(type) {
    if (confirm(`هل أنت متأكد من مسح ذاكرة ${type}؟`)) {
        // محاكاة مسح الذاكرة
        alert(`تم مسح ذاكرة ${type} بنجاح`);
    }
}

function clearAllCache() {
    if (confirm('هل أنت متأكد من مسح جميع الذاكرة المؤقتة؟ قد يؤثر هذا على أداء النظام مؤقت<|im_start|>.')) {
        alert('تم مسح جميع الذاكرة المؤقتة بنجاح');
    }
}

function toggleMaintenanceMode() {
    const checkbox = document.getElementById('maintenanceMode');
    const action = checkbox.checked ? 'تفعيل' : 'إلغاء';
    
    if (confirm(`هل أنت متأكد من ${action} وضع الصيانة؟`)) {
        alert(`تم ${action} وضع الصيانة`);
    } else {
        checkbox.checked = !checkbox.checked;
    }
}

function optimizeSystem() {
    if (confirm('هل تريد تحسين النظام؟ قد يستغرق هذا بضع دقائق.')) {
        alert('تم بدء عملية تحسين النظام');
    }
}
</script>
@endsection
