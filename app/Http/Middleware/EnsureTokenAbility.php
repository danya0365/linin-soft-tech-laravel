<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\TransientToken;

/**
 * ตรวจว่า Bearer token (Sanctum) มี ability ที่ระบุหรือไม่
 * ใช้กับ external API — คืน JSON 401/403 เสมอ (ไม่ redirect)
 *
 * Sanctum: token ที่ abilities เป็น ['*'] จะผ่านทุก ability
 *
 * หมายเหตุ: ถ้า login ผ่าน session, Sanctum ติด TransientToken ที่ can()=true ทุกอย่าง
 * — ต้องปฏิเสธ TransientToken เพื่อบังคับใช้ personal access token จริง
 */
class EnsureTokenAbility
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ability
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $ability)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthenticated.',
                'code' => 401,
            ], 401);
        }

        $token = $user->currentAccessToken();

        // ต้องเป็น personal access token — ไม่รับ session TransientToken
        if (!$token || $token instanceof TransientToken) {
            return response()->json([
                'success' => false,
                'error' => 'API token required.',
                'code' => 401,
            ], 401);
        }

        if (!$user->tokenCan($ability)) {
            return response()->json([
                'success' => false,
                'error' => 'Token missing required ability: ' . $ability,
                'code' => 403,
            ], 403);
        }

        return $next($request);
    }
}
