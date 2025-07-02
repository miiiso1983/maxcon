@extends('admin.layouts.app')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">لوحة التحكم الرئيسية</h1>
                    <p class="text-muted">مرحباً {{ auth('super_admin')->user()->name }}، إليك نظرة عامة على النظام</p>
                </div>
                <div>
                    <span class="badge bg-success">النظام يعمل بشكل طبيعي</span>
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
                                {{ number_format($stats['total_tenants']) }}
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
                                {{ number_format($stats['active_tenants']) }}
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                إجمالي المستخدمين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_users']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                الإيرادات الشهرية
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($revenueStats['total_revenue'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الرسوم البيانية والتقارير -->
    <div class="row">
        <!-- رسم بياني للمستأجرين الجدد -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">المستأجرين الجدد - آخر 30 يوم</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">خيارات:</div>
                            <a class="dropdown-item" href="#">تصدير البيانات</a>
                            <a class="dropdown-item" href="#">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="newTenantsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- توزيع أنواع التراخيص -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع أنواع التراخيص</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="licenseDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المستأجرين الجدد والتراخيص المنتهية -->
    <div class="row">
        <!-- المستأجرين الجدد -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث المستأجرين</h6>
                </div>
                <div class="card-body">
                    @if($newTenants->count() > 0)
                        @foreach($newTenants as $tenant)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <div class="avatar-initial bg-primary rounded-circle">
                                        {{ substr($tenant->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $tenant->name }}</h6>
                                    <small class="text-muted">{{ $tenant->email }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : 'warning' }}">
                                        {{ $tenant->status }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $tenant->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">لا توجد مستأجرين جدد</p>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-primary btn-sm">
                            عرض جميع المستأجرين
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- التراخيص المنتهية قريباً -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">التراخيص المنتهية قريباً</h6>
                </div>
                <div class="card-body">
                    @if($expiringLicenses->count() > 0)
                        @foreach($expiringLicenses->take(5) as $tenant)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <div class="avatar-initial bg-warning rounded-circle">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $tenant->name }}</h6>
                                    <small class="text-muted">{{ $tenant->license_type }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning">
                                        {{ $tenant->getDaysUntilExpiry() }} يوم
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $tenant->license_end_date->format('Y-m-d') }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">لا توجد تراخيص منتهية قريباً</p>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.tenants.index', ['expiring_soon' => 1]) }}" class="btn btn-warning btn-sm">
                            عرض جميع التراخيص المنتهية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث الأنشطة -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث الأنشطة</h6>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>النشاط</th>
                                        <th>المستخدم</th>
                                        <th>المستأجر</th>
                                        <th>الوقت</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                        <tr>
                                            <td>
                                                <i class="fas fa-{{ $activity->action === 'login' ? 'sign-in-alt' : 'cog' }} me-2"></i>
                                                {{ $activity->description }}
                                            </td>
                                            <td>{{ $activity->superAdmin->name ?? 'غير محدد' }}</td>
                                            <td>{{ $activity->tenant->name ?? '-' }}</td>
                                            <td>{{ $activity->created_at->diffForHumans() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $activity->status === 'success' ? 'success' : 'danger' }}">
                                                    {{ $activity->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">لا توجد أنشطة حديثة</p>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-primary btn-sm">
                            عرض جميع سجلات التدقيق
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// رسم بياني للمستأجرين الجدد
const newTenantsCtx = document.getElementById('newTenantsChart').getContext('2d');
const newTenantsChart = new Chart(newTenantsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($chartData['new_tenants'])) !!},
        datasets: [{
            label: 'مستأجرين جدد',
            data: {!! json_encode(array_values($chartData['new_tenants'])) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// رسم بياني لتوزيع التراخيص
const licenseCtx = document.getElementById('licenseDistributionChart').getContext('2d');
const licenseChart = new Chart(licenseCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($chartData['license_distribution'])) !!},
        datasets: [{
            data: {!! json_encode(array_values($chartData['license_distribution'])) !!},
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
