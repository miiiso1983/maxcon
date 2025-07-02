@extends('super-admin.layouts.app')

@section('title', 'إضافة مدير جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus me-2"></i>
                إضافة مدير جديد
            </h1>
            <p class="text-muted">إنشاء حساب سوبر أدمن جديد مع صلاحيات محددة</p>
        </div>
        <div>
            <a href="{{ route('super-admin.admins.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Admin Form -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-cog me-2"></i>
                        بيانات المدير
                    </h6>
                </div>
                <div class="card-body">
                    <form id="adminForm">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    الاسم الكامل <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control" required 
                                       placeholder="أحمد محمد">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    البريد الإلكتروني <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" class="form-control" required 
                                       placeholder="admin@example.com">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="tel" name="phone" id="phone" class="form-control" 
                                       placeholder="+966501234567">
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">المنصب</label>
                                <input type="text" name="role" id="role" class="form-control" 
                                       placeholder="مدير النظام">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    كلمة المرور <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" required 
                                           placeholder="كلمة مرور قوية">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    يجب أن تحتوي على 8 أحرف على الأقل، أحرف كبيرة وصغيرة، أرقام ورموز
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    تأكيد كلمة المرور <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control" required placeholder="أعد كتابة كلمة المرور">
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-shield-alt me-2"></i>
                                الصلاحيات
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="can_create_tenants" id="can_create_tenants">
                                        <label class="form-check-label" for="can_create_tenants">
                                            <i class="fas fa-plus-circle text-success me-2"></i>
                                            إنشاء مستأجرين جدد
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="can_delete_tenants" id="can_delete_tenants">
                                        <label class="form-check-label" for="can_delete_tenants">
                                            <i class="fas fa-trash text-danger me-2"></i>
                                            حذف المستأجرين
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="can_manage_licenses" id="can_manage_licenses">
                                        <label class="form-check-label" for="can_manage_licenses">
                                            <i class="fas fa-key text-info me-2"></i>
                                            إدارة التراخيص
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="can_view_all_data" id="can_view_all_data">
                                        <label class="form-check-label" for="can_view_all_data">
                                            <i class="fas fa-eye text-warning me-2"></i>
                                            عرض جميع البيانات
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-cog me-2"></i>
                                إعدادات الحساب
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" value="active" checked>
                                <label class="form-check-label" for="status">
                                    تفعيل الحساب فور الإنشاء
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
                                <label class="form-check-label" for="send_welcome_email">
                                    إرسال بريد ترحيبي مع بيانات الدخول
                                </label>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>
                                ملاحظات
                            </label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" 
                                      placeholder="أي ملاحظات إضافية حول هذا المدير"></textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('super-admin.admins.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                إنشاء المدير
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview & Guidelines -->
        <div class="col-lg-4">
            <!-- Admin Preview -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-eye me-2"></i>
                        معاينة المدير
                    </h6>
                </div>
                <div class="card-body">
                    <div id="adminPreview" class="text-center">
                        <i class="fas fa-user-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">املأ البيانات لرؤية المعاينة</p>
                    </div>
                </div>
            </div>

            <!-- Security Guidelines -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-shield-alt me-2"></i>
                        إرشادات الأمان
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">كلمة المرور القوية:</h6>
                        <ul class="small text-muted">
                            <li>8 أحرف على الأقل</li>
                            <li>أحرف كبيرة وصغيرة</li>
                            <li>أرقام ورموز</li>
                            <li>تجنب المعلومات الشخصية</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-success">الصلاحيات:</h6>
                        <ul class="small text-muted">
                            <li>امنح الصلاحيات حسب الحاجة فقط</li>
                            <li>راجع الصلاحيات دورياً</li>
                            <li>تجنب منح جميع الصلاحيات</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            تذكر: المديرين لديهم صلاحيات واسعة في النظام
                        </small>
                    </div>
                </div>
            </div>

            <!-- Permission Levels -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>
                        مستويات الصلاحيات
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="badge bg-success">إنشاء مستأجرين</span>
                        <small class="d-block text-muted">إضافة مستأجرين جدد للنظام</small>
                    </div>
                    
                    <div class="mb-2">
                        <span class="badge bg-danger">حذف مستأجرين</span>
                        <small class="d-block text-muted">حذف المستأجرين وبياناتهم</small>
                    </div>
                    
                    <div class="mb-2">
                        <span class="badge bg-info">إدارة التراخيص</span>
                        <small class="d-block text-muted">تجديد وتعليق التراخيص</small>
                    </div>
                    
                    <div class="mb-2">
                        <span class="badge bg-warning">عرض جميع البيانات</span>
                        <small class="d-block text-muted">الوصول لجميع بيانات النظام</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password Toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        button.classList.remove('fa-eye');
        button.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        button.classList.remove('fa-eye-slash');
        button.classList.add('fa-eye');
    }
}

// Live Preview
function updatePreview() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const role = document.getElementById('role').value;
    const previewDiv = document.getElementById('adminPreview');
    
    if (name || email) {
        const permissions = Array.from(document.querySelectorAll('input[name="permissions[]"]:checked'))
            .map(cb => cb.nextElementSibling.textContent.trim());
        
        previewDiv.innerHTML = `
            <div class="text-center">
                <div class="avatar-initial bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                    <span class="h4 text-white mb-0">${name ? name.charAt(0).toUpperCase() : 'A'}</span>
                </div>
                <h6>${name || 'اسم المدير'}</h6>
                <small class="text-muted">${email || 'البريد الإلكتروني'}</small>
                <div class="mt-2">
                    <small class="badge bg-secondary">${role || 'مدير النظام'}</small>
                </div>
                ${permissions.length > 0 ? `
                    <div class="mt-2">
                        <small class="text-muted d-block">الصلاحيات:</small>
                        <div class="mt-1">
                            ${permissions.map(p => `<span class="badge bg-info me-1">${p.replace(/.*\s/, '')}</span>`).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }
}

// Add event listeners
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('email').addEventListener('input', updatePreview);
document.getElementById('role').addEventListener('input', updatePreview);
document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
    cb.addEventListener('change', updatePreview);
});

// Form submission
document.getElementById('adminForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirmation').value;
    
    if (password !== passwordConfirm) {
        alert('كلمات المرور غير متطابقة');
        return;
    }
    
    if (password.length < 8) {
        alert('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
        return;
    }
    
    // محاكاة إنشاء المدير
    alert('تم إنشاء المدير بنجاح');
    window.location.href = '{{ route("super-admin.admins.index") }}';
});
</script>
@endsection
