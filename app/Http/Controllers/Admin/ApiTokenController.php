<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * หลังบ้าน: จัดการ Sanctum personal access tokens สำหรับ External API
 *
 * - index: รายการ token ของ admin ที่ล็อกอิน
 * - create / store: สร้าง token + แสดง plain text ครั้งเดียว
 * - destroy: revoke
 */
class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = Auth::user()
            ->tokens()
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.api-tokens.index', compact('tokens'))
            ->with('i', (request()->input('page', 1) - 1) * $tokens->perPage());
    }

    public function create()
    {
        $permissionLevels = config('api-tokens.permission_levels');
        $defaultLevel = config('api-tokens.default_permission_level', 'read');

        return view('admin.api-tokens.create', compact('permissionLevels', 'defaultLevel'));
    }

    public function store(Request $request)
    {
        $levels = array_keys(config('api-tokens.permission_levels', []));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permission_level' => ['required', 'string', 'in:' . implode(',', $levels)],
        ]);

        $level = config('api-tokens.permission_levels.' . $validated['permission_level']);
        $abilities = $level['abilities'] ?? ['dashboard:read'];

        $newToken = Auth::user()->createToken($validated['name'], $abilities);

        return view('admin.api-tokens.show', [
            'token' => $newToken->accessToken,
            'plainTextToken' => $newToken->plainTextToken,
            'permissionLevel' => $validated['permission_level'],
            'permissionLabel' => $level['label'] ?? $validated['permission_level'],
        ]);
    }

    public function destroy(int $id)
    {
        /** @var PersonalAccessToken|null $token */
        $token = Auth::user()->tokens()->where('id', $id)->first();

        if (!$token) {
            return redirect()
                ->route('admin.api-tokens.index')
                ->with('error', 'ไม่พบ token หรือไม่มีสิทธิ์เพิกถอน');
        }

        $token->delete();

        return redirect()
            ->route('admin.api-tokens.index')
            ->with('success', 'เพิกถอน token สำเร็จ');
    }
}
