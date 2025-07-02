@extends('layouts.app')

@section('title', 'إضافة مندوب تجاري جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus me-2"></i>
                إضافة مندوب تجاري جديد
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sales-representatives.dashboard') }}">المندوبين التجاريين</a></li>
                    <li class="breadcrumb-item active">إضافة مندوب جديد</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('sales-representatives.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user-tie me-2"></i>
                بيانات المندوب التجاري
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sales-representatives.store') }}" method="POST" id="createRepForm">
                @csrf
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">المعلومات الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="employee_code" class="form-label">
                                        <i class="fas fa-barcode me-2"></i>
                                        كود المندوب <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('employee_code') is-invalid @enderror" 
                                           id="employee_code" 
                                           name="employee_code" 
                                           value="{{ old('employee_code') }}" 
                                           placeholder="مثال: REP001"
                                           required>
                                    @error('employee_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-2"></i>
                                        الاسم الكامل <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="الاسم الكامل للمندوب"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>
                                        البريد الإلكتروني <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="example@company.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>
                                        رقم الهاتف <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="+966501234567"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="national_id" class="form-label">
                                        <i class="fas fa-id-card me-2"></i>
                                        رقم الهوية الوطنية
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('national_id') is-invalid @enderror" 
                                           id="national_id" 
                                           name="national_id" 
                                           value="{{ old('national_id') }}" 
                                           placeholder="1234567890">
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        العنوان
                                    </label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" 
                                              name="address" 
                                              rows="3" 
                                              placeholder="العنوان الكامل">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Information -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">معلومات التوظيف</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="hire_date" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>
                                        تاريخ التوظيف <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('hire_date') is-invalid @enderror" 
                                           id="hire_date" 
                                           name="hire_date" 
                                           value="{{ old('hire_date', date('Y-m-d')) }}" 
                                           required>
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="base_salary" class="form-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        الراتب الأساسي (ريال)
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('base_salary') is-invalid @enderror" 
                                           id="base_salary" 
                                           name="base_salary" 
                                           value="{{ old('base_salary', 0) }}" 
                                           min="0" 
                                           step="0.01"
                                           placeholder="5000.00">
                                    @error('base_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="commission_rate" class="form-label">
                                        <i class="fas fa-percentage me-2"></i>
                                        نسبة العمولة (%)
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('commission_rate') is-invalid @enderror" 
                                           id="commission_rate" 
                                           name="commission_rate" 
                                           value="{{ old('commission_rate', 0) }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           placeholder="2.5">
                                    @error('commission_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="assigned_areas" class="form-label">
                                        <i class="fas fa-map me-2"></i>
                                        المناطق المخصصة
                                    </label>
                                    <select class="form-select @error('assigned_areas') is-invalid @enderror" 
                                            id="assigned_areas" 
                                            name="assigned_areas[]" 
                                            multiple>
                                        <option value="الرياض" {{ in_array('الرياض', old('assigned_areas', [])) ? 'selected' : '' }}>الرياض</option>
                                        <option value="جدة" {{ in_array('جدة', old('assigned_areas', [])) ? 'selected' : '' }}>جدة</option>
                                        <option value="الدمام" {{ in_array('الدمام', old('assigned_areas', [])) ? 'selected' : '' }}>الدمام</option>
                                        <option value="مكة المكرمة" {{ in_array('مكة المكرمة', old('assigned_areas', [])) ? 'selected' : '' }}>مكة المكرمة</option>
                                        <option value="المدينة المنورة" {{ in_array('المدينة المنورة', old('assigned_areas', [])) ? 'selected' : '' }}>المدينة المنورة</option>
                                        <option value="الطائف" {{ in_array('الطائف', old('assigned_areas', [])) ? 'selected' : '' }}>الطائف</option>
                                        <option value="بريدة" {{ in_array('بريدة', old('assigned_areas', [])) ? 'selected' : '' }}>بريدة</option>
                                        <option value="خميس مشيط" {{ in_array('خميس مشيط', old('assigned_areas', [])) ? 'selected' : '' }}>خميس مشيط</option>
                                        <option value="حائل" {{ in_array('حائل', old('assigned_areas', [])) ? 'selected' : '' }}>حائل</option>
                                        <option value="الجبيل" {{ in_array('الجبيل', old('assigned_areas', [])) ? 'selected' : '' }}>الجبيل</option>
                                    </select>
                                    <div class="form-text">يمكن اختيار أكثر من منطقة</div>
                                    @error('assigned_areas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        ملاحظات
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" 
                                              name="notes" 
                                              rows="4" 
                                              placeholder="أي ملاحظات إضافية حول المندوب">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <button type="submit" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-save me-2"></i>
                                    حفظ المندوب
                                </button>
                                <a href="{{ route('sales-representatives.dashboard') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تفعيل Select2 للمناطق المخصصة
    $('#assigned_areas').select2({
        theme: 'bootstrap-5',
        placeholder: 'اختر المناطق المخصصة',
        allowClear: true
    });

    // تنسيق رقم الهاتف
    $('#phone').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.startsWith('966')) {
            value = '+' + value;
        } else if (value.startsWith('5') && value.length === 9) {
            value = '+966' + value;
        }
        this.value = value;
    });

    // تنسيق رقم الهوية
    $('#national_id').on('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 10);
    });

    // تنسيق كود المندوب
    $('#employee_code').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // التحقق من صحة النموذج قبل الإرسال
    $('#createRepForm').on('submit', function(e) {
        let isValid = true;
        let errors = [];

        // التحقق من كود المندوب
        if ($('#employee_code').val().length < 3) {
            errors.push('كود المندوب يجب أن يكون 3 أحرف على الأقل');
            isValid = false;
        }

        // التحقق من البريد الإلكتروني
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test($('#email').val())) {
            errors.push('البريد الإلكتروني غير صحيح');
            isValid = false;
        }

        // التحقق من رقم الهاتف
        const phoneRegex = /^\+966[0-9]{9}$/;
        if (!phoneRegex.test($('#phone').val())) {
            errors.push('رقم الهاتف يجب أن يكون بصيغة +966xxxxxxxxx');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('يرجى تصحيح الأخطاء التالية:\n' + errors.join('\n'));
        }
    });
});
</script>
@endsection
