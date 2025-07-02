@extends('super-admin.layouts.app')

@section('title', 'بيانات دخول المستأجرين')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-key me-2"></i>
                بيانات دخول المستأجرين
            </h1>
            <p class="text-muted">عرض بيانات الدخول لجميع مديري المستأجرين</p>
        </div>
        <div>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للمستأجرين
            </a>
        </div>
    </div>

    <!-- Alert -->
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>تنبيه أمني:</strong> هذه الصفحة تحتوي على معلومات حساسة. تأكد من عدم مشاركة هذه البيانات مع أشخاص غير مخولين.
    </div>

    <!-- Tenants Credentials -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>
                قائمة بيانات الدخول
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>المستأجر</th>
                            <th>اسم المدير</th>
                            <th>البريد الإلكتروني</th>
                            <th>كلمة المرور</th>
                            <th>رابط الدخول</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                            @foreach($tenant->tenantUsers->where('role', 'admin') as $admin)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <div class="avatar-initial bg-primary rounded-circle">
                                                {{ substr($tenant->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $tenant->name }}</h6>
                                            <small class="text-muted">ID: {{ $tenant->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $admin->name }}</strong>
                                    @if($admin->phone)
                                        <br><small class="text-muted">{{ $admin->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" value="{{ $admin->email }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $admin->email }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="form-control" value="Manager@2024" readonly id="password-{{ $admin->id }}">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password-{{ $admin->id }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('Manager@2024')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">كلمة المرور الافتراضية</small>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" value="{{ url('/login') }}" readonly>
                                        <a href="{{ url('/login') }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if($admin->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                    
                                    @if($tenant->status == 'active')
                                        <br><span class="badge bg-info">مستأجر نشط</span>
                                    @else
                                        <br><span class="badge bg-warning">مستأجر معلق</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="resetPassword({{ $admin->id }})">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" onclick="sendCredentials({{ $admin->id }})">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <a href="{{ route('super-admin.tenants.show', $tenant->id) }}" class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>لا يوجد مستأجرين مسجلين</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-info-circle me-2"></i>
                        تعليمات الدخول للمستأجرين
                    </h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li>اذهب إلى رابط الدخول: <code>{{ url('/login') }}</code></li>
                        <li>أدخل البريد الإلكتروني المخصص لك</li>
                        <li>أدخل كلمة المرور: <code>Manager@2024</code></li>
                        <li>انقر على "تسجيل الدخول"</li>
                        <li>ستتم إعادة توجيهك للوحة التحكم الخاصة بك</li>
                    </ol>
                    
                    <div class="alert alert-info mt-3">
                        <strong>ملاحظة:</strong> يُنصح بتغيير كلمة المرور بعد أول دخول لأسباب أمنية.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-shield-alt me-2"></i>
                        إرشادات الأمان
                    </h6>
                </div>
                <div class="card-body">
                    <ul>
                        <li><strong>لا تشارك</strong> بيانات الدخول مع أشخاص غير مخولين</li>
                        <li><strong>غيّر كلمة المرور</strong> بعد أول دخول</li>
                        <li><strong>استخدم كلمة مرور قوية</strong> تحتوي على أحرف وأرقام ورموز</li>
                        <li><strong>لا تحفظ</strong> كلمة المرور في المتصفح على أجهزة مشتركة</li>
                        <li><strong>سجّل الخروج</strong> دائماً بعد انتهاء العمل</li>
                    </ul>
                    
                    <div class="alert alert-danger mt-3">
                        <strong>تحذير:</strong> أي استخدام غير مصرح به للنظام قد يؤدي إلى تعليق الحساب.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Credentials Summary -->
    <div class="card shadow mt-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-clipboard-list me-2"></i>
                ملخص بيانات الدخول الافتراضية
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">رابط الدخول</h5>
                            <p class="card-text">
                                <code>{{ url('/login') }}</code>
                            </p>
                            <button class="btn btn-sm btn-primary" onclick="copyToClipboard('{{ url('/login') }}')">
                                نسخ الرابط
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">البريد الإلكتروني</h5>
                            <p class="card-text">
                                البريد المحدد لكل مستأجر
                            </p>
                            <small class="text-muted">مختلف لكل مستأجر</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">كلمة المرور</h5>
                            <p class="card-text">
                                <code>Manager@2024</code>
                            </p>
                            <button class="btn btn-sm btn-primary" onclick="copyToClipboard('Manager@2024')">
                                نسخ كلمة المرور
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    تم نسخ النص بنجاح!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Remove toast after 3 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 3000);
    }).catch(function(err) {
        alert('فشل في نسخ النص: ' + err);
    });
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Reset password
function resetPassword(userId) {
    if (confirm('هل أنت متأكد من إعادة تعيين كلمة المرور للمستخدم؟')) {
        fetch(`/super-admin/tenant-users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إعادة تعيين كلمة المرور بنجاح');
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ غير متوقع');
        });
    }
}

// Send credentials via email
function sendCredentials(userId) {
    if (confirm('هل تريد إرسال بيانات الدخول عبر البريد الإلكتروني؟')) {
        fetch(`/super-admin/tenant-users/${userId}/send-credentials`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إرسال بيانات الدخول بنجاح');
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ غير متوقع');
        });
    }
}
</script>
@endsection
