<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class EnsureTenantDataIsolation
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
            return $this->unauthorizedResponse($request);
        }

        $user = Auth::guard('tenant')->user();
        $tenant = $user->tenant;

        // التحقق من صحة المستأجر
        if (!$tenant || !$tenant->isLicenseValid()) {
            return $this->forbiddenResponse($request, 'انتهت صلاحية الترخيص أو المستأجر غير نشط');
        }

        // تعيين المستأجر الحالي في التطبيق
        app()->instance('current_tenant', $tenant);
        
        // إضافة معلومات المستأجر للطلب
        $request->merge([
            'current_tenant_id' => $tenant->id,
            'current_tenant' => $tenant
        ]);

        // تعيين headers للاستجابة
        $response = $next($request);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response->header('X-Tenant-ID', $tenant->id);
            $response->header('X-Tenant-Code', $tenant->tenant_code);
        }

        // تسجيل النشاط
        $this->logTenantActivity($tenant, $request);

        return $response;
    }

    /**
     * استجابة غير مصرح
     */
    private function unauthorizedResponse(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'يجب تسجيل الدخول للوصول إلى هذا المورد'
            ], 401);
        }

        return redirect()->route('tenant.login');
    }

    /**
     * استجابة محظورة
     */
    private function forbiddenResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Forbidden',
                'error' => $message
            ], 403);
        }

        return redirect()->route('tenant.login')->with('error', $message);
    }

    /**
     * تسجيل نشاط المستأجر
     */
    private function logTenantActivity(Tenant $tenant, Request $request): void
    {
        // تحديث آخر نشاط للمستأجر
        $tenant->update(['last_activity_at' => now()]);

        // تسجيل النشاط في حالة العمليات المهمة
        $importantActions = ['POST', 'PUT', 'PATCH', 'DELETE'];
        
        if (in_array($request->method(), $importantActions)) {
            \Log::info('Tenant Activity', [
                'tenant_id' => $tenant->id,
                'tenant_code' => $tenant->tenant_code,
                'user_id' => auth('tenant')->id(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
            ]);
        }
    }
}
