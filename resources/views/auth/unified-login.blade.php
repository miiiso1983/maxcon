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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            min-height: 600px;
        }
        .login-sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        .login-form {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .user-type-tabs {
            display: flex;
            margin-bottom: 2rem;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        .user-type-tab {
            flex: 1;
            padding: 1rem;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .user-type-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .demo-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            margin: 0.25rem 0;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .demo-account:hover {
            background: #e9ecef;
        }
        .sidebar-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="row g-0 h-100">
                <!-- Sidebar -->
                <div class="col-lg-5">
                    <div class="login-sidebar h-100">
                        <div>
                            <div class="sidebar-icon">
                                <i class="fas fa-pills"></i>
                            </div>
                            <h2 class="mb-3">نظام إدارة الصيدلية</h2>
                            <p class="mb-4">نظام شامل لإدارة الصيدليات مع دعم متعدد المستأجرين</p>
                            
                            <div class="features">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>إدارة المخزون والمبيعات</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>تقارير مالية متقدمة</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>أمان عالي وحماية البيانات</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>دعم فني متواصل</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Form -->
                <div class="col-lg-7">
                    <div class="login-form h-100">
                        <div>
                            <h3 class="text-center mb-4">تسجيل الدخول</h3>
                            
                            <!-- User Type Tabs -->
                            <div class="user-type-tabs">
                                <button type="button" class="user-type-tab active" data-type="user">
                                    <i class="fas fa-user me-2"></i>
                                    مستخدم عادي
                                </button>
                                <button type="button" class="user-type-tab" data-type="admin">
                                    <i class="fas fa-user-shield me-2"></i>
                                    مدير النظام
                                </button>
                            </div>

                            <!-- Login Form -->
                            <form id="loginForm" method="POST">
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
                                    <label class="form-check-label" for="remember">
                                        تذكرني
                                    </label>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-login">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        تسجيل الدخول
                                    </button>
                                </div>
                            </form>

                            <!-- Demo Accounts -->
                            <div class="demo-accounts">
                                <h6 class="mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    حسابات تجريبية
                                </h6>
                                
                                <!-- User Accounts -->
                                <div id="userAccounts">
                                    <small class="text-muted d-block mb-2">حسابات المستخدمين:</small>
                                    <div class="demo-account" data-email="ahmed@alnoor-pharmacy.com" data-password="Manager@2024">
                                        <div>
                                            <strong>صيدلية النور</strong>
                                            <br><small class="text-muted">ahmed@alnoor-pharmacy.com</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary use-demo">استخدام</button>
                                    </div>
                                    <div class="demo-account" data-email="fatima@alshifa-pharmacy.com" data-password="Manager@2024">
                                        <div>
                                            <strong>صيدلية الشفاء</strong>
                                            <br><small class="text-muted">fatima@alshifa-pharmacy.com</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary use-demo">استخدام</button>
                                    </div>
                                </div>

                                <!-- Admin Accounts -->
                                <div id="adminAccounts" style="display: none;">
                                    <small class="text-muted d-block mb-2">حسابات المديرين:</small>
                                    <div class="demo-account" data-email="superadmin@pharmacy-erp.com" data-password="SuperAdmin@2024">
                                        <div>
                                            <strong>السوبر أدمن</strong>
                                            <br><small class="text-muted">superadmin@pharmacy-erp.com</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary use-demo">استخدام</button>
                                    </div>
                                    <div class="demo-account" data-email="admin@pharmacy-erp.com" data-password="Admin@2024">
                                        <div>
                                            <strong>أدمن مساعد</strong>
                                            <br><small class="text-muted">admin@pharmacy-erp.com</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary use-demo">استخدام</button>
                                    </div>
                                    <div class="demo-account" data-email="support@pharmacy-erp.com" data-password="Support@2024">
                                        <div>
                                            <strong>دعم فني</strong>
                                            <br><small class="text-muted">support@pharmacy-erp.com</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary use-demo">استخدام</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Messages -->
                            <div id="errorMessages" class="alert alert-danger mt-3" style="display: none;"></div>
                            
                            <!-- Success Messages -->
                            <div id="successMessages" class="alert alert-success mt-3" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // User Type Switching
        document.querySelectorAll('.user-type-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.user-type-tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                const userType = this.dataset.type;
                
                // Update form action
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
        document.querySelectorAll('.use-demo').forEach(button => {
            button.addEventListener('click', function() {
                const account = this.closest('.demo-account');
                const email = account.dataset.email;
                const password = account.dataset.password;
                
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
            });
        });

        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const activeTab = document.querySelector('.user-type-tab.active');
            const userType = activeTab.dataset.type;

            console.log('Form action:', this.action);
            console.log('User type:', userType);
            console.log('Form data:', Object.fromEntries(formData));
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري تسجيل الدخول...';
            submitBtn.disabled = true;
            
            // Hide previous messages
            document.getElementById('errorMessages').style.display = 'none';
            document.getElementById('successMessages').style.display = 'none';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('successMessages').innerHTML = data.message || 'تم تسجيل الدخول بنجاح';
                    document.getElementById('successMessages').style.display = 'block';

                    // Redirect after success
                    setTimeout(() => {
                        window.location.href = data.redirect || (userType === 'admin' ? '/admin/dashboard' : '/dashboard');
                    }, 1000);
                } else {
                    document.getElementById('errorMessages').innerHTML = data.message || 'خطأ في تسجيل الدخول';
                    document.getElementById('errorMessages').style.display = 'block';
                }
            })
            .catch(error => {
                document.getElementById('errorMessages').innerHTML = 'حدث خطأ غير متوقع';
                document.getElementById('errorMessages').style.display = 'block';
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Initialize form action
        document.getElementById('loginForm').action = '/login/user';
    </script>
</body>
</html>
