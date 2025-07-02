<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل دخول السوبر أدمن - نظام إدارة الصيدلية</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .super-admin-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            border: 3px solid #dc3545;
        }
        .super-admin-header {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
        }
        .super-admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }
        .super-admin-header .content {
            position: relative;
            z-index: 1;
        }
        .super-admin-body {
            padding: 2.5rem 2rem;
        }
        .form-control {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .btn-super-admin {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-super-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(220, 53, 69, 0.4);
            color: white;
        }
        .super-admin-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .demo-account {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dc3545;
            border-radius: 12px;
            padding: 1rem;
            margin: 0.5rem 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .demo-account:hover {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
        }
        .security-badge {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #dc3545;
            font-weight: 600;
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Back Link -->
    <a href="{{ url('/login') }}" class="back-link">
        <i class="fas fa-arrow-left me-2"></i>
        العودة للنظام العادي
    </a>

    <div class="super-admin-card">
        <!-- Header -->
        <div class="super-admin-header">
            <div class="content">
                <div class="super-admin-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2 class="mb-2">السوبر أدمن</h2>
                <p class="mb-0">لوحة التحكم الرئيسية</p>
                <small class="opacity-75">نظام إدارة الصيدليات المتقدم</small>
            </div>
        </div>

        <!-- Body -->
        <div class="super-admin-body">
            <!-- Security Badge -->
            <div class="security-badge">
                <i class="fas fa-shield-alt me-2"></i>
                منطقة محمية - مخصصة للسوبر أدمن فقط
            </div>

            <!-- Login Form -->
            <form id="superAdminForm" action="/super-admin/login" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label fw-bold">
                        <i class="fas fa-envelope me-2 text-danger"></i>
                        البريد الإلكتروني
                    </label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           placeholder="أدخل البريد الإلكتروني للسوبر أدمن">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">
                        <i class="fas fa-lock me-2 text-danger"></i>
                        كلمة المرور
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="أدخل كلمة المرور">
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label fw-bold" for="remember">
                        <i class="fas fa-remember me-1"></i>
                        تذكر بيانات الدخول
                    </label>
                </div>

                <button type="submit" class="btn btn-super-admin">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    دخول لوحة التحكم
                </button>
            </form>

            <!-- Demo Accounts -->
            <div class="mt-4">
                <h6 class="fw-bold text-center mb-3">
                    <i class="fas fa-users-cog me-2 text-danger"></i>
                    حسابات السوبر أدمن التجريبية
                </h6>
                
                <div class="demo-account" data-email="superadmin@pharmacy-erp.com" data-password="SuperAdmin@2024">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>
                                <i class="fas fa-crown me-2"></i>
                                السوبر أدمن الرئيسي
                            </strong>
                            <br>
                            <small class="opacity-75">superadmin@pharmacy-erp.com</small>
                        </div>
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                </div>

                <div class="demo-account" data-email="admin@pharmacy-erp.com" data-password="Admin@2024">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>
                                <i class="fas fa-user-tie me-2"></i>
                                أدمن مساعد
                            </strong>
                            <br>
                            <small class="opacity-75">admin@pharmacy-erp.com</small>
                        </div>
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                </div>

                <div class="demo-account" data-email="support@pharmacy-erp.com" data-password="Support@2024">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>
                                <i class="fas fa-headset me-2"></i>
                                دعم فني
                            </strong>
                            <br>
                            <small class="opacity-75">support@pharmacy-erp.com</small>
                        </div>
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div id="messages" class="mt-4"></div>

            <!-- Footer Info -->
            <div class="text-center mt-4 pt-3 border-top">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    هذه المنطقة مخصصة لإدارة النظام والمستأجرين
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
        document.querySelectorAll('.demo-account').forEach(account => {
            account.addEventListener('click', function() {
                document.getElementById('email').value = this.dataset.email;
                document.getElementById('password').value = this.dataset.password;
                
                // Visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-2px)';
                }, 150);
            });
        });

        // Form Submission
        document.getElementById('superAdminForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التحقق...';
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
                        '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + 
                        (data.message || 'تم تسجيل الدخول بنجاح') + '</div>';
                    
                    setTimeout(() => {
                        window.location.href = data.redirect || '/super-admin/dashboard';
                    }, 1000);
                } else {
                    document.getElementById('messages').innerHTML = 
                        '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>' + 
                        (data.message || 'خطأ في تسجيل الدخول') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('messages').innerHTML = 
                    '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>حدث خطأ غير متوقع</div>';
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
