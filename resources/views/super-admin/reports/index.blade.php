@extends('super-admin.layouts.app')

@section('title', 'التقارير المتقدمة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar me-2"></i>
                التقارير المتقدمة
            </h1>
            <p class="text-muted">تقارير شاملة وإحصائيات مفصلة للنظام</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المستأجرين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Tenant::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-primary"></i>
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
                                التراخيص النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\License::where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-success"></i>
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
                                أنشطة اليوم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\AdminAuditLog::whereDate('created_at', today())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-info"></i>
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
                                تراخيص تنتهي قريباً
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Tenant::where('license_end_date', '>', now())->where('license_end_date', '<=', now()->addDays(30))->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row">
        <!-- Tenant Reports -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building me-2"></i>
                        تقارير المستأجرين
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">تقارير مفصلة حول المستأجرين وأنشطتهم</p>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير المستأجرين النشطين</h6>
                                <small class="text-muted">قائمة بجميع المستأجرين النشطين وتفاصيلهم</small>
                            </div>
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير الاستخدام</h6>
                                <small class="text-muted">إحصائيات استخدام المستأجرين للنظام</small>
                            </div>
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير النمو</h6>
                                <small class="text-muted">نمو عدد المستأجرين عبر الوقت</small>
                            </div>
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Reports -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-key me-2"></i>
                        تقارير التراخيص
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">تقارير شاملة حول التراخيص وحالتها</p>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير التراخيص النشطة</h6>
                                <small class="text-muted">جميع التراخيص النشطة وتفاصيلها</small>
                            </div>
                            <button class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير انتهاء الصلاحية</h6>
                                <small class="text-muted">التراخيص المنتهية والتي تنتهي قريباً</small>
                            </div>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير الإيرادات</h6>
                                <small class="text-muted">إيرادات التراخيص والاشتراكات</small>
                            </div>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Reports -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-cogs me-2"></i>
                        تقارير النظام
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">تقارير حول أداء وحالة النظام</p>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير الأداء</h6>
                                <small class="text-muted">إحصائيات أداء النظام والخوادم</small>
                            </div>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير الأخطاء</h6>
                                <small class="text-muted">سجل الأخطاء والمشاكل التقنية</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تقرير الأمان</h6>
                                <small class="text-muted">محاولات الدخول والأنشطة المشبوهة</small>
                            </div>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Reports -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-purple">
                        <i class="fas fa-chart-line me-2"></i>
                        تقارير مخصصة
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">إنشاء تقارير مخصصة حسب احتياجاتك</p>
                    
                    <form>
                        <div class="mb-3">
                            <label for="reportType" class="form-label">نوع التقرير</label>
                            <select class="form-select" id="reportType">
                                <option value="">اختر نوع التقرير</option>
                                <option value="tenants">المستأجرين</option>
                                <option value="licenses">التراخيص</option>
                                <option value="activities">الأنشطة</option>
                                <option value="financial">المالي</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dateFrom" class="form-label">من تاريخ</label>
                                    <input type="date" class="form-control" id="dateFrom">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dateTo" class="form-label">إلى تاريخ</label>
                                    <input type="date" class="form-control" id="dateTo">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="format" class="form-label">تنسيق التصدير</label>
                            <select class="form-select" id="format">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn btn-primary" onclick="generateCustomReport()">
                            <i class="fas fa-file-export me-2"></i>
                            إنشاء التقرير
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>
                        نشاط النظام - آخر 7 أيام
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="activityChart" width="100%" height="30"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateCustomReport() {
    const reportType = document.getElementById('reportType').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const format = document.getElementById('format').value;
    
    if (!reportType) {
        alert('يرجى اختيار نوع التقرير');
        return;
    }
    
    if (!dateFrom || !dateTo) {
        alert('يرجى تحديد نطاق التاريخ');
        return;
    }
    
    // محاكاة إنشاء التقرير
    alert(`جاري إنشاء تقرير ${reportType} من ${dateFrom} إلى ${dateTo} بتنسيق ${format}`);
}

// محاكاة بيانات الرسم البياني
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart');
    if (ctx) {
        // يمكن إضافة Chart.js هنا لاحقاً
        ctx.getContext('2d').fillText('رسم بياني لنشاط النظام', 50, 50);
    }
});
</script>

<style>
.text-purple {
    color: #6f42c1 !important;
}
.border-left-purple {
    border-left: 4px solid #6f42c1 !important;
}
</style>
@endsection
