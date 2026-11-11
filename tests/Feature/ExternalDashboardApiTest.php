<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * External Dashboard API — Sanctum Bearer + abilities
 *
 * ใช้ DatabaseTransactions (ห้าม RefreshDatabase) เพราะ phpunit ชี้ MySQL จริง
 * รันผ่าน Sail: ./vendor/bin/sail test --filter=ExternalDashboardApiTest
 */
class ExternalDashboardApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function adminUser(): User
    {
        return User::factory()->create([
            'is_can_access_admin' => true,
            'is_can_access_manager' => false,
            'is_can_access_supervisor' => false,
            'is_can_access_worker' => false,
        ]);
    }

    public function test_guest_gets_401(): void
    {
        $this->getJson('/api/v1/dashboard')->assertStatus(401);
    }

    public function test_session_user_without_token_gets_401(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->getJson('/api/v1/dashboard')
            ->assertStatus(401)
            ->assertJsonPath('error', 'API token required.');
    }

    public function test_token_without_dashboard_read_gets_403(): void
    {
        $user = $this->adminUser();

        Sanctum::actingAs($user, ['dashboard:write']);

        $this->getJson('/api/v1/dashboard')
            ->assertStatus(403)
            ->assertJsonPath('code', 403);
    }

    public function test_read_token_can_access_dashboard_overview(): void
    {
        $user = $this->adminUser();

        Sanctum::actingAs($user, ['dashboard:read']);

        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'overall',
                    'operations',
                ],
            ]);
    }

    public function test_full_control_token_can_access_all_dashboard_endpoints(): void
    {
        $user = $this->adminUser();

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/v1/dashboard')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/dashboard/operations')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/dashboard/sales')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/dashboard/energy')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/dashboard/income')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/dashboard/expense')->assertOk()->assertJsonPath('success', true);
    }

    public function test_invalid_date_query_returns_422(): void
    {
        $user = $this->adminUser();

        Sanctum::actingAs($user, ['dashboard:read']);

        $this->getJson('/api/v1/dashboard/operations?start_at=not-a-date')
            ->assertStatus(422);
    }

    /**
     * E2E: สร้าง personal access token จริง → เรียก API ด้วย Bearer header → revoke → 401
     */
    public function test_e2e_real_bearer_token_round_trip(): void
    {
        $user = $this->adminUser();
        $newToken = $user->createToken('E2E Mobile Dashboard', ['dashboard:read']);
        $plain = $newToken->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $plain,
            'Accept' => 'application/json',
        ];

        $this->withHeaders($headers)
            ->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => ['overall', 'operations'],
            ]);

        $this->withHeaders($headers)
            ->getJson('/api/v1/dashboard/operations')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeaders($headers)
            ->getJson('/api/v1/dashboard/sales')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeaders($headers)
            ->getJson('/api/v1/dashboard/energy')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeaders($headers)
            ->getJson('/api/v1/dashboard/income')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeaders($headers)
            ->getJson('/api/v1/dashboard/expense')
            ->assertOk()
            ->assertJsonPath('success', true);

        // revoke แล้วต้องเข้าไม่ได้ (forgetGuards — Auth แคช user ข้าม request ใน feature test)
        $tokenId = $newToken->accessToken->id;
        $user->tokens()->where('id', $tokenId)->delete();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);

        $this->app['auth']->forgetGuards();

        $this->withHeaders($headers)
            ->getJson('/api/v1/dashboard')
            ->assertStatus(401);
    }

    /**
     * E2E Admin UI: สร้าง token (read) ผ่านหน้า admin → ใช้ Bearer ยิง API → revoke ผ่าน UI
     */
    public function test_e2e_admin_ui_create_token_then_call_api_then_revoke(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->get(route('admin.api-tokens.index'))
            ->assertOk();

        $create = $this->actingAs($user)
            ->post(route('admin.api-tokens.store'), [
                'name' => 'E2E Admin UI Token',
                'permission_level' => 'read',
            ]);

        $create->assertOk();
        $create->assertSee('E2E Admin UI Token');
        $create->assertSee('Bearer Token');

        // ดึง plain text จากหน้า show (input#plain-token)
        $html = $create->getContent();
        $this->assertMatchesRegularExpression(
            '/id="plain-token"[^>]*value="([^"]+)"/',
            $html
        );
        preg_match('/id="plain-token"[^>]*value="([^"]+)"/', $html, $matches);
        $plain = $matches[1];
        $this->assertNotEmpty($plain);

        $tokenId = $user->tokens()->where('name', 'E2E Admin UI Token')->value('id');
        $this->assertNotNull($tokenId);

        // ออกจาก session แล้วใช้ Bearer อย่างเดียว
        $this->flushSession();
        $this->app['auth']->forgetGuards();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plain,
            'Accept' => 'application/json',
        ])
            ->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true);

        // revoke ผ่าน Admin UI
        $this->actingAs($user)
            ->delete(route('admin.api-tokens.destroy', $tokenId))
            ->assertRedirect(route('admin.api-tokens.index'));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);

        // เคลียร์ session + guard แล้วค่อยยิง Bearer เก่า — ต้องได้ 401 จริงจาก token ที่ถูก revoke
        $this->flushSession();
        $this->app['auth']->forgetGuards();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plain,
            'Accept' => 'application/json',
        ])
            ->getJson('/api/v1/dashboard')
            ->assertStatus(401);
    }

    public function test_full_permission_level_stores_star_ability(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->post(route('admin.api-tokens.store'), [
                'name' => 'Full Control Token',
                'permission_level' => 'full',
            ])
            ->assertOk();

        $token = $user->tokens()->where('name', 'Full Control Token')->first();
        $this->assertNotNull($token);
        $this->assertSame(['*'], $token->abilities);
    }
}
