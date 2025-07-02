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

    <!-- الإحصائيات الرئيسية -->
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
                                {{ number_format($stats['monthly_revenue'], 2) }} ريال
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
    <div class="row mb-4">
        <!-- رسم بياني للمستأجرين الجدد -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">المستأجرين الجدد - آخر 30 يوم</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="tenantsChart"></canvas>
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
                        <canvas id="licensesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($licenseStats as $license)
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> {{ $license->license_type }} ({{ $license->count }})
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- التنبيهات والإشعارات -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">التراخيص المنتهية قريباً</h6>
                </div>
                <div class="card-body">
                    @if($expiringLicenses->count() > 0)
                        @foreach($expiringLicenses as $tenant)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3">
                                <div class="avatar-initial bg-warning rounded-circle">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $tenant->name }}</h6>
                                <small class="text-muted">
                                    ينتهي في {{ $tenant->license_end_date->diffForHumans() }}
                                </small>
                            </div>
                            <div>
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">
                                    عرض
                                </a>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">لا توجد تراخيص منتهية قريباً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث الأنشطة</h6>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        @foreach($recentActivities as $activity)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3">
                                <div class="avatar-initial bg-info rounded-circle">
                                    <i class="fas fa-{{ $activity->action === 'login' ? 'sign-in-alt' : 'cog' }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $activity->description }}</h6>
                                <small class="text-muted">
                                    {{ $activity->created_at->diffForHumans() }}
                                    @if($activity->superAdmin)
                                        بواسطة {{ $activity->superAdmin->name }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        @endforeach
                        <div class="text-center">
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-primary">
                                عرض جميع الأنشطة
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">لا توجد أنشطة حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث المستأجرين -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث المستأجرين</h6>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if($recentTenants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>المستأجر</th>
                                        <th>نوع الترخيص</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTenants as $tenant)
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
                                            <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : 'warning' }}">
                                                {{ $tenant->status }}
                                            </span>
                                        </td>
                                        <td>{{ $tenant->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">
                                                عرض
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد مستأجرين</h5>
                            <p class="text-muted">لم يتم إنشاء أي مستأجرين بعد</p>
                            <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إضافة مستأجر جديد
                            </a>
                        </div>
                    @endif
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
const tenantsCtx = document.getElementById('tenantsChart').getContext('2d');
const tenantsChart = new Chart(tenantsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartData['labels']) !!},
        datasets: [{
            label: 'مستأجرين جدد',
            data: {!! json_encode($chartData['data']) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// رسم بياني دائري للتراخيص
const licensesCtx = document.getElementById('licensesChart').getContext('2d');
const licensesChart = new Chart(licensesCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($licenseStats->pluck('license_type')) !!},
        datasets: [{
            data: {!! json_encode($licenseStats->pluck('count')) !!},
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush
