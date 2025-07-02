@extends('super-admin.layouts.app')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')

@push('styles')
<style>
        .super-admin-navbar {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .super-admin-navbar .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .super-admin-navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .super-admin-navbar .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .stats-card .card-body {
            padding: 2rem;
        }
        .stats-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1rem;
        }
        .stats-icon.tenants {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stats-icon.licenses {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
        }
        .stats-icon.users {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .stats-icon.logs {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }
        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .stats-label {
            font-size: 1.1rem;
            color: #6c757d;
            font-weight: 600;
        }
        .quick-actions {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
        }
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .action-btn.danger {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
        }
        .action-btn.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .action-btn.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .welcome-section {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .system-status {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .status-badge.online {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.warning {
            background: #fff3cd;
            color: #856404;
        }
</style>
@endpush

<div class="container-fluid">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 class="mb-3">
                <i class="fas fa-crown me-3"></i>
                مرحباً بك في لوحة التحكم الرئيسية
            </h1>
            <p class="mb-0 fs-5">
                إدارة شاملة لنظام الصيدليات متعدد المستأجرين
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon tenants mx-auto">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stats-number">{{ $stats['tenants'] ?? '0' }}</div>
                        <div class="stats-label">المستأجرين النشطين</div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon licenses mx-auto">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="stats-number">{{ $stats['licenses'] ?? '0' }}</div>
                        <div class="stats-label">التراخيص المفعلة</div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon users mx-auto">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-number">{{ $stats['users'] ?? '0' }}</div>
                        <div class="stats-label">إجمالي المستخدمين</div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon logs mx-auto">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <div class="stats-number">{{ $stats['logs'] ?? '0' }}</div>
                        <div class="stats-label">سجلات اليوم</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quick Actions -->
            <div class="col-lg-8">
                <div class="quick-actions">
                    <h3 class="mb-4">
                        <i class="fas fa-bolt me-2 text-primary"></i>
                        الإجراءات السريعة
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <a href="/super-admin/tenants/create" class="action-btn success w-100 text-center">
                                <i class="fas fa-plus me-2"></i>
                                إضافة مستأجر جديد
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/super-admin/licenses" class="action-btn w-100 text-center">
                                <i class="fas fa-key me-2"></i>
                                إدارة التراخيص
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/super-admin/tenants" class="action-btn warning w-100 text-center">
                                <i class="fas fa-building me-2"></i>
                                عرض جميع المستأجرين
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/super-admin/audit-logs" class="action-btn danger w-100 text-center">
                                <i class="fas fa-list-alt me-2"></i>
                                سجلات التدقيق
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="col-lg-4">
                <div class="system-status">
                    <h3 class="mb-4">
                        <i class="fas fa-server me-2 text-success"></i>
                        حالة النظام
                    </h3>
                    
                    <div class="status-item">
                        <span>
                            <i class="fas fa-database me-2"></i>
                            قاعدة البيانات
                        </span>
                        <span class="status-badge online">متصلة</span>
                    </div>
                    
                    <div class="status-item">
                        <span>
                            <i class="fas fa-cloud me-2"></i>
                            الخدمات السحابية
                        </span>
                        <span class="status-badge online">نشطة</span>
                    </div>
                    
                    <div class="status-item">
                        <span>
                            <i class="fas fa-shield-alt me-2"></i>
                            الأمان
                        </span>
                        <span class="status-badge online">محمي</span>
                    </div>
                    
                    <div class="status-item">
                        <span>
                            <i class="fas fa-backup me-2"></i>
                            النسخ الاحتياطية
                        </span>
                        <span class="status-badge warning">تحديث مطلوب</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
