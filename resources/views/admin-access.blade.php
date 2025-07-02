<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>الوصول لنظام الإدارة - نظام إدارة الصيدلية</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .admin-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 2rem;
        }
        .admin-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
        .home-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .home-btn:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-card">
            <!-- أيقونة الإدارة -->
            <div class="admin-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            
            <!-- العنوان -->
            <h1 class="h2 mb-4 text-primary">نظام إدارة الصيدلية</h1>
            <p class="text-muted mb-4">الوصول لنظام الإدارة والتحكم</p>
            
            <!-- أزرار الوصول -->
            <div class="d-grid gap-3 mb-4">
                <a href="{{ route('admin.login') }}" class="admin-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    تسجيل دخول المديرين
                </a>
                
                <a href="{{ route('admin.dashboard') }}" class="admin-btn">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    لوحة التحكم الرئيسية
                </a>
            </div>
            
            <!-- معلومات تسجيل الدخول -->
            <div class="alert alert-info text-start">
                <h6><i class="fas fa-info-circle me-2"></i>بيانات تسجيل الدخول:</h6>
                <small>
                    <strong>البريد الإلكتروني:</strong> superadmin@pharmacy-erp.com<br>
                    <strong>كلمة المرور:</strong> SuperAdmin@2024
                </small>
            </div>
            
            <!-- أزرار إضافية -->
            <div class="mt-4">
                <h6 class="text-muted mb-3">روابط سريعة:</h6>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-building me-1"></i>
                        المستأجرين
                    </a>
                    <a href="{{ route('admin.licenses.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-key me-1"></i>
                        التراخيص
                    </a>
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        إضافة مستأجر
                    </a>
                </div>
            </div>
            
            <!-- رابط العودة للصفحة الرئيسية -->
            <div class="mt-4 pt-3 border-top">
                <a href="{{ url('/') }}" class="home-btn">
                    <i class="fas fa-home me-1"></i>
                    العودة للصفحة الرئيسية
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
