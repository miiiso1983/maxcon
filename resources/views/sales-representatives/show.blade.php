@extends('layouts.app')

@section('title', 'تفاصيل المندوب التجاري - ' . $salesRepresentative->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-tie me-2"></i>
                تفاصيل المندوب التجاري
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sales-representatives.dashboard') }}">المندوبين التجاريين</a></li>
                    <li class="breadcrumb-item active">{{ $salesRepresentative->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('sales-representatives.edit', $salesRepresentative) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>
                تعديل
            </a>
            <a href="{{ route('sales-representatives.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Representative Info Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        معلومات المندوب
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-initial bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ substr($salesRepresentative->name, 0, 1) }}
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $salesRepresentative->name }}</h5>
                        <p class="text-muted mb-0">{{ $salesRepresentative->employee_code }}</p>
                        <span class="badge bg-{{ $salesRepresentative->status == 'active' ? 'success' : 'secondary' }} mt-2">
                            {{ $salesRepresentative->status == 'active' ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="info-list">
                        <div class="info-item mb-3">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            <strong>البريد الإلكتروني:</strong><br>
                            <span class="text-muted">{{ $salesRepresentative->email }}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <strong>الهاتف:</strong><br>
                            <span class="text-muted">{{ $salesRepresentative->phone }}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <i class="fas fa-calendar text-muted me-2"></i>
                            <strong>تاريخ التوظيف:</strong><br>
                            <span class="text-muted">{{ $salesRepresentative->hire_date->format('Y-m-d') }}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <i class="fas fa-money-bill-wave text-muted me-2"></i>
                            <strong>الراتب الأساسي:</strong><br>
                            <span class="text-muted">{{ number_format($salesRepresentative->base_salary, 2) }} ريال</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <i class="fas fa-percentage text-muted me-2"></i>
                            <strong>نسبة العمولة:</strong><br>
                            <span class="text-muted">{{ $salesRepresentative->commission_rate }}%</span>
                        </div>
                        
                        @if($salesRepresentative->assigned_areas)
                        <div class="info-item mb-3">
                            <i class="fas fa-map text-muted me-2"></i>
                            <strong>المناطق المخصصة:</strong><br>
                            @foreach($salesRepresentative->assigned_areas as $area)
                                <span class="badge bg-secondary me-1">{{ $area }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        إجمالي الزيارات
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_visits'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        إجمالي الطلبات
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        إجمالي التحصيلات
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_collections'], 2) }} ريال</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        التذكيرات المعلقة
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_reminders'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bell fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information Tabs -->
    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="detailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="visits-tab" data-bs-toggle="tab" data-bs-target="#visits" type="button" role="tab">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        الزيارات الأخيرة
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                        <i class="fas fa-shopping-cart me-2"></i>
                        الطلبات الأخيرة
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="collections-tab" data-bs-toggle="tab" data-bs-target="#collections" type="button" role="tab">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        التحصيلات الأخيرة
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
            <div class="tab-content" id="detailTabsContent">
                <!-- Visits Tab -->
                <div class="tab-pane fade show active" id="visits" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>العميل</th>
                                    <th>تاريخ الزيارة</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th>المدة</th>
                                    <th>الموقع</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesRepresentative->latestVisits->take(10) as $visit)
                                <tr>
                                    <td>{{ $visit->customer->name ?? 'غير محدد' }}</td>
                                    <td>{{ $visit->visit_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($visit->visit_type == 'planned')
                                            <span class="badge bg-info">مخططة</span>
                                        @elseif($visit->visit_type == 'unplanned')
                                            <span class="badge bg-warning">غير مخططة</span>
                                        @elseif($visit->visit_type == 'follow_up')
                                            <span class="badge bg-primary">متابعة</span>
                                        @else
                                            <span class="badge bg-success">تحصيل</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->visit_status == 'completed')
                                            <span class="badge bg-success">مكتملة</span>
                                        @elseif($visit->visit_status == 'in_progress')
                                            <span class="badge bg-info">جارية</span>
                                        @elseif($visit->visit_status == 'scheduled')
                                            <span class="badge bg-warning">مجدولة</span>
                                        @else
                                            <span class="badge bg-danger">ملغية</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->duration_minutes)
                                            {{ $visit->duration_minutes }} دقيقة
                                        @else
                                            غير محدد
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->location_address)
                                            <small>{{ Str::limit($visit->location_address, 30) }}</small>
                                        @else
                                            غير محدد
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewVisitDetails({{ $visit->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        لا توجد زيارات مسجلة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-pane fade" id="orders" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>تاريخ الطلب</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesRepresentative->latestOrders->take(10) as $order)
                                <tr>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->customer->name ?? 'غير محدد' }}</td>
                                    <td>{{ $order->order_date->format('Y-m-d') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} ريال</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">{{ $order->status_text }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrderDetails({{ $order->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        لا توجد طلبات مسجلة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Collections Tab -->
                <div class="tab-pane fade" id="collections" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>رقم التحصيل</th>
                                    <th>العميل</th>
                                    <th>التاريخ</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesRepresentative->latestCollections->take(10) as $collection)
                                <tr>
                                    <td><strong>{{ $collection->collection_number }}</strong></td>
                                    <td>{{ $collection->customer->name ?? 'غير محدد' }}</td>
                                    <td>{{ $collection->collection_date->format('Y-m-d') }}</td>
                                    <td>{{ $collection->formatted_amount }}</td>
                                    <td>{{ $collection->payment_method_text }}</td>
                                    <td>
                                        <span class="badge bg-{{ $collection->status_color }}">{{ $collection->status_text }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewCollectionDetails({{ $collection->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        لا توجد تحصيلات مسجلة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reminders Tab -->
                <div class="tab-pane fade" id="reminders" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>العنوان</th>
                                    <th>العميل</th>
                                    <th>تاريخ التذكير</th>
                                    <th>المبلغ</th>
                                    <th>الأولوية</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesRepresentative->latestReminders->take(10) as $reminder)
                                <tr>
                                    <td>{{ $reminder->reminder_title }}</td>
                                    <td>{{ $reminder->customer->name ?? 'غير محدد' }}</td>
                                    <td>{{ $reminder->reminder_date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($reminder->amount_to_collect)
                                            {{ number_format($reminder->amount_to_collect, 2) }} ريال
                                        @else
                                            غير محدد
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $reminder->priority_color }}">{{ $reminder->priority_text }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $reminder->status == 'pending' ? 'warning' : ($reminder->status == 'completed' ? 'success' : 'info') }}">
                                            {{ $reminder->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewReminderDetails({{ $reminder->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        لا توجد تذكيرات مسجلة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewVisitDetails(visitId) {
    // عرض تفاصيل الزيارة
    console.log('View visit details:', visitId);
}

function viewOrderDetails(orderId) {
    // عرض تفاصيل الطلب
    console.log('View order details:', orderId);
}

function viewCollectionDetails(collectionId) {
    // عرض تفاصيل التحصيل
    console.log('View collection details:', collectionId);
}

function viewReminderDetails(reminderId) {
    // عرض تفاصيل التذكير
    console.log('View reminder details:', reminderId);
}
</script>
@endsection
