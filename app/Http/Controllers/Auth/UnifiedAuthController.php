<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TenantUser;
use App\Models\SuperAdmin;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UnifiedAuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول الموحدة
     */
    public function showLoginForm()
    {
        return view('auth.unified-login');
    }

    /**
     * تسجيل دخول المستخدمين العاديين
     */
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // التحقق من Rate Limiting
        $key = 'login-attempts:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. حاول مرة أخرى بعد {$seconds} ثانية."
            ], 429);
        }

        // محاولة تسجيل الدخول للمستخدمين العاديين
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            // محاولة البحث في مستخدمي المستأجرين
            $tenantUser = TenantUser::where('email', $request->email)->first();
            
            if ($tenantUser && Hash::check($request->password, $tenantUser->password)) {
                // التحقق من حالة المستخدم والمستأجر
                if ($tenantUser->status !== 'active') {
                    RateLimiter::hit($key);
                    return response()->json([
                        'success' => false,
                        'message' => 'حسابك معطل. يرجى التواصل مع الإدارة.'
                    ], 401);
                }

                if ($tenantUser->tenant->status !== 'active') {
                    RateLimiter::hit($key);
                    return response()->json([
                        'success' => false,
                        'message' => 'حساب المستأجر معطل. يرجى التواصل مع الإدارة.'
                    ], 401);
                }

                // تسجيل الدخول
                Auth::guard('tenant_user')->login($tenantUser, $request->filled('remember'));
                
                // تحديث آخر تسجيل دخول
                $tenantUser->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);

                // تسجيل العملية في سجل التدقيق
                AdminAuditLog::createLog([
                    'tenant_id' => $tenantUser->tenant_id,
                    'action' => 'tenant_user_login',
                    'description' => "تسجيل دخول مستخدم المستأجر: {$tenantUser->name}",
                    'metadata' => [
                        'user_id' => $tenantUser->id,
                        'tenant_id' => $tenantUser->tenant_id,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'category' => 'authentication',
                    'severity' => 'low',
                    'status' => 'success',
                ]);

                RateLimiter::clear($key);
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل الدخول بنجاح',
                    'redirect' => route('dashboard')
                ]);
            }
        } else {
            // تسجيل دخول المستخدم العادي
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user, $request->filled('remember'));
                
                // تحديث آخر تسجيل دخول
                $user->update([
                    'last_login_at' => now(),
                ]);

                RateLimiter::clear($key);
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل الدخول بنجاح',
                    'redirect' => route('dashboard')
                ]);
            }
        }

        // فشل تسجيل الدخول
        RateLimiter::hit($key);
        
        return response()->json([
            'success' => false,
            'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
        ], 401);
    }

    /**
     * تسجيل دخول المديرين
     */
    public function loginAdmin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى التأكد من صحة البيانات المدخلة.'
            ], 422);
        }

        // التحقق من Rate Limiting
        $key = 'admin-login-attempts:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. حاول مرة أخرى بعد {$seconds} ثانية."
            ], 429);
        }

        // البحث عن السوبر أدمن
        $admin = SuperAdmin::where('email', $request->email)->first();

        \Log::info('Admin login attempt', [
            'email' => $request->email,
            'admin_found' => $admin ? 'yes' : 'no',
            'admin_status' => $admin ? $admin->status : 'N/A',
        ]);

        if (!$admin) {
            RateLimiter::hit($key);
            
            // تسجيل محاولة تسجيل دخول فاشلة
            AdminAuditLog::createLog([
                'action' => 'admin_login_failed',
                'description' => "محاولة تسجيل دخول فاشلة - بريد إلكتروني غير موجود: {$request->email}",
                'metadata' => [
                    'email' => $request->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'reason' => 'email_not_found',
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'category' => 'authentication',
                'severity' => 'medium',
                'status' => 'failed',
                'error_message' => 'البريد الإلكتروني غير موجود',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
            ], 401);
        }

        // التحقق من حالة الحساب
        if ($admin->status !== 'active') {
            RateLimiter::hit($key);
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'admin_login_failed',
                'description' => "محاولة تسجيل دخول لحساب معطل: {$admin->name}",
                'metadata' => [
                    'admin_id' => $admin->id,
                    'admin_status' => $admin->status,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'reason' => 'account_disabled',
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'category' => 'authentication',
                'severity' => 'high',
                'status' => 'failed',
                'error_message' => 'الحساب معطل',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حسابك معطل. يرجى التواصل مع الإدارة.'
            ], 401);
        }

        // التحقق من كلمة المرور
        $passwordCheck = Hash::check($request->password, $admin->password);

        \Log::info('Password check', [
            'email' => $request->email,
            'password_provided' => $request->password,
            'password_check_result' => $passwordCheck ? 'CORRECT' : 'WRONG',
        ]);

        if (!$passwordCheck) {
            RateLimiter::hit($key);
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'admin_login_failed',
                'description' => "محاولة تسجيل دخول بكلمة مرور خاطئة: {$admin->name}",
                'metadata' => [
                    'admin_id' => $admin->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'reason' => 'wrong_password',
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'category' => 'authentication',
                'severity' => 'medium',
                'status' => 'failed',
                'error_message' => 'كلمة مرور خاطئة',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
            ], 401);
        }

        // التحقق من قفل الحساب
        if ($admin->isLocked()) {
            RateLimiter::hit($key);
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'admin_login_failed',
                'description' => "محاولة تسجيل دخول لحساب مقفل: {$admin->name}",
                'metadata' => [
                    'admin_id' => $admin->id,
                    'locked_until' => $admin->locked_until,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'reason' => 'account_locked',
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'category' => 'authentication',
                'severity' => 'high',
                'status' => 'failed',
                'error_message' => 'الحساب مقفل',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حسابك مقفل مؤقتاً. يرجى المحاولة لاحقاً.'
            ], 401);
        }

        // تسجيل الدخول بنجاح
        Auth::guard('super_admin')->login($admin, $request->filled('remember'));
        
        // تحديث آخر تسجيل دخول
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'login_attempts' => 0, // إعادة تعيين عدد المحاولات الفاشلة
        ]);

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => $admin->id,
            'action' => 'admin_login',
            'description' => "تسجيل دخول ناجح: {$admin->name}",
            'metadata' => [
                'admin_id' => $admin->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'category' => 'authentication',
            'severity' => 'low',
            'status' => 'success',
        ]);

        RateLimiter::clear($key);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'redirect' => url('/admin/dashboard')
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        if (Auth::guard('super_admin')->check()) {
            $admin = Auth::guard('super_admin')->user();
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'admin_logout',
                'description' => "تسجيل خروج: {$admin->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'category' => 'authentication',
                'severity' => 'low',
                'status' => 'success',
            ]);
            
            Auth::guard('super_admin')->logout();
        } elseif (Auth::guard('tenant_user')->check()) {
            Auth::guard('tenant_user')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
