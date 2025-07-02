<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * عرض نموذج تسجيل الدخول
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = 'admin-login:' . $request->ip();
        
        // التحقق من محاولات تسجيل الدخول
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "تم تجاوز عدد المحاولات المسموحة. حاول مرة أخرى بعد {$seconds} ثانية.",
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // البحث عن المستخدم
        $admin = SuperAdmin::where('email', $credentials['email'])->first();

        if (!$admin) {
            RateLimiter::hit($key, 300); // 5 دقائق
            AdminAuditLog::createLog([
                'action' => 'login_failed',
                'description' => 'محاولة تسجيل دخول بإيميل غير موجود: ' . $credentials['email'],
                'category' => 'authentication',
                'severity' => 'medium',
                'status' => 'failed',
                'error_message' => 'البريد الإلكتروني غير موجود',
            ]);

            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        // التحقق من حالة الحساب
        if (!$admin->isActive()) {
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'login_blocked',
                'description' => 'محاولة تسجيل دخول لحساب غير نشط أو مقفل',
                'category' => 'authentication',
                'severity' => 'high',
                'status' => 'failed',
                'error_message' => 'الحساب غير نشط أو مقفل',
            ]);

            throw ValidationException::withMessages([
                'email' => 'حسابك غير نشط أو مقفل مؤقتاً.',
            ]);
        }

        // التحقق من كلمة المرور
        if (!Hash::check($credentials['password'], $admin->password)) {
            RateLimiter::hit($key, 300);
            $admin->incrementLoginAttempts();
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'login_failed',
                'description' => 'محاولة تسجيل دخول بكلمة مرور خاطئة',
                'category' => 'authentication',
                'severity' => 'medium',
                'status' => 'failed',
                'error_message' => 'كلمة المرور خاطئة',
            ]);

            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        // تسجيل الدخول الناجح
        Auth::guard('super_admin')->login($admin, $remember);
        $admin->recordSuccessfulLogin($request->ip());
        RateLimiter::clear($key);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::logLogin($admin, true);

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
                        ->with('success', 'مرحباً بك في لوحة التحكم الرئيسية');
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $admin = Auth::guard('super_admin')->user();

        if ($admin && $admin instanceof SuperAdmin) {
            AdminAuditLog::logLogout($admin);
        }

        Auth::guard('super_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
                        ->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * عرض نموذج تغيير كلمة المرور
     */
    public function showChangePasswordForm()
    {
        return view('admin.auth.change-password');
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Auth::guard('super_admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'كلمة المرور الحالية غير صحيحة.',
            ]);
        }

        $oldValues = ['password_changed_at' => now()];
        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => $admin->id,
            'action' => 'password_changed',
            'description' => 'تغيير كلمة المرور',
            'old_values' => $oldValues,
            'category' => 'authentication',
            'severity' => 'medium',
            'status' => 'success',
        ]);

        return redirect()->route('admin.dashboard')
                        ->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * عرض الملف الشخصي
     */
    public function profile()
    {
        $admin = Auth::guard('super_admin')->user();
        return view('admin.auth.profile', compact('admin'));
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('super_admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:super_admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'locale' => 'required|in:ar,en',
            'timezone' => 'required|string',
        ]);

        $oldValues = $admin->toArray();
        
        $admin->update($request->only([
            'name', 'email', 'phone', 'locale', 'timezone'
        ]));

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => $admin->id,
            'action' => 'profile_updated',
            'description' => 'تحديث الملف الشخصي',
            'old_values' => $oldValues,
            'new_values' => $admin->toArray(),
            'category' => 'user_management',
            'severity' => 'low',
            'status' => 'success',
        ]);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
}
