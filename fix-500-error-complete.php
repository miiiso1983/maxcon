<?php
echo "<h1>🔧 إصلاح خطأ 500 - الحل الشامل</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .warning{color:orange;}</style>";

try {
    echo "<h2>1. تحليل المشكلة من ملف السجل:</h2>";
    
    $logFile = 'storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        if (strpos($logContent, 'View [auth.login] not found') !== false) {
            echo "<span class='error'>❌ المشكلة: ملفات Views مفقودة</span><br>";
        }
        if (strpos($logContent, 'View [dashboard] not found') !== false) {
            echo "<span class='error'>❌ المشكلة: ملف dashboard.blade.php مفقود</span><br>";
        }
        if (strpos($logContent, 'View [layouts.app] not found') !== false) {
            echo "<span class='error'>❌ المشكلة: ملف layouts/app.blade.php مفقود</span><br>";
        }
    }
    
    echo "<h2>2. إنشاء مجلدات Views:</h2>";
    
    $viewDirs = [
        'resources/views',
        'resources/views/auth',
        'resources/views/layouts',
        'resources/views/components',
        'resources/views/items',
        'resources/views/categories',
        'resources/views/suppliers',
        'resources/views/super-admin',
        'resources/views/super-admin/auth'
    ];
    
    foreach ($viewDirs as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "<span class='success'>✅ تم إنشاء مجلد: $dir</span><br>";
            } else {
                echo "<span class='error'>❌ فشل في إنشاء مجلد: $dir</span><br>";
            }
        } else {
            echo "<span class='info'>📁 موجود: $dir</span><br>";
        }
    }
    
    echo "<h2>3. إنشاء Layout الأساسي:</h2>";
    
    $appLayout = '<!DOCTYPE html>
<html lang="{{ str_replace(\'_\', \'-\', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config(\'app.name\', \'نظام إدارة الصيدلية\') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.2); }
        .sidebar .nav-link i { color: #ffd700; margin-left: 10px; }
        .main-content { background: #f8f9fa; min-height: 100vh; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .navbar-brand { font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @auth
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">{{ config(\'app.name\') }}</h5>
                        <small class="text-white-50">مرحباً {{ Auth::user()->name }}</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs(\'dashboard\') ? \'active\' : \'\' }}" href="{{ route(\'dashboard\') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                لوحة التحكم
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-pills"></i>
                                المنتجات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-tags"></i>
                                الفئات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-truck"></i>
                                الموردين
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users"></i>
                                العملاء
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-invoice"></i>
                                الفواتير
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-shopping-cart"></i>
                                المشتريات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar"></i>
                                التقارير
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog"></i>
                                الإعدادات
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <form method="POST" action="{{ route(\'logout\') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start w-100" style="color: rgba(255,255,255,0.8);">
                                    <i class="fas fa-sign-out-alt"></i>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield(\'title\', \'لوحة التحكم\')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="badge bg-primary">{{ now()->format(\'Y-m-d H:i\') }}</span>
                        </div>
                    </div>
                </div>
                
                @if(session(\'success\'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session(\'success\') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session(\'error\'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session(\'error\') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield(\'content\')
            </main>
            @else
            <!-- Guest Layout -->
            <div class="col-12">
                @yield(\'content\')
            </div>
            @endauth
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield(\'scripts\')
</body>
</html>';
    
    file_put_contents('resources/views/layouts/app.blade.php', $appLayout);
    echo "<span class='success'>✅ تم إنشاء layouts/app.blade.php</span><br>";
    
    echo "<h2>4. إنشاء صفحة تسجيل الدخول:</h2>";
    
    $loginView = '@extends(\'layouts.app\')

@section(\'title\', \'تسجيل الدخول\')

@section(\'content\')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 mt-5">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        تسجيل الدخول
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route(\'login\') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control @error(\'email\') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old(\'email\') }}" required autofocus>
                            </div>
                            @error(\'email\')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error(\'password\') is-invalid @enderror" 
                                       id="password" name="password" required>
                            </div>
                            @error(\'password\')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                تذكرني
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                دخول
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            للحصول على حساب، تواصل مع المدير
                        </small>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">بيانات تجريبية:</h6>
                        <small class="d-block">📧 test@test.com</small>
                        <small class="d-block">🔑 123456</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection';
    
    file_put_contents('resources/views/auth/login.blade.php', $loginView);
    echo "<span class='success'>✅ تم إنشاء auth/login.blade.php</span><br>";
    
    echo "<h2>5. إنشاء صفحة الداشبورد:</h2>";
    
    $dashboardView = '@extends(\'layouts.app\')

@section(\'title\', \'لوحة التحكم\')

@section(\'content\')
<div class="container-fluid">
    <div class="row">
        <!-- إحصائيات سريعة -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                المنتجات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats[\'total_items\'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
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
                                الفئات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats[\'total_categories\'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                الموردين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats[\'total_suppliers\'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                                مخزون منخفض
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats[\'low_stock_items\'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>
                        نظرة عامة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-primary">{{ $stats[\'total_invoices\'] ?? 0 }}</div>
                                <div class="small text-muted">إجمالي الفواتير</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success">{{ $stats[\'total_purchases\'] ?? 0 }}</div>
                                <div class="small text-muted">إجمالي المشتريات</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات النظام
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>المستخدم:</strong> {{ $user->name ?? \'غير محدد\' }}</p>
                    <p><strong>البريد:</strong> {{ $user->email ?? \'غير محدد\' }}</p>
                    <p><strong>الدور:</strong> {{ $user->role ?? \'مستخدم\' }}</p>
                    <p><strong>آخر دخول:</strong> {{ now()->format(\'Y-m-d H:i:s\') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
@endsection';
    
    file_put_contents('resources/views/dashboard.blade.php', $dashboardView);
    echo "<span class='success'>✅ تم إنشاء dashboard.blade.php</span><br>";
    
    echo "<h2>✅ تم إنشاء جميع ملفات Views المطلوبة!</h2>";
    
    echo "<h3>🔗 اختبار النظام:</h3>";
    echo "<a href='/login' target='_blank' style='display:inline-block;padding:10px;background:#007cba;color:white;text-decoration:none;margin:5px;border-radius:5px;'>تسجيل دخول جديد</a><br>";
    
    echo "<h3>🔑 بيانات تسجيل الدخول:</h3>";
    echo "<div style='background:#f8f9fa;padding:15px;border-radius:5px;'>";
    echo "<strong>مستخدم اختبار:</strong> test@test.com / 123456<br>";
    echo "<strong>Super Admin:</strong> superadmin@pharmacy-erp.com / SuperAdmin@2024";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<span class='error'>❌ خطأ عام: " . $e->getMessage() . "</span><br>";
}

echo "<h3>⚠️ الخطوات التالية:</h3>";
echo "<ol>";
echo "<li>جرب تسجيل الدخول مرة أخرى</li>";
echo "<li>يجب أن يعمل النظام الآن بدون أخطاء 500</li>";
echo "<li>احذف ملفات الاختبار بعد التأكد من عمل النظام</li>";
echo "</ol>";
?>
