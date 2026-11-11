<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WashingMachine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * External Resource Read API — Sanctum Bearer + resources:read
 *
 * รัน: ./vendor/bin/sail test --filter=ExternalResourceApiTest
 */
class ExternalResourceApiTest extends TestCase
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

    public function test_guest_gets_401_on_washing_machines(): void
    {
        $this->getJson('/api/v1/washing-machines')->assertStatus(401);
    }

    public function test_dashboard_read_only_token_cannot_access_resources(): void
    {
        Sanctum::actingAs($this->adminUser(), ['dashboard:read']);

        $this->getJson('/api/v1/washing-machines')
            ->assertStatus(403)
            ->assertJsonPath('code', 403);
    }

    public function test_resources_read_token_can_list_and_show_washing_machines(): void
    {
        Sanctum::actingAs($this->adminUser(), ['resources:read']);

        $machine = WashingMachine::factory()->create([
            'name' => 'API Probe Washer',
            'photo' => 'probe.jpg',
            'maximum_weight' => 50,
            'service_status' => 'available',
            'operation_id' => null,
        ]);

        $this->getJson('/api/v1/washing-machines')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items',
                    'meta' => ['current_page', 'per_page', 'total', 'last_page'],
                ],
            ]);

        $this->getJson('/api/v1/washing-machines/' . $machine->id)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $machine->id)
            ->assertJsonPath('data.status_text', 'พร้อมใช้งาน');
    }

    public function test_full_control_token_can_access_core_resource_endpoints(): void
    {
        Sanctum::actingAs($this->adminUser(), ['*']);

        $paths = [
            '/api/v1/washing-machines',
            '/api/v1/dryer-machines',
            '/api/v1/trucks',
            '/api/v1/customers',
            '/api/v1/customer-groups',
            '/api/v1/employees',
            '/api/v1/departments',
            '/api/v1/linen-types',
            '/api/v1/linen-products',
            '/api/v1/operations',
            '/api/v1/energy-resources',
            '/api/v1/inventories',
        ];

        foreach ($paths as $path) {
            $this->getJson($path)
                ->assertOk()
                ->assertJsonPath('success', true);
        }
    }

    public function test_e2e_admin_read_token_includes_resources_ability_and_calls_api(): void
    {
        $user = $this->adminUser();

        $create = $this->actingAs($user)
            ->post(route('admin.api-tokens.store'), [
                'name' => 'E2E Resources Token',
                'permission_level' => 'read',
            ])
            ->assertOk();

        preg_match('/id="plain-token"[^>]*value="([^"]+)"/', $create->getContent(), $matches);
        $plain = $matches[1] ?? null;
        $this->assertNotEmpty($plain);

        $token = $user->tokens()->where('name', 'E2E Resources Token')->first();
        $this->assertNotNull($token);
        $this->assertEqualsCanonicalizing(
            ['dashboard:read', 'resources:read'],
            $token->abilities
        );

        $this->flushSession();
        $this->app['auth']->forgetGuards();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plain,
            'Accept' => 'application/json',
        ])
            ->getJson('/api/v1/washing-machines')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plain,
            'Accept' => 'application/json',
        ])
            ->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_show_missing_washing_machine_returns_404(): void
    {
        Sanctum::actingAs($this->adminUser(), ['resources:read']);

        $this->getJson('/api/v1/washing-machines/99999999')
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }
}
