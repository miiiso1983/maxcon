@extends('super-admin.layouts.app')

@section('title', 'إضافة ترخيص جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                إضافة ترخيص جديد
            </h1>
            <p class="text-muted">إنشاء ترخيص جديد لمستأجر</p>
        </div>
        <div>
            <a href="{{ route('super-admin.licenses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Create License Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-key me-2"></i>
                        بيانات الترخيص
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('super-admin.licenses.store') }}" method="POST" id="licenseForm">
                        @csrf
                        
                        <!-- Tenant Selection -->
                        <div class="mb-4">
                            <label for="tenant_id" class="form-label">
                                <i class="fas fa-building me-2"></i>
                                المستأجر <span class="text-danger">*</span>
                            </label>
                            <select name="tenant_id" id="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                                <option value="">اختر المستأجر</option>
                                @foreach($tenants ?? [] as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} ({{ $tenant->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- License Type -->
                        <div class="mb-4">
                            <label for="type" class="form-label">
                                <i class="fas fa-tag me-2"></i>
                                نوع الترخيص <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">اختر نوع الترخيص</option>
                                <option value="basic" {{ old('type') == 'basic' ? 'selected' : '' }}>أساسي</option>
                                <option value="premium" {{ old('type') == 'premium' ? 'selected' : '' }}>مميز</option>
                                <option value="enterprise" {{ old('type') == 'enterprise' ? 'selected' : '' }}>مؤسسي</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- License Duration -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="starts_at" class="form-label">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    تاريخ البداية <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="starts_at" id="starts_at" 
                                       class="form-control @error('starts_at') is-invalid @enderror" 
                                       value="{{ old('starts_at', date('Y-m-d')) }}" required>
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="duration_months" class="form-label">
                                    <i class="fas fa-clock me-2"></i>
                                    مدة الترخيص (بالأشهر) <span class="text-danger">*</span>
                                </label>
                                <select name="duration_months" id="duration_months" 
                                        class="form-select @error('duration_months') is-invalid @enderror" required>
                                    <option value="">اختر المدة</option>
                                    <option value="1" {{ old('duration_months') == '1' ? 'selected' : '' }}>شهر واحد</option>
                                    <option value="3" {{ old('duration_months') == '3' ? 'selected' : '' }}>3 أشهر</option>
                                    <option value="6" {{ old('duration_months') == '6' ? 'selected' : '' }}>6 أشهر</option>
                                    <option value="12" {{ old('duration_months') == '12' ? 'selected' : '' }}>سنة واحدة</option>
                                    <option value="24" {{ old('duration_months') == '24' ? 'selected' : '' }}>سنتان</option>
                                    <option value="36" {{ old('duration_months') == '36' ? 'selected' : '' }}>3 سنوات</option>
                                </select>
                                @error('duration_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- License Features -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-list-check me-2"></i>
                                مميزات الترخيص
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" 
                                               value="inventory_management" id="feature_inventory" 
                                               {{ in_array('inventory_management', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_inventory">
                                            إدارة المخزون
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" 
                                               value="sales_management" id="feature_sales"
                                               {{ in_array('sales_management', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_sales">
                                            إدارة المبيعات
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" 
                                               value="financial_reports" id="feature_reports"
                                               {{ in_array('financial_reports', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_reports">
                                            التقارير المالية
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" 
                                               value="customer_management" id="feature_customers"
                                               {{ in_array('customer_management', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_customers">
                                            إدارة العملاء
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" 
                                               value="employee_management" id="feature_employees"
                                               {{ in_array('employee_management', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_employees">
                                            إدارة الموظفين
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" 
                                               value="api_access" id="feature_api"
                                               {{ in_array('api_access', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_api">
                                            الوصول للـ API
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- License Limits -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="max_users" class="form-label">
                                    <i class="fas fa-users me-2"></i>
                                    الحد الأقصى للمستخدمين
                                </label>
                                <input type="number" name="max_users" id="max_users" 
                                       class="form-control @error('max_users') is-invalid @enderror" 
                                       value="{{ old('max_users', 10) }}" min="1" max="1000">
                                @error('max_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="max_storage_gb" class="form-label">
                                    <i class="fas fa-hdd me-2"></i>
                                    مساحة التخزين (جيجابايت)
                                </label>
                                <input type="number" name="max_storage_gb" id="max_storage_gb" 
                                       class="form-control @error('max_storage_gb') is-invalid @enderror" 
                                       value="{{ old('max_storage_gb', 5) }}" min="1" max="1000">
                                @error('max_storage_gb')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="max_branches" class="form-label">
                                    <i class="fas fa-store me-2"></i>
                                    الحد الأقصى للفروع
                                </label>
                                <input type="number" name="max_branches" id="max_branches" 
                                       class="form-control @error('max_branches') is-invalid @enderror" 
                                       value="{{ old('max_branches', 1) }}" min="1" max="100">
                                @error('max_branches')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>
                                ملاحظات
                            </label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="أي ملاحظات إضافية حول الترخيص">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('super-admin.licenses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                إنشاء الترخيص
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- License Preview -->
        <div class="col-lg-4">
            <div class="card shadow">
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
                            <p>اختر المستأجر ونوع الترخيص لرؤية المعاينة</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- License Types Info -->
            <div class="card shadow mt-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        أنواع التراخيص
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">أساسي</h6>
                        <small class="text-muted">
                            • حتى 5 مستخدمين<br>
                            • 2 جيجابايت تخزين<br>
                            • فرع واحد<br>
                            • المميزات الأساسية
                        </small>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-success">مميز</h6>
                        <small class="text-muted">
                            • حتى 20 مستخدم<br>
                            • 10 جيجابايت تخزين<br>
                            • حتى 5 فروع<br>
                            • جميع المميزات
                        </small>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-warning">مؤسسي</h6>
                        <small class="text-muted">
                            • مستخدمين غير محدود<br>
                            • 100 جيجابايت تخزين<br>
                            • فروع غير محدودة<br>
                            • جميع المميزات + API
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill license limits based on type
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const maxUsers = document.getElementById('max_users');
    const maxStorage = document.getElementById('max_storage_gb');
    const maxBranches = document.getElementById('max_branches');
    
    switch(type) {
        case 'basic':
            maxUsers.value = 5;
            maxStorage.value = 2;
            maxBranches.value = 1;
            break;
        case 'premium':
            maxUsers.value = 20;
            maxStorage.value = 10;
            maxBranches.value = 5;
            break;
        case 'enterprise':
            maxUsers.value = 1000;
            maxStorage.value = 100;
            maxBranches.value = 100;
            break;
    }
    
    updatePreview();
});

// Update preview when form changes
function updatePreview() {
    const tenantSelect = document.getElementById('tenant_id');
    const typeSelect = document.getElementById('type');
    const startsAt = document.getElementById('starts_at');
    const duration = document.getElementById('duration_months');
    
    if (tenantSelect.value && typeSelect.value) {
        const tenantName = tenantSelect.options[tenantSelect.selectedIndex].text;
        const startDate = new Date(startsAt.value);
        const endDate = new Date(startDate);
        endDate.setMonth(endDate.getMonth() + parseInt(duration.value || 12));
        
        document.getElementById('licensePreview').innerHTML = `
            <div class="text-center">
                <div class="badge bg-${typeSelect.value === 'basic' ? 'primary' : typeSelect.value === 'premium' ? 'success' : 'warning'} mb-3">
                    ${typeSelect.options[typeSelect.selectedIndex].text}
                </div>
                <h6>${tenantName.split(' (')[0]}</h6>
                <hr>
                <div class="text-start">
                    <small class="text-muted">
                        <strong>تاريخ البداية:</strong><br>
                        ${startDate.toLocaleDateString('ar-SA')}<br><br>
                        <strong>تاريخ الانتهاء:</strong><br>
                        ${endDate.toLocaleDateString('ar-SA')}<br><br>
                        <strong>المدة:</strong><br>
                        ${duration.value || 12} شهر
                    </small>
                </div>
            </div>
        `;
    }
}

// Add event listeners
document.getElementById('tenant_id').addEventListener('change', updatePreview);
document.getElementById('starts_at').addEventListener('change', updatePreview);
document.getElementById('duration_months').addEventListener('change', updatePreview);
</script>
@endsection
