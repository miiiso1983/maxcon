@extends('layouts.app')

@section('title', 'لوحة تحكم المندوبين التجاريين')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-tie me-2"></i>
                لوحة تحكم المندوبين التجاريين
            </h1>
            <p class="text-muted">إدارة شاملة للمندوبين والزيارات والطلبات</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary me-2" onclick="addNewRepresentative()">
                <i class="fas fa-plus me-2"></i>
                إضافة مندوب جديد
            </button>
            <button type="button" class="btn btn-success" onclick="exportReports()">
                <i class="fas fa-download me-2"></i>
                تصدير التقارير
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المندوبين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_reps'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                زيارات اليوم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_visits'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
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
                                طلبات اليوم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_orders'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                                تحصيلات اليوم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today_collections'] ?? 0, 2) }} ريال</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="mainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="representatives-tab" data-bs-toggle="tab" data-bs-target="#representatives" type="button" role="tab">
                        <i class="fas fa-users me-2"></i>
                        المندوبين
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="visits-tab" data-bs-toggle="tab" data-bs-target="#visits" type="button" role="tab">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        الزيارات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                        <i class="fas fa-shopping-cart me-2"></i>
                        الطلبات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="collections-tab" data-bs-toggle="tab" data-bs-target="#collections" type="button" role="tab">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        التحصيلات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reminders-tab" data-bs-toggle="tab" data-bs-target="#reminders" type="button" role="tab">
                        <i class="fas fa-bell me-2"></i>
                        التذكيرات
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="mainTabsContent">
                <!-- Representatives Tab -->
                <div class="tab-pane fade show active" id="representatives" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">قائمة المندوبين التجاريين</h5>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control" placeholder="البحث في المندوبين..." id="searchReps">
                            <select class="form-select" id="filterRepStatus">
                                <option value="">جميع الحالات</option>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                                <option value="suspended">معلق</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="representativesTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>كود المندوب</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>المناطق المخصصة</th>
                                    <th>الحالة</th>
                                    <th>آخر زيارة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($representatives ?? [] as $rep)
                                <tr>
                                    <td><strong>{{ $rep->employee_code }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <div class="avatar-initial bg-primary rounded-circle">
                                                    {{ substr($rep->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $rep->name }}</h6>
                                                <small class="text-muted">منذ {{ $rep->hire_date->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $rep->email }}</td>
                                    <td>{{ $rep->phone }}</td>
                                    <td>
                                        @if($rep->assigned_areas)
                                            @foreach($rep->assigned_areas as $area)
                                                <span class="badge bg-secondary me-1">{{ $area }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rep->status == 'active')
                                            <span class="badge bg-success">نشط</span>
                                        @elseif($rep->status == 'inactive')
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @else
                                            <span class="badge bg-warning">معلق</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rep->visits->count() > 0)
                                            {{ $rep->visits->first()->visit_date->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted">لا توجد زيارات</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="viewRepDetails({{ $rep->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" onclick="editRep({{ $rep->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info" onclick="viewRepReports({{ $rep->id }})">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>لا يوجد مندوبين مسجلين</p>
                                        <button type="button" class="btn btn-primary" onclick="addNewRepresentative()">
                                            إضافة مندوب جديد
                                        </button>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Visits Tab -->
                <div class="tab-pane fade" id="visits" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">زيارات العملاء</h5>
                        <div class="d-flex gap-2">
                            <input type="date" class="form-control" id="visitDateFilter" value="{{ date('Y-m-d') }}">
                            <select class="form-select" id="visitRepFilter">
                                <option value="">جميع المندوبين</option>
                                @foreach($representatives ?? [] as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" onclick="refreshVisits()">
                                <i class="fas fa-sync"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="visitsContainer">
                        <!-- سيتم تحميل الزيارات هنا عبر AJAX -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-pane fade" id="orders" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">طلبات المبيعات</h5>
                        <div class="d-flex gap-2">
                            <input type="date" class="form-control" id="orderDateFilter" value="{{ date('Y-m-d') }}">
                            <select class="form-select" id="orderStatusFilter">
                                <option value="">جميع الحالات</option>
                                <option value="draft">مسودة</option>
                                <option value="confirmed">مؤكد</option>
                                <option value="processing">قيد المعالجة</option>
                                <option value="shipped">تم الشحن</option>
                                <option value="delivered">تم التسليم</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="ordersContainer">
                        <!-- سيتم تحميل الطلبات هنا عبر AJAX -->
                    </div>
                </div>

                <!-- Collections Tab -->
                <div class="tab-pane fade" id="collections" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">تحصيلات المدفوعات</h5>
                        <div class="d-flex gap-2">
                            <input type="date" class="form-control" id="collectionDateFilter" value="{{ date('Y-m-d') }}">
                            <select class="form-select" id="collectionMethodFilter">
                                <option value="">جميع الطرق</option>
                                <option value="cash">نقدي</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="check">شيك</option>
                                <option value="credit_card">بطاقة ائتمان</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="collectionsContainer">
                        <!-- سيتم تحميل التحصيلات هنا عبر AJAX -->
                    </div>
                </div>

                <!-- Reminders Tab -->
                <div class="tab-pane fade" id="reminders" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">تذكيرات التحصيل</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select" id="reminderStatusFilter">
                                <option value="">جميع الحالات</option>
                                <option value="pending">في الانتظار</option>
                                <option value="notified">تم التنبيه</option>
                                <option value="completed">مكتمل</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                            <button type="button" class="btn btn-success" onclick="addReminder()">
                                <i class="fas fa-plus me-2"></i>
                                إضافة تذكير
                            </button>
                        </div>
                    </div>
                    
                    <div id="remindersContainer">
                        <!-- سيتم تحميل التذكيرات هنا عبر AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Representative Modal -->
<div class="modal fade" id="addRepresentativeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة مندوب تجاري جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRepresentativeForm">
                <div class="modal-body">
                    <!-- سيتم إضافة نموذج إضافة المندوب هنا -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ المندوب</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JavaScript functions will be added here
function addNewRepresentative() {
    $('#addRepresentativeModal').modal('show');
}

function viewRepDetails(repId) {
    // عرض تفاصيل المندوب
    window.location.href = `/sales-representatives/${repId}`;
}

function editRep(repId) {
    // تعديل المندوب
    window.location.href = `/sales-representatives/${repId}/edit`;
}

function viewRepReports(repId) {
    // عرض تقارير المندوب
    window.location.href = `/sales-representatives/${repId}/reports`;
}

function refreshVisits() {
    // تحديث قائمة الزيارات
    loadVisits();
}

function loadVisits() {
    const date = $('#visitDateFilter').val();
    const repId = $('#visitRepFilter').val();
    
    $('#visitsContainer').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
    
    // AJAX call to load visits
    fetch(`/api/visits?date=${date}&rep_id=${repId}`)
        .then(response => response.json())
        .then(data => {
            // عرض الزيارات
            displayVisits(data);
        })
        .catch(error => {
            console.error('Error loading visits:', error);
        });
}

function displayVisits(visits) {
    // عرض الزيارات في الجدول
    let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-dark"><tr>';
    html += '<th>المندوب</th><th>العميل</th><th>وقت الزيارة</th><th>الموقع</th><th>الحالة</th><th>الإجراءات</th>';
    html += '</tr></thead><tbody>';
    
    visits.forEach(visit => {
        html += `<tr>
            <td>${visit.representative_name}</td>
            <td>${visit.customer_name}</td>
            <td>${visit.visit_date}</td>
            <td>${visit.location_address || 'غير محدد'}</td>
            <td><span class="badge bg-${getStatusColor(visit.visit_status)}">${getStatusText(visit.visit_status)}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewVisitDetails(${visit.id})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    $('#visitsContainer').html(html);
}

function getStatusColor(status) {
    const colors = {
        'scheduled': 'warning',
        'in_progress': 'info',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusText(status) {
    const texts = {
        'scheduled': 'مجدولة',
        'in_progress': 'جارية',
        'completed': 'مكتملة',
        'cancelled': 'ملغية'
    };
    return texts[status] || status;
}

function exportReports() {
    // تصدير التقارير
    window.open('/sales-representatives/export', '_blank');
}

// تحميل البيانات عند تغيير التبويبات
document.addEventListener('DOMContentLoaded', function() {
    // تحميل الزيارات عند فتح تبويب الزيارات
    document.getElementById('visits-tab').addEventListener('shown.bs.tab', function() {
        loadVisits();
    });
    
    // إعداد المرشحات
    $('#visitDateFilter, #visitRepFilter').on('change', function() {
        if ($('#visits').hasClass('show')) {
            loadVisits();
        }
    });
});
</script>
@endsection
