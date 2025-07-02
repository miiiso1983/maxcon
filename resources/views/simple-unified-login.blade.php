<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول الموحد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="fas fa-pills me-2"></i>نظام إدارة الصيدلية</h3>
                        <p class="mb-0">تسجيل الدخول الموحد</p>
                    </div>
                    <div class="card-body">
                        <!-- User Type Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary w-100 user-type-btn active" data-type="user">
                                    <i class="fas fa-user me-2"></i>مستخدم عادي
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-danger w-100 user-type-btn" data-type="admin">
                                    <i class="fas fa-user-shield me-2"></i>مدير النظام
                                </button>
                            </div>
                        </div>

                        <!-- Login Form -->
                        <form id="loginForm" action="/login/user" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">تذكرني</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                                </button>
                            </div>
                        </form>

                        <!-- Demo Accounts -->
                        <div class="mt-4">
                            <h6>حسابات تجريبية:</h6>
                            
                            <!-- User Accounts -->
                            <div id="userAccounts">
                                <div class="alert alert-info">
                                    <strong>مستخدمين عاديين:</strong><br>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2 demo-btn" 
                                            data-email="ahmed@alnoor-pharmacy.com" data-password="Manager@2024">
                                        صيدلية النور
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary demo-btn" 
                                            data-email="fatima@alshifa-pharmacy.com" data-password="Manager@2024">
                                        صيدلية الشفاء
                                    </button>
                                </div>
                            </div>

                            <!-- Admin Accounts -->
                            <div id="adminAccounts" style="display: none;">
                                <div class="alert alert-danger">
                                    <strong>مديري النظام:</strong><br>
                                    <button type="button" class="btn btn-sm btn-outline-danger me-2 demo-btn" 
                                            data-email="superadmin@pharmacy-erp.com" data-password="SuperAdmin@2024">
                                        السوبر أدمن
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger demo-btn" 
                                            data-email="admin@pharmacy-erp.com" data-password="Admin@2024">
                                        أدمن مساعد
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div id="messages" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // User Type Switching
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.user-type-btn').forEach(b => {
                    b.classList.remove('active');
                    b.classList.add('btn-outline-primary');
                    b.classList.remove('btn-primary', 'btn-danger');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                const userType = this.dataset.type;
                
                if (userType === 'admin') {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-danger');
                    document.getElementById('loginForm').action = '/login/admin';
                    document.getElementById('userAccounts').style.display = 'none';
                    document.getElementById('adminAccounts').style.display = 'block';
                } else {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                    document.getElementById('loginForm').action = '/login/user';
                    document.getElementById('userAccounts').style.display = 'block';
                    document.getElementById('adminAccounts').style.display = 'none';
                }
            });
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
