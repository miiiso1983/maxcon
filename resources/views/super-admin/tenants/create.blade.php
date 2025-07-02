@extends('super-admin.layouts.app')

@section('title', 'إضافة مستأجر جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                إضافة مستأجر جديد
            </h1>
            <p class="text-muted">إنشاء مستأجر جديد مع ترخيص ومدير</p>
        </div>
        <div>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Create Tenant Form -->
    <form action="{{ route('super-admin.tenants.store') }}" method="POST" id="tenantForm">
        @csrf
        
        <div class="row">
            <!-- Tenant Information -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-building me-2"></i>
                            بيانات المستأجر
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Tenant Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                اسم المستأجر <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required 
                                   placeholder="مثال: صيدلية النور">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                البريد الإلكتروني <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required 
                                   placeholder="info@pharmacy.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                رقم الهاتف <span class="text-danger">*</span>
                            </label>
                            <input type="tel" name="phone" id="phone" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" required 
                                   placeholder="+966501234567">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contact Person -->
                        <div class="mb-3">
                            <label for="contact_person" class="form-label">
                                شخص الاتصال <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="contact_person" id="contact_person"
                                   class="form-control @error('contact_person') is-invalid @enderror"
                                   value="{{ old('contact_person') }}" required
                                   placeholder="اسم الشخص المسؤول">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">
                                العنوان <span class="text-danger">*</span>
                            </label>
                            <textarea name="address" id="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror"
                                      required placeholder="العنوان الكامل للصيدلية">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- License Information -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-key me-2"></i>
                            بيانات الترخيص
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- License Type -->
                        <div class="mb-3">
                            <label for="license_type" class="form-label">
                                نوع الترخيص <span class="text-danger">*</span>
                            </label>
                            <select name="license_type" id="license_type" 
                                    class="form-select @error('license_type') is-invalid @enderror" required>
                                <option value="">اختر نوع الترخيص</option>
                                <option value="basic" {{ old('license_type') == 'basic' ? 'selected' : '' }}>
                                    أساسي (5 مستخدمين، 2 جيجابايت)
                                </option>
                                <option value="premium" {{ old('license_type') == 'premium' ? 'selected' : '' }}>
                                    مميز (20 مستخدم، 10 جيجابايت)
                                </option>
                                <option value="enterprise" {{ old('license_type') == 'enterprise' ? 'selected' : '' }}>
                                    مؤسسي (غير محدود، 100 جيجابايت)
                                </option>
                            </select>
                            @error('license_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- License Duration -->
                        <div class="mb-3">
                            <label for="license_duration" class="form-label">
                                مدة الترخيص (بالأشهر) <span class="text-danger">*</span>
                            </label>
                            <select name="license_duration" id="license_duration" 
                                    class="form-select @error('license_duration') is-invalid @enderror" required>
                                <option value="">اختر المدة</option>
                                <option value="1" {{ old('license_duration') == '1' ? 'selected' : '' }}>شهر واحد</option>
                                <option value="3" {{ old('license_duration') == '3' ? 'selected' : '' }}>3 أشهر</option>
                                <option value="6" {{ old('license_duration') == '6' ? 'selected' : '' }}>6 أشهر</option>
                                <option value="12" {{ old('license_duration') == '12' ? 'selected' : '' }}>سنة واحدة</option>
                                <option value="24" {{ old('license_duration') == '24' ? 'selected' : '' }}>سنتان</option>
                                <option value="36" {{ old('license_duration') == '36' ? 'selected' : '' }}>3 سنوات</option>
                            </select>
                            @error('license_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manager Information -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-user-tie me-2"></i>
                            بيانات مدير المستأجر
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Manager Name -->
                        <div class="mb-3">
                            <label for="manager_name" class="form-label">
                                اسم المدير <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="manager_name" id="manager_name" 
                                   class="form-control @error('manager_name') is-invalid @enderror" 
                                   value="{{ old('manager_name') }}" required 
                                   placeholder="أحمد محمد">
                            @error('manager_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Manager Email -->
                        <div class="mb-3">
                            <label for="manager_email" class="form-label">
                                البريد الإلكتروني للمدير <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="manager_email" id="manager_email" 
                                   class="form-control @error('manager_email') is-invalid @enderror" 
                                   value="{{ old('manager_email') }}" required 
                                   placeholder="manager@pharmacy.com">
                            @error('manager_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Manager Phone -->
                        <div class="mb-3">
                            <label for="manager_phone" class="form-label">
                                رقم هاتف المدير
                            </label>
                            <input type="tel" name="manager_phone" id="manager_phone" 
                                   class="form-control @error('manager_phone') is-invalid @enderror" 
                                   value="{{ old('manager_phone') }}" 
                                   placeholder="+966501234567">
                            @error('manager_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Manager Password -->
                        <div class="mb-3">
                            <label for="manager_password" class="form-label">
                                كلمة المرور <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="manager_password" id="manager_password" 
                                       class="form-control @error('manager_password') is-invalid @enderror" 
                                       required placeholder="كلمة مرور قوية">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">
                                يجب أن تحتوي على 8 أحرف على الأقل، أحرف كبيرة وصغيرة، أرقام ورموز
                            </small>
                            @error('manager_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="manager_password_confirmation" class="form-label">
                                تأكيد كلمة المرور <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="manager_password_confirmation" id="manager_password_confirmation" 
                                   class="form-control" required 
                                   placeholder="أعد كتابة كلمة المرور">
                        </div>
                    </div>
                </div>

                <!-- License Preview -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-eye me-2"></i>
                            معاينة الترخيص
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="licensePreview">
                            <div class="text-center text-muted">
                                <i class="fas fa-key fa-3x mb-3"></i>
                                <p>اختر نوع الترخيص لرؤية المعاينة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                إنشاء المستأجر
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Password Toggle
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('manager_password');
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

// License Preview
function updateLicensePreview() {
    const licenseType = document.getElementById('license_type').value;
    const duration = document.getElementById('license_duration').value;
    const previewDiv = document.getElementById('licensePreview');
    
    if (licenseType) {
        const features = {
            'basic': {
                name: 'أساسي',
                color: 'primary',
                users: '5 مستخدمين',
                storage: '2 جيجابايت',
                branches: 'فرع واحد',
                features: ['إدارة المخزون', 'إدارة المبيعات', 'تقارير أساسية']
            },
            'premium': {
                name: 'مميز',
                color: 'success',
                users: '20 مستخدم',
                storage: '10 جيجابايت',
                branches: '5 فروع',
                features: ['إدارة المخزون', 'إدارة المبيعات', 'التقارير المالية', 'إدارة العملاء', 'إدارة الموظفين']
            },
            'enterprise': {
                name: 'مؤسسي',
                color: 'warning',
                users: 'غير محدود',
                storage: '100 جيجابايت',
                branches: 'غير محدود',
                features: ['جميع المميزات', 'الوصول للـ API', 'تقارير متقدمة', 'فروع متعددة']
            }
        };
        
        const license = features[licenseType];
        const endDate = new Date();
        endDate.setMonth(endDate.getMonth() + parseInt(duration || 12));
        
        previewDiv.innerHTML = `
            <div class="text-center">
                <div class="badge bg-${license.color} mb-3">${license.name}</div>
                <h6>معاينة الترخيص</h6>
                <hr>
                <div class="text-start">
                    <small>
                        <strong>المستخدمين:</strong> ${license.users}<br>
                        <strong>التخزين:</strong> ${license.storage}<br>
                        <strong>الفروع:</strong> ${license.branches}<br>
                        <strong>المدة:</strong> ${duration || 12} شهر<br>
                        <strong>ينتهي في:</strong> ${endDate.toLocaleDateString('ar-SA')}<br><br>
                        <strong>المميزات:</strong><br>
                        ${license.features.map(f => `• ${f}`).join('<br>')}
                    </small>
                </div>
            </div>
        `;
    }
}

document.getElementById('license_type').addEventListener('change', updateLicensePreview);
document.getElementById('license_duration').addEventListener('change', updateLicensePreview);

// Auto-fill contact person from manager name
document.getElementById('manager_name').addEventListener('input', function() {
    const contactPersonField = document.getElementById('contact_person');
    if (!contactPersonField.value || contactPersonField.value === contactPersonField.dataset.autoFilled) {
        contactPersonField.value = this.value;
        contactPersonField.dataset.autoFilled = this.value;
    }
});

// Password Strength Indicator
document.getElementById('manager_password').addEventListener('input', function() {
    const password = this.value;
    const strength = checkPasswordStrength(password);
    // يمكن إضافة مؤشر قوة كلمة المرور هنا
});

function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}
</script>
@endsection
