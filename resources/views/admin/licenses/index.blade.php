@extends('admin.layouts.app')

@section('title', 'إدارة التراخيص')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">إدارة التراخيص</h1>
                    <p class="text-muted">إدارة أنواع التراخيص والباقات المتاحة</p>
                </div>
                <div>
                    <a href="{{ route('admin.licenses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إضافة ترخيص جديد
                    </a>
                    <form method="POST" action="{{ route('admin.licenses.create-defaults') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-magic me-2"></i>
                            إنشاء التراخيص الافتراضية
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي التراخيص
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                التراخيص النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['active']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                التراخيص العامة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['public']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                مع نسخة تجريبية
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['with_trial']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول التراخيص -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة التراخيص</h6>
        </div>
        <div class="card-body">
            @if($licenses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>الترخيص</th>
                                <th>النوع</th>
                                <th>الحدود</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>المستأجرين</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licenses as $license)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $license->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($license->description, 50) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $license->license_type }}</span>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>المستخدمين:</strong> {{ $license->max_users }}<br>
                                            <strong>التخزين:</strong> {{ $license->max_storage_gb }} GB<br>
                                            <strong>API:</strong> {{ number_format($license->max_api_calls_per_month) }}
                                        </small>
                                    </td>
                                    <td>
                                        <div>
                                            @if($license->price_monthly > 0)
                                                <strong>{{ number_format($license->price_monthly, 2) }} {{ $license->currency }}</strong>/شهر<br>
                                                <small class="text-muted">{{ number_format($license->price_yearly, 2) }} {{ $license->currency }}/سنة</small>
                                            @else
                                                <span class="badge bg-success">مجاني</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $license->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ $license->status }}
                                        </span>
                                        @if($license->is_public)
                                            <br><small class="text-success">عام</small>
                                        @else
                                            <br><small class="text-warning">خاص</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $license->tenants_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.licenses.show', $license) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.licenses.edit', $license) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.licenses.toggle-status', $license) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $license->status === 'active' ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $license->status === 'active' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $licenses->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-key fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد تراخيص</h5>
                    <p class="text-muted">لم يتم إنشاء أي تراخيص بعد</p>
                    <a href="{{ route('admin.licenses.create') }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus me-2"></i>
                        إضافة ترخيص جديد
                    </a>
                    <form method="POST" action="{{ route('admin.licenses.create-defaults') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-magic me-2"></i>
                            إنشاء التراخيص الافتراضية
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
