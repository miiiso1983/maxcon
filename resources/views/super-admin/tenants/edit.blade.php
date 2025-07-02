@extends('super-admin.layouts.app')

@section('title', 'تعديل المستأجر')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit me-2"></i>
                تعديل المستأجر: {{ $tenant->name }}
            </h1>
            <p class="text-muted">تحديث بيانات المستأجر ومعلومات الترخيص</p>
        </div>
        <div>
            <a href="{{ route('super-admin.tenants.show', $tenant->id) }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للتفاصيل
            </a>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>
                القائمة
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('super-admin.tenants.update', $tenant->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Tenant Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-building me-2"></i>
                            بيانات المستأجر
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Tenant Name -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    اسم المستأجر <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $tenant->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    البريد الإلكتروني <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $tenant->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    رقم الهاتف <span class="text-danger">*</span>
                                </label>
                                <input type="tel" name="phone" id="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $tenant->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">
                                    شخص الاتصال <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="contact_person" id="contact_person" 
                                       class="form-control @error('contact_person') is-invalid @enderror" 
                                       value="{{ old('contact_person', $tenant->contact_person) }}" required>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">
                                العنوان <span class="text-danger">*</span>
                            </label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      required>{{ old('address', $tenant->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">
                                    حالة المستأجر <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" 
                                        class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="pending" {{ old('status', $tenant->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                    <option value="suspended" {{ old('status', $tenant->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="license_type" class="form-label">
                                    نوع الترخيص
                                </label>
                                <select name="license_type" id="license_type" class="form-select">
                                    <option value="basic" {{ old('license_type', $tenant->license_type) == 'basic' ? 'selected' : '' }}>أساسي</option>
                                    <option value="premium" {{ old('license_type', $tenant->license_type) == 'premium' ? 'selected' : '' }}>مميز</option>
                                    <option value="enterprise" {{ old('license_type', $tenant->license_type) == 'enterprise' ? 'selected' : '' }}>مؤسسي</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="max_users" class="form-label">الحد الأقصى للمستخدمين</label>
                                <input type="number" name="max_users" id="max_users" 
                                       class="form-control" min="1" 
                                       value="{{ old('max_users', $tenant->max_users) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="max_storage_gb" class="form-label">مساحة التخزين (جيجابايت)</label>
                                <input type="number" name="max_storage_gb" id="max_storage_gb" 
                                       class="form-control" min="1" 
                                       value="{{ old('max_storage_gb', $tenant->max_storage_gb) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="monthly_fee" class="form-label">الرسوم الشهرية</label>
                                <div class="input-group">
                                    <input type="number" name="monthly_fee" id="monthly_fee" 
                                           class="form-control" step="0.01" min="0" 
                                           value="{{ old('monthly_fee', $tenant->monthly_fee) }}">
                                    <span class="input-group-text">{{ $tenant->currency ?? 'SAR' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="billing_cycle" class="form-label">دورة الفوترة</label>
                                <select name="billing_cycle" id="billing_cycle" class="form-select">
                                    <option value="monthly" {{ old('billing_cycle', $tenant->billing_cycle) == 'monthly' ? 'selected' : '' }}>شهرية</option>
                                    <option value="quarterly" {{ old('billing_cycle', $tenant->billing_cycle) == 'quarterly' ? 'selected' : '' }}>ربع سنوية</option>
                                    <option value="yearly" {{ old('billing_cycle', $tenant->billing_cycle) == 'yearly' ? 'selected' : '' }}>سنوية</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- License Dates -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-calendar me-2"></i>
                            تواريخ الترخيص
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="license_start_date" class="form-label">تاريخ بداية الترخيص</label>
                                <input type="date" name="license_start_date" id="license_start_date" 
                                       class="form-control" 
                                       value="{{ old('license_start_date', $tenant->license_start_date) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="license_end_date" class="form-label">تاريخ انتهاء الترخيص</label>
                                <input type="date" name="license_end_date" id="license_end_date" 
                                       class="form-control" 
                                       value="{{ old('license_end_date', $tenant->license_end_date) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="next_billing_date" class="form-label">تاريخ الفاتورة التالية</label>
                                <input type="date" name="next_billing_date" id="next_billing_date" 
                                       class="form-control" 
                                       value="{{ old('next_billing_date', $tenant->next_billing_date) }}">
                            </div>
                        </div>

                        @if($tenant->license_end_date && \Carbon\Carbon::parse($tenant->license_end_date)->isPast())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>تنبيه:</strong> انتهت صلاحية الترخيص! يجب تحديث تاريخ الانتهاء.
                            </div>
                        @elseif($tenant->license_end_date && \Carbon\Carbon::parse($tenant->license_end_date)->diffInDays() <= 30)
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                <strong>تنبيه:</strong> سينتهي الترخيص خلال {{ \Carbon\Carbon::parse($tenant->license_end_date)->diffInDays() }} يوم.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Current Information & Actions -->
            <div class="col-lg-4">
                <!-- Current Status -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-info-circle me-2"></i>
                            الحالة الحالية
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">الحالة</label>
                            <div>
                                @if($tenant->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($tenant->status == 'pending')
                                    <span class="badge bg-warning">في الانتظار</span>
                                @elseif($tenant->status == 'suspended')
                                    <span class="badge bg-danger">معلق</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">نوع الترخيص الحالي</label>
                            <div>
                                @if($tenant->license_type == 'basic')
                                    <span class="badge bg-primary">أساسي</span>
                                @elseif($tenant->license_type == 'premium')
                                    <span class="badge bg-success">مميز</span>
                                @elseif($tenant->license_type == 'enterprise')
                                    <span class="badge bg-warning">مؤسسي</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">المستخدمين</label>
                            <div class="fw-bold">{{ $tenant->users->count() }} / {{ $tenant->max_users ?? 'غير محدد' }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">تاريخ الإنشاء</label>
                            <div class="fw-bold">{{ $tenant->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-tools me-2"></i>
                            إجراءات سريعة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-info" onclick="autoFillLimits()">
                                <i class="fas fa-magic me-2"></i>
                                ملء الحدود تلقائياً
                            </button>
                            
                            <button type="button" class="btn btn-outline-success" onclick="extendLicense()">
                                <i class="fas fa-plus me-2"></i>
                                تمديد الترخيص شهر
                            </button>
                            
                            <button type="button" class="btn btn-outline-warning" onclick="resetToDefaults()">
                                <i class="fas fa-undo me-2"></i>
                                إعادة للقيم الافتراضية
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                حفظ التغييرات
                            </button>
                            
                            <a href="{{ route('super-admin.tenants.show', $tenant->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Auto-fill limits based on license type
function autoFillLimits() {
    const licenseType = document.getElementById('license_type').value;
    const limits = {
        'basic': { users: 5, storage: 2, fee: 99 },
        'premium': { users: 20, storage: 10, fee: 299 },
        'enterprise': { users: 1000, storage: 100, fee: 999 }
    };
    
    if (limits[licenseType]) {
        document.getElementById('max_users').value = limits[licenseType].users;
        document.getElementById('max_storage_gb').value = limits[licenseType].storage;
        document.getElementById('monthly_fee').value = limits[licenseType].fee;
    }
}

// Extend license by one month
function extendLicense() {
    const currentEndDate = document.getElementById('license_end_date').value;
    if (currentEndDate) {
        const date = new Date(currentEndDate);
        date.setMonth(date.getMonth() + 1);
        document.getElementById('license_end_date').value = date.toISOString().split('T')[0];
    } else {
        const today = new Date();
        today.setMonth(today.getMonth() + 1);
        document.getElementById('license_end_date').value = today.toISOString().split('T')[0];
    }
}

// Reset to default values
function resetToDefaults() {
    if (confirm('هل أنت متأكد من إعادة تعيين القيم للافتراضية؟')) {
        document.getElementById('license_type').value = 'basic';
        autoFillLimits();
        document.getElementById('billing_cycle').value = 'monthly';
    }
}

// Auto-fill limits when license type changes
document.getElementById('license_type').addEventListener('change', function() {
    if (confirm('هل تريد تحديث الحدود تلقائياً حسب نوع الترخيص الجديد؟')) {
        autoFillLimits();
    }
});
</script>
@endsection
