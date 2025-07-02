@extends('super-admin.layouts.app')

@section('title', 'إدارة المديرين')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users-cog me-2"></i>
                إدارة المديرين
            </h1>
            <p class="text-muted">إدارة حسابات السوبر أدمن والصلاحيات</p>
        </div>
        <div>
            <a href="{{ route('super-admin.admins.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة مدير جديد
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                المديرين النشطين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\SuperAdmin::where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                المديرين المعطلين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\SuperAdmin::where('status', '!=', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                دخول اليوم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\SuperAdmin::whereDate('last_login_at', today())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-in-alt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المديرين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\SuperAdmin::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admins Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                قائمة المديرين
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>المدير</th>
                            <th>البريد الإلكتروني</th>
                            <th>الصلاحيات</th>
                            <th>آخر دخول</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $admins = \App\Models\SuperAdmin::orderBy('created_at', 'desc')->get();
                        @endphp
                        @forelse($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            {{ substr($admin->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $admin->name }}</h6>
                                        <small class="text-muted">{{ $admin->role ?? 'مدير' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($admin->can_create_tenants)
                                        <span class="badge bg-success">إنشاء مستأجرين</span>
                                    @endif
                                    @if($admin->can_delete_tenants)
                                        <span class="badge bg-danger">حذف مستأجرين</span>
                                    @endif
                                    @if($admin->can_manage_licenses)
                                        <span class="badge bg-info">إدارة تراخيص</span>
                                    @endif
                                    @if($admin->can_view_all_data)
                                        <span class="badge bg-warning">عرض جميع البيانات</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($admin->last_login_at)
                                    <div>{{ $admin->last_login_at->format('Y-m-d') }}</div>
                                    <small class="text-muted">{{ $admin->last_login_at->format('H:i') }}</small>
                                @else
                                    <span class="text-muted">لم يسجل دخول</span>
                                @endif
                            </td>
                            <td>
                                @if($admin->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($admin->status == 'inactive')
                                    <span class="badge bg-danger">معطل</span>
                                @elseif($admin->status == 'suspended')
                                    <span class="badge bg-warning">معلق</span>
                                @else
                                    <span class="badge bg-secondary">{{ $admin->status }}</span>
                                @endif
                                
                                @if($admin->isLocked())
                                    <span class="badge bg-warning">مقفل</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $admin->created_at->format('Y-m-d') }}</div>
                                <small class="text-muted">{{ $admin->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            onclick="viewAdmin({{ $admin->id }})" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if($admin->id !== auth('super_admin')->id())
                                        @if($admin->status == 'active')
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="toggleAdmin({{ $admin->id }}, 'deactivate')" title="تعطيل">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="toggleAdmin({{ $admin->id }}, 'activate')" title="تفعيل">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @endif
                                        
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="resetPassword({{ $admin->id }})" title="إعادة تعيين كلمة المرور">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteAdmin({{ $admin->id }})" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-info">الحساب الحالي</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا يوجد مديرين</p>
                                <a href="{{ route('super-admin.admins.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    إضافة مدير جديد
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>
                آخر أنشطة المديرين
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>المدير</th>
                            <th>النشاط</th>
                            <th>الوقت</th>
                            <th>عنوان IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recentActivities = \App\Models\AdminAuditLog::with('superAdmin')
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        @forelse($recentActivities as $activity)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-initial bg-secondary rounded-circle">
                                            {{ substr($activity->superAdmin->name ?? 'N', 0, 1) }}
                                        </div>
                                    </div>
                                    <span>{{ $activity->superAdmin->name ?? 'غير محدد' }}</span>
                                </div>
                            </td>
                            <td>
                                <div>{{ $activity->action }}</div>
                                <small class="text-muted">{{ Str::limit($activity->description, 50) }}</small>
                            </td>
                            <td>
                                <div>{{ $activity->created_at->format('H:i:s') }}</div>
                                <small class="text-muted">{{ $activity->created_at->format('Y-m-d') }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $activity->ip_address }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">لا توجد أنشطة حديثة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Admin Details Modal -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المدير</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="adminModalBody">
                <!-- سيتم ملؤها بـ JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
function viewAdmin(adminId) {
    // محاكاة عرض تفاصيل المدير
    document.getElementById('adminModalBody').innerHTML = `
        <div class="text-center">
            <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
            <h5>تفاصيل المدير #${adminId}</h5>
            <p class="text-muted">سيتم إضافة التفاصيل الكاملة لاحقاً</p>
        </div>
    `;
    new bootstrap.Modal(document.getElementById('adminModal')).show();
}

function toggleAdmin(adminId, action) {
    const actionText = action === 'activate' ? 'تفعيل' : 'تعطيل';
    if (confirm(`هل أنت متأكد من ${actionText} هذا المدير؟`)) {
        alert(`تم ${actionText} المدير بنجاح`);
        // هنا يمكن إضافة AJAX request
        location.reload();
    }
}

function resetPassword(adminId) {
    if (confirm('هل أنت متأكد من إعادة تعيين كلمة مرور هذا المدير؟ سيتم إرسال كلمة مرور جديدة عبر البريد الإلكتروني.')) {
        alert('تم إرسال كلمة مرور جديدة عبر البريد الإلكتروني');
        // هنا يمكن إضافة AJAX request
    }
}

function deleteAdmin(adminId) {
    if (confirm('هل أنت متأكد من حذف هذا المدير؟ هذا الإجراء لا يمكن التراجع عنه!')) {
        if (confirm('تأكيد نهائي: سيتم حذف المدير وجميع سجلاته!')) {
            alert('تم حذف المدير بنجاح');
            // هنا يمكن إضافة AJAX request
            location.reload();
        }
    }
}
</script>

<style>
.avatar {
    width: 40px;
    height: 40px;
}
.avatar-sm {
    width: 30px;
    height: 30px;
}
.avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
}
</style>
@endsection
