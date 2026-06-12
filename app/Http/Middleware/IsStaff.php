<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * อนุญาตเฉพาะ supervisor / manager / admin (อย่างใดอย่างหนึ่ง)
 * ใช้กับฟีเจอร์ที่เข้าถึงข้อมูลธุรกิจ เช่น AI Chat
 */
class IsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::user();

        if ($user && ($user->is_can_access_supervisor || $user->is_can_access_manager || $user->is_can_access_admin)) {
            return $next($request);
        }

        // For API/AJAX requests, return JSON response instead of redirect
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'error' => $user ? 'You have no staff access' : 'Unauthenticated. Please login again.',
                'code' => $user ? 403 : 401,
            ], $user ? 403 : 401);
        }

        return redirect('home')->with('error', 'You have no staff access');
    }
}
