<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - نظام إدارة الصيدلية</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .user-type-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 2rem;
        }
        .user-type-btn {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .user-type-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
        .user-type-btn.admin.active {
            border-color: #dc3545;
            background: #dc3545;
            color: white;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
        .demo-accounts {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .demo-btn {
            margin: 0.25rem;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="mb-3">
                <i class="fas fa-pills fa-3x"></i>
            </div>
            <h2>نظام إدارة الصيدلية</h2>
            <p class="mb-0">تسجيل الدخول الموحد</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <!-- User Type Selection -->
            <div class="user-type-buttons">
                <button type="button" class="user-type-btn active" data-type="user">
                    <i class="fas fa-user d-block mb-2"></i>
                    <small>مستخدم عادي</small>
                </button>
                <button type="button" class="user-type-btn admin" data-type="admin">
                    <i class="fas fa-user-shield d-block mb-2"></i>
                    <small>مدير النظام</small>
                </button>
            </div>

            <!-- Login Form -->
            <form id="loginForm" action="/login/user" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="أدخل البريد الإلكتروني">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="أدخل كلمة المرور">
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">تذكرني</label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    تسجيل الدخول
                </button>
            </form>

            <!-- Demo Accounts -->
            <div class="demo-accounts">
                <h6 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    حسابات تجريبية
                </h6>
                
                <!-- User Accounts -->
                <div id="userAccounts">
                    <small class="text-muted d-block mb-2">مستخدمين عاديين:</small>
                    <button type="button" class="btn btn-sm btn-outline-primary demo-btn" 
                            data-email="ahmed@alnoor-pharmacy.com" data-password="Manager@2024">
                        صيدلية النور
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary demo-btn" 
                            data-email="fatima@alshifa-pharmacy.com" data-password="Manager@2024">
                        صيدلية الشفاء
                    </button>
                </div>

                <!-- Admin Accounts -->
                <div id="adminAccounts" style="display: none;">
                    <small class="text-muted d-block mb-2">مديري النظام:</small>
                    <button type="button" class="btn btn-sm btn-outline-danger demo-btn" 
                            data-email="superadmin@pharmacy-erp.com" data-password="SuperAdmin@2024">
                        السوبر أدمن
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger demo-btn" 
                            data-email="admin@pharmacy-erp.com" data-password="Admin@2024">
                        أدمن مساعد
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div id="messages" class="mt-3"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // User Type Switching
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.user-type-btn').forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                const userType = this.dataset.type;
                const form = document.getElementById('loginForm');
                
                if (userType === 'admin') {
                    form.action = '/login/admin';
                    document.getElementById('userAccounts').style.display = 'none';
                    document.getElementById('adminAccounts').style.display = 'block';
                } else {
                    form.action = '/login/user';
                    document.getElementById('userAccounts').style.display = 'block';
                    document.getElementById('adminAccounts').style.display = 'none';
                }
            });
        });

        // Password Toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Demo Account Usage
        document.querySelectorAll('.demo-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('email').value = this.dataset.email;
                document.getElementById('password').value = this.dataset.password;
            });
        });

        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري تسجيل الدخول...';
            submitBtn.disabled = true;
            
            // Clear previous messages
            document.getElementById('messages').innerHTML = '';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('messages').innerHTML = 
                        '<div class="alert alert-success">' + (data.message || 'تم تسجيل الدخول بنجاح') + '</div>';
                    
                    setTimeout(() => {
                        window.location.href = data.redirect || '/admin/dashboard';
                    }, 1000);
                } else {
                    document.getElementById('messages').innerHTML = 
                        '<div class="alert alert-danger">' + (data.message || 'خطأ في تسجيل الدخول') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('messages').innerHTML = 
                    '<div class="alert alert-danger">حدث خطأ غير متوقع</div>';
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
