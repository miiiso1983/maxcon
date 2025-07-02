<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class TenantScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من وجود مستخدم مسجل دخول
        if (!Auth::guard('tenant')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect()->route('tenant.login');
        }

        $user = Auth::guard('tenant')->user();
        $tenant = $user->tenant;

        // التحقق من صحة الترخيص
        if (!$tenant->isLicenseValid()) {
            Auth::guard('tenant')->logout();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'License expired or invalid'], 403);
            }
            
            return redirect()->route('tenant.login')
                           ->with('error', 'انتهت صلاحية الترخيص أو أن الحساب غير نشط');
        }

        // تعيين المستأجر الحالي في الطلب
        $request->merge(['current_tenant' => $tenant]);
        
        // تعيين المستأجر في التطبيق للاستخدام في النماذج
        app()->instance('current_tenant', $tenant);

        return $next($request);
    }
}
