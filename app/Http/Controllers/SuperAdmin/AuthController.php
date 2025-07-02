<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل دخول السوبر أدمن
     */
    public function showLoginForm()
    {
        return view('super-admin.login');
    }

    /**
     * تسجيل دخول السوبر أدمن
     */
    public function login(Request $request)
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



        // البحث عن السوبر أدمن
        $admin = SuperAdmin::where('email', $request->email)->first();

        if (!$admin) {
            
            // تسجيل محاولة تسجيل دخول فاشلة
            AdminAuditLog::createLog([
                'action' => 'super_admin_login_failed',
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
                'severity' => 'high',
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
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'super_admin_login_failed',
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
        if (!Hash::check($request->password, $admin->password)) {
            
            // زيادة عدد المحاولات الفاشلة
            $admin->incrementLoginAttempts();
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'super_admin_login_failed',
                'description' => "محاولة تسجيل دخول بكلمة مرور خاطئة: {$admin->name}",
                'metadata' => [
                    'admin_id' => $admin->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'reason' => 'wrong_password',
                    'failed_attempts' => $admin->login_attempts,
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
            
            AdminAuditLog::createLog([
                'super_admin_id' => $admin->id,
                'action' => 'super_admin_login_failed',
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
        $admin->recordSuccessfulLogin($request->ip());

        // تسجيل العملية في سجل التدقيق
        AdminAuditLog::createLog([
            'super_admin_id' => $admin->id,
            'action' => 'super_admin_login',
            'description' => "تسجيل دخول ناجح للسوبر أدمن: {$admin->name}",
            'metadata' => [
                'admin_id' => $admin->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_method' => 'super_admin_portal',
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'category' => 'authentication',
            'severity' => 'low',
            'status' => 'success',
        ]);


        
        return response()->json([
            'success' => true,
            'message' => 'مرحباً بك في لوحة التحكم الرئيسية',
            'redirect' => route('super-admin.dashboard')
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
                'action' => 'super_admin_logout',
                'description' => "تسجيل خروج السوبر أدمن: {$admin->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'category' => 'authentication',
                'severity' => 'low',
                'status' => 'success',
            ]);
            
            Auth::guard('super_admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login');
    }
}
