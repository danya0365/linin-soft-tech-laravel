<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Public External API docs — ไม่ต้อง login
 */
class ExternalApiDocsTest extends TestCase
{
    public function test_html_docs_are_public(): void
    {
        $this->get('/docs/external-api')
            ->assertOk()
            ->assertSee('External API Spec', false)
            ->assertSee('api-manifest', false)
            ->assertSee('/api/v1/washing-machines', false)
            ->assertSee('dashboard:read', false)
            ->assertSee('resources:read', false);
    }

    public function test_json_manifest_is_public(): void
    {
        $this->getJson('/docs/external-api.json')
            ->assertOk()
            ->assertJsonPath('api_name', 'LinenSoftTech External API')
            ->assertJsonPath('version', 'v1')
            ->assertJsonStructure([
                'auth' => ['how_to_get_token' => ['permission_levels']],
                'dashboard_endpoints',
                'resource_endpoints',
                'docs_html_url',
                'docs_json_url',
            ]);
    }

    public function test_legacy_html_path_redirects(): void
    {
        $this->get('/docs/external-dashboard-api.html')
            ->assertRedirect('/docs/external-api');
    }

    public function test_openapi_spec_is_public(): void
    {
        $this->getJson('/docs/external-api/openapi.json')
            ->assertOk()
            ->assertJsonPath('openapi', '3.0.3')
            ->assertJsonPath('info.title', 'LinenSoftTech External API')
            ->assertJsonStructure([
                'paths',
                'components' => ['securitySchemes' => ['BearerAuth']],
            ]);
    }

    public function test_swagger_ui_is_public(): void
    {
        $this->get('/docs/external-api/swagger')
            ->assertOk()
            ->assertSee('swagger-ui', false)
            ->assertSee('Authorize', false)
            ->assertSee('/admin/api-tokens/create', false);
    }
}
