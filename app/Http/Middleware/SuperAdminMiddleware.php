<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من تسجيل الدخول كسوبر أدمن
        if (!Auth::guard('super_admin')->check()) {
            // إذا كان الطلب AJAX، إرجاع JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول كسوبر أدمن للوصول لهذه الصفحة',
                    'redirect' => route('super-admin.login')
                ], 401);
            }
            
            // إعادة توجيه لصفحة تسجيل دخول السوبر أدمن
            return redirect()->route('super-admin.login')
                ->with('error', 'يجب تسجيل الدخول كسوبر أدمن للوصول لهذه الصفحة');
        }

        // التحقق من حالة السوبر أدمن
        $admin = Auth::guard('super_admin')->user();
        
        if ($admin->status !== 'active') {
            Auth::guard('super_admin')->logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حسابك معطل. يرجى التواصل مع الإدارة',
                    'redirect' => route('super-admin.login')
                ], 403);
            }
            
            return redirect()->route('super-admin.login')
                ->with('error', 'حسابك معطل. يرجى التواصل مع الإدارة');
        }

        // التحقق من قفل الحساب
        if ($admin->isLocked()) {
            Auth::guard('super_admin')->logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حسابك مقفل مؤقتاً. يرجى المحاولة لاحقاً',
                    'redirect' => route('super-admin.login')
                ], 403);
            }
            
            return redirect()->route('super-admin.login')
                ->with('error', 'حسابك مقفل مؤقتاً. يرجى المحاولة لاحقاً');
        }

        return $next($request);
    }
}
