<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم الرئيسية') - السوبر أدمن</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
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
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }
        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }
        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }
        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }
        .avatar {
            width: 40px;
            height: 40px;
        }
        .avatar-initial {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }
        .table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            border: none;
        }
        .table td {
            border: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
        }
        .btn-group .btn {
            border-radius: 8px;
            margin: 0 2px;
        }
        .sidebar {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: calc(100vh - 76px);
            padding: 0;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
        }
        .main-content {
            padding: 2rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg super-admin-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('super-admin.dashboard') }}">
                <i class="fas fa-user-shield me-2"></i>
                لوحة التحكم الرئيسية
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('super-admin.tenants.index') }}">
                            <i class="fas fa-building me-1"></i>
                            المستأجرين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('super-admin.licenses.index') }}">
                            <i class="fas fa-key me-1"></i>
                            التراخيص
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('super-admin.audit-logs.index') }}">
                            <i class="fas fa-list-alt me-1"></i>
                            سجلات التدقيق
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('super-admin.admins.index') }}">
                            <i class="fas fa-users-cog me-1"></i>
                            المديرين
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ Auth::guard('super_admin')->user()->name ?? 'السوبر أدمن' }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('super-admin.profile.index') }}">
                                <i class="fas fa-user me-2"></i>الملف الشخصي
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('super-admin.settings.index') }}">
                                <i class="fas fa-cog me-2"></i>الإعدادات
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('super-admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            لوحة التحكم
                        </a>
                        <a class="nav-link {{ request()->routeIs('super-admin.tenants.*') ? 'active' : '' }}" 
                           href="{{ route('super-admin.tenants.index') }}">
                            <i class="fas fa-building me-2"></i>
                            المستأجرين
                        </a>
                        <a class="nav-link {{ request()->routeIs('super-admin.licenses.*') ? 'active' : '' }}" 
                           href="{{ route('super-admin.licenses.index') }}">
                            <i class="fas fa-key me-2"></i>
                            التراخيص
                        </a>
                        <a class="nav-link {{ request()->routeIs('super-admin.audit-logs.*') ? 'active' : '' }}" 
                           href="{{ route('super-admin.audit-logs.index') }}">
                            <i class="fas fa-list-alt me-2"></i>
                            سجلات التدقيق
                        </a>
                        <a class="nav-link {{ request()->routeIs('super-admin.admins.*') ? 'active' : '' }}" 
                           href="{{ route('super-admin.admins.index') }}">
                            <i class="fas fa-users-cog me-2"></i>
                            المديرين
                        </a>
                        <a class="nav-link {{ request()->routeIs('super-admin.reports.*') ? 'active' : '' }}" 
                           href="{{ route('super-admin.reports.index') }}">
                            <i class="fas fa-chart-bar me-2"></i>
                            التقارير
                        </a>
                        <a class="nav-link {{ request()->routeIs('super-admin.settings.*') ? 'active' : '' }}" 
                           href="{{ route('super-admin.settings.index') }}">
                            <i class="fas fa-cog me-2"></i>
                            الإعدادات
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-10">
                <div class="main-content">
                    <!-- Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('super-admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
