<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleCsrfErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // إذا كان الطلب AJAX أو يتوقع JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'CSRF token mismatch',
                    'message' => 'انتهت صلاحية الجلسة، يرجى تحديث الصفحة',
                    'redirect' => url()->current(),
                    'new_token' => csrf_token()
                ], 419);
            }
            
            // للطلبات العادية، إعادة توجيه مع رسالة خطأ
            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->withErrors([
                    'csrf' => 'انتهت صلاحية الجلسة، يرجى المحاولة مرة أخرى'
                ])
                ->with('error', 'انتهت صلاحية الجلسة، يرجى المحاولة مرة أخرى');
        }
    }
}
