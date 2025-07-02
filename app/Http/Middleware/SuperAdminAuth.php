<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('super_admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('super_admin')->user();

        // التحقق من حالة النشاط
        if (!$admin->isActive()) {
            Auth::guard('super_admin')->logout();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Account is inactive or locked'], 403);
            }
            
            return redirect()->route('admin.login')
                           ->with('error', 'حسابك غير نشط أو مقفل مؤقتاً');
        }

        return $next($request);
    }
}
