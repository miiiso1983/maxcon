<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تشخيص تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>تشخيص تسجيل دخول السوبر أدمن</h4>
                    </div>
                    <div class="card-body">
                        <!-- Test Database Connection -->
                        <div class="mb-4">
                            <h5>1. اختبار الاتصال بقاعدة البيانات:</h5>
                            @php
                                try {
                                    $admin = App\Models\SuperAdmin::where('email', 'superadmin@pharmacy-erp.com')->first();
                                    if ($admin) {
                                        echo '<div class="alert alert-success">✅ تم العثور على السوبر أدمن: ' . $admin->name . '</div>';
                                        echo '<div class="alert alert-info">📧 البريد الإلكتروني: ' . $admin->email . '</div>';
                                        echo '<div class="alert alert-info">📊 الحالة: ' . $admin->status . '</div>';
                                        
                                        $passwordCheck = Hash::check('SuperAdmin@2024', $admin->password);
                                        if ($passwordCheck) {
                                            echo '<div class="alert alert-success">🔑 كلمة المرور صحيحة</div>';
                                        } else {
                                            echo '<div class="alert alert-danger">❌ كلمة المرور خاطئة</div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger">❌ لم يتم العثور على السوبر أدمن</div>';
                                    }
                                } catch (Exception $e) {
                                    echo '<div class="alert alert-danger">❌ خطأ في قاعدة البيانات: ' . $e->getMessage() . '</div>';
                                }
                            @endphp
                        </div>

                        <!-- Test Routes -->
                        <div class="mb-4">
                            <h5>2. اختبار المسارات:</h5>
                            @php
                                $routes = [
                                    '/login/admin' => 'مسار تسجيل دخول المديرين',
                                    '/admin/dashboard' => 'مسار لوحة التحكم',
                                ];
                                
                                foreach ($routes as $route => $description) {
                                    if (Route::has(str_replace('/', '', $route))) {
                                        echo '<div class="alert alert-success">✅ ' . $description . ': ' . $route . '</div>';
                                    } else {
                                        echo '<div class="alert alert-warning">⚠️ ' . $description . ': ' . $route . ' (قد لا يكون مُعرف)</div>';
                                    }
                                }
                            @endphp
                        </div>

                        <!-- Manual Login Form -->
                        <div class="mb-4">
                            <h5>3. نموذج تسجيل الدخول المباشر:</h5>
                            <form action="/login/admin" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="superadmin@pharmacy-erp.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           value="SuperAdmin@2024" required>
                                </div>
                                <button type="submit" class="btn btn-primary">تسجيل الدخول (POST)</button>
                            </form>
                        </div>

                        <!-- AJAX Login Test -->
                        <div class="mb-4">
                            <h5>4. اختبار تسجيل الدخول عبر AJAX:</h5>
                            <button type="button" class="btn btn-success" id="ajaxLoginBtn">
                                اختبار AJAX
                            </button>
                            <div id="ajaxResult" class="mt-3"></div>
                        </div>

                        <!-- Session Info -->
                        <div class="mb-4">
                            <h5>5. معلومات الجلسة:</h5>
                            @if(Auth::guard('super_admin')->check())
                                <div class="alert alert-success">
                                    ✅ مسجل دخول كسوبر أدمن: {{ Auth::guard('super_admin')->user()->name }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    ℹ️ غير مسجل دخول كسوبر أدمن
                                </div>
                            @endif
                        </div>

                        <!-- Error Messages -->
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('ajaxLoginBtn').addEventListener('click', function() {
            const resultDiv = document.getElementById('ajaxResult');
            resultDiv.innerHTML = '<div class="alert alert-info">جاري الاختبار...</div>';
            
            const formData = new FormData();
            formData.append('email', 'superadmin@pharmacy-erp.com');
            formData.append('password', 'SuperAdmin@2024');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            fetch('/login/admin', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ نجح تسجيل الدخول: ' + data.message + '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger">❌ فشل تسجيل الدخول: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="alert alert-danger">❌ خطأ في الشبكة: ' + error.message + '</div>';
            });
        });
    </script>
</body>
</html>
