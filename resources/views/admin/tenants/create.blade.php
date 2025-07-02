@extends('admin.layouts.app')

@section('title', 'إضافة مستأجر جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">إضافة مستأجر جديد</h1>
                    <p class="text-muted">إنشاء حساب مستأجر جديد في النظام</p>
                </div>
                <div>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">بيانات المستأجر</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tenants.store') }}">
                        @csrf
                        
                        <!-- معلومات الشركة -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-building me-2"></i>
                                    معلومات الشركة
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="مثال: صيدلية النور">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required
                                           placeholder="info@pharmacy.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}"
                                           placeholder="+966501234567">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="license_type" class="form-label">نوع الترخيص <span class="text-danger">*</span></label>
                                    <select class="form-control @error('license_type') is-invalid @enderror" 
                                            id="license_type" name="license_type" required>
                                        <option value="">اختر نوع الترخيص</option>
                                        @foreach($licenses as $license)
                                            <option value="{{ $license->license_type }}" 
                                                    {{ old('license_type') == $license->license_type ? 'selected' : '' }}
                                                    data-max-users="{{ $license->max_users }}"
                                                    data-max-storage="{{ $license->max_storage_gb }}"
                                                    data-price="{{ $license->price_monthly }}">
                                                {{ $license->name }} 
                                                @if($license->price_monthly > 0)
                                                    ({{ number_format($license->price_monthly) }} ريال/شهر)
                                                @else
                                                    (مجاني)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('license_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3"
                                              placeholder="العنوان الكامل للشركة">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- معلومات مدير النظام -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user-tie me-2"></i>
                                    معلومات مدير النظام
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="manager_name" class="form-label">اسم المدير <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('manager_name') is-invalid @enderror" 
                                           id="manager_name" name="manager_name" value="{{ old('manager_name') }}" required
                                           placeholder="اسم مدير النظام">
                                    @error('manager_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="manager_email" class="form-label">بريد المدير <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('manager_email') is-invalid @enderror" 
                                           id="manager_email" name="manager_email" value="{{ old('manager_email') }}" required
                                           placeholder="manager@pharmacy.com">
                                    @error('manager_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="manager_password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('manager_password') is-invalid @enderror" 
                                           id="manager_password" name="manager_password" required
                                           placeholder="كلمة مرور قوية">
                                    @error('manager_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        يجب أن تحتوي على 8 أحرف على الأقل مع أرقام ورموز
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="manager_phone" class="form-label">هاتف المدير</label>
                                    <input type="text" class="form-control @error('manager_phone') is-invalid @enderror" 
                                           id="manager_phone" name="manager_phone" value="{{ old('manager_phone') }}"
                                           placeholder="+966501234567">
                                    @error('manager_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- إعدادات الترخيص -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>
                                    إعدادات الترخيص
                                </h6>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="license_start_date" class="form-label">تاريخ بداية الترخيص</label>
                                    <input type="date" class="form-control @error('license_start_date') is-invalid @enderror" 
                                           id="license_start_date" name="license_start_date" 
                                           value="{{ old('license_start_date', now()->format('Y-m-d')) }}">
                                    @error('license_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="license_end_date" class="form-label">تاريخ انتهاء الترخيص</label>
                                    <input type="date" class="form-control @error('license_end_date') is-invalid @enderror" 
                                           id="license_end_date" name="license_end_date" 
                                           value="{{ old('license_end_date', now()->addYear()->format('Y-m-d')) }}">
                                    @error('license_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">حالة المستأجر</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ملاحظات -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3"
                                              placeholder="أي ملاحظات إضافية حول المستأجر">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الحفظ -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary me-2">
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
                    </form>
                </div>
            </div>
        </div>

        <!-- معلومات الترخيص -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الترخيص</h6>
                </div>
                <div class="card-body" id="license-info">
                    <div class="text-center text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>اختر نوع الترخيص لعرض التفاصيل</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">نصائح</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            تأكد من صحة البريد الإلكتروني
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            اختر كلمة مرور قوية للمدير
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            يمكن تعديل الإعدادات لاحقاً
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            سيتم إرسال بيانات الدخول للمدير
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('license_type').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const licenseInfo = document.getElementById('license-info');
    
    if (selectedOption.value) {
        const maxUsers = selectedOption.dataset.maxUsers;
        const maxStorage = selectedOption.dataset.maxStorage;
        const price = selectedOption.dataset.price;
        
        licenseInfo.innerHTML = `
            <div class="text-center">
                <h5 class="text-primary mb-3">${selectedOption.text.split('(')[0].trim()}</h5>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h6 class="text-muted mb-1">المستخدمين</h6>
                            <h4 class="text-primary">${maxUsers}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted mb-1">التخزين</h6>
                        <h4 class="text-primary">${maxStorage} GB</h4>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h6 class="text-muted mb-1">السعر الشهري</h6>
                    <h4 class="text-success">${price > 0 ? price + ' ريال' : 'مجاني'}</h4>
                </div>
            </div>
        `;
    } else {
        licenseInfo.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p>اختر نوع الترخيص لعرض التفاصيل</p>
            </div>
        `;
    }
});
</script>
@endpush
