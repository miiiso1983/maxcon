@extends('layouts.app')

@section('title', 'لوحة تحكم المدير - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة التحكم</li>
@endsection

@push('styles')
<style>
    .dashboard-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px 0;
    }
    
    .dashboard-header {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .dashboard-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }
    
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 1rem;
        color: #718096;
        font-weight: 500;
        margin-bottom: 15px;
    }
    
    .stat-change {
        font-size: 0.9rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-block;
    }
    
    .stat-change.positive {
        background: rgba(72, 187, 120, 0.1);
        color: #38a169;
    }
    
    .stat-change.negative {
        background: rgba(245, 101, 101, 0.1);
        color: #e53e3e;
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    
    .content-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .content-card h3 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 25px;
        font-size: 1.4rem;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .action-btn {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 15px 20px;
        border-radius: 15px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .recent-activity {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .activity-item {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.2s ease;
    }
    
    .activity-item:hover {
        background-color: #f7fafc;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
        margin-left: 15px;
        background: linear-gradient(135deg, #667eea, #764ba2);
    }
    
    .activity-content h6 {
        margin-bottom: 5px;
        color: #2d3748;
        font-weight: 600;
    }
    
    .activity-content p {
        margin-bottom: 0;
        color: #718096;
        font-size: 0.9rem;
    }
    
    .activity-time {
        font-size: 0.8rem;
        color: #a0aec0;
    }
    
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .dashboard-title {
            font-size: 2rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt me-3"></i>
                لوحة تحكم المدير
            </h1>
            <p class="dashboard-subtitle">
                مرحباً {{ auth()->user()->name }}، إليك نظرة عامة على نظام إدارة المذاخر
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['total_orders']) }}</div>
                <div class="stat-label">إجمالي الطلبات</div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +12% من الشهر الماضي
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['pending_orders']) }}</div>
                <div class="stat-label">طلبات معلقة</div>
                <span class="stat-change negative">
                    <i class="fas fa-arrow-down"></i> -5% من الأسبوع الماضي
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['total_customers']) }}</div>
                <div class="stat-label">العملاء</div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +8% من الشهر الماضي
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['total_items']) }}</div>
                <div class="stat-label">المنتجات</div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +3% من الشهر الماضي
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['total_invoices_amount'], 2) }}</div>
                <div class="stat-label">إجمالي الفواتير (دينار)</div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +15% من الشهر الماضي
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['paid_amount'], 2) }}</div>
                <div class="stat-label">المبلغ المدفوع (دينار)</div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +10% من الشهر الماضي
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['low_stock_items']) }}</div>
                <div class="stat-label">منتجات قليلة المخزون</div>
                <span class="stat-change negative">
                    <i class="fas fa-arrow-up"></i> +2 من الأسبوع الماضي
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-number">{{ number_format($stats['today_collections'], 2) }}</div>
                <div class="stat-label">تحصيلات اليوم (دينار)</div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +25% من أمس
                </span>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Chart Section -->
            <div class="content-card">
                <h3>
                    <i class="fas fa-chart-line me-2"></i>
                    الإيرادات الشهرية
                </h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="content-card">
                <h3>
                    <i class="fas fa-clock me-2"></i>
                    النشاطات الأخيرة
                </h3>
                <div class="recent-activity">
                    @if(isset($recent_orders) && $recent_orders->count() > 0)
                        @foreach($recent_orders->take(5) as $order)
                        <div class="activity-item d-flex align-items-center">
                            <div class="activity-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content flex-grow-1">
                                <h6>طلب جديد #{{ $order->id }}</h6>
                                <p>{{ $order->customer->name ?? 'عميل غير محدد' }}</p>
                                <div class="activity-time">{{ $order->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>لا توجد أنشطة حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-card">
            <h3>
                <i class="fas fa-bolt me-2"></i>
                إجراءات سريعة
            </h3>
            <div class="quick-actions">
                <a href="{{ route('orders.create') }}" class="action-btn">
                    <i class="fas fa-plus"></i>
                    طلب جديد
                </a>
                <a href="{{ route('invoices.create') }}" class="action-btn">
                    <i class="fas fa-file-invoice"></i>
                    فاتورة جديدة
                </a>
                <a href="{{ route('items.index') }}" class="action-btn">
                    <i class="fas fa-pills"></i>
                    إدارة المنتجات
                </a>
                <a href="{{ route('customers.index') }}" class="action-btn">
                    <i class="fas fa-users"></i>
                    إدارة العملاء
                </a>
                <a href="{{ route('reports.index') }}" class="action-btn">
                    <i class="fas fa-chart-bar"></i>
                    التقارير
                </a>
                <a href="{{ route('items.low-stock') }}" class="action-btn">
                    <i class="fas fa-exclamation-triangle"></i>
                    مخزون منخفض
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// رسم بياني للإيرادات الشهرية
const ctx = document.getElementById('revenueChart').getContext('2d');
const monthlyRevenue = @json($monthly_revenue ?? []);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthlyRevenue.map(item => item.month),
        datasets: [{
            label: 'الإيرادات (دينار)',
            data: monthlyRevenue.map(item => item.revenue),
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointBackgroundColor: '#667eea',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' د.ع';
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                }
            }
        },
        elements: {
            point: {
                hoverBackgroundColor: '#667eea'
            }
        }
    }
});
</script>
@endpush
