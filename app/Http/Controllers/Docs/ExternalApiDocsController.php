<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Services\ExternalApiOpenApiBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Public External API documentation (no login required).
 * HTML guide + JSON manifest + OpenAPI/Swagger Try it out.
 */
class ExternalApiDocsController extends Controller
{
    public function show(): View
    {
        return view('docs.external-api', [
            'manifest' => $this->manifest(),
            'baseUrl' => rtrim(config('app.url'), '/'),
            'docsUrl' => route('docs.external-api'),
            'docsJsonUrl' => route('docs.external-api.json'),
            'swaggerUrl' => route('docs.external-api.swagger'),
            'openapiUrl' => route('docs.external-api.openapi'),
            'tokenAdminUrl' => url('/admin/api-tokens'),
            'permissionLevels' => config('api-tokens.permission_levels', []),
            'abilities' => config('api-tokens.abilities', []),
        ]);
    }

    public function swagger(): View
    {
        return view('docs.external-api-swagger');
    }

    public function manifestJson(): JsonResponse
    {
        return response()->json($this->manifest());
    }

    public function openApiJson(ExternalApiOpenApiBuilder $builder): JsonResponse
    {
        return response()->json($builder->build());
    }

    /**
     * @return array<string, mixed>
     */
    public function manifest(): array
    {
        $base = rtrim((string) config('app.url'), '/');

        return [
            'api_name' => 'LinenSoftTech External API',
            'version' => 'v1',
            'base_url' => $base,
            'docs_html_url' => route('docs.external-api'),
            'docs_json_url' => route('docs.external-api.json'),
            'docs_swagger_url' => route('docs.external-api.swagger'),
            'docs_openapi_url' => route('docs.external-api.openapi'),
            'auth' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'token_format' => '{id}|{plainText}',
                'header' => 'Authorization: Bearer <token>',
                'abilities' => config('api-tokens.abilities', []),
                'how_to_get_token' => [
                    'ui_path' => '/admin/api-tokens/create',
                    'list_path' => '/admin/api-tokens',
                    'role_required' => 'admin (is_can_access_admin)',
                    'self_service_api' => false,
                    'steps' => [
                        'Login as admin in LinenSoftTech web app',
                        'Open /admin/api-tokens → Create Token',
                        'Name the token (e.g. Partner AI)',
                        'Choose permission level: read (default) | read_write | full',
                        'Copy plainTextToken shown ONCE',
                        'Store in env of the separate project; send as Bearer',
                    ],
                    'permission_levels' => collect(config('api-tokens.permission_levels', []))
                        ->mapWithKeys(fn ($level, $key) => [$key => $level['abilities'] ?? []])
                        ->all(),
                    'note_existing_tokens' => 'Tokens created before resources:read was added may only have dashboard:read — recreate Read only token to access resource tables',
                ],
            ],
            'list_envelope' => [
                'success' => true,
                'data' => [
                    'items' => [],
                    'meta' => [
                        'current_page' => 1,
                        'per_page' => 20,
                        'total' => 0,
                        'last_page' => 1,
                    ],
                ],
            ],
            'dashboard_endpoints' => [
                ['method' => 'GET', 'path' => '/api/v1/dashboard', 'ability' => 'dashboard:read', 'query' => ['date_start_at', 'date_end_at', 'start_at', 'end_at']],
                ['method' => 'GET', 'path' => '/api/v1/dashboard/operations', 'ability' => 'dashboard:read', 'query' => ['start_at', 'end_at']],
                ['method' => 'GET', 'path' => '/api/v1/dashboard/sales', 'ability' => 'dashboard:read', 'query' => ['start_at', 'end_at']],
                ['method' => 'GET', 'path' => '/api/v1/dashboard/energy', 'ability' => 'dashboard:read', 'query' => ['date_start_at', 'date_end_at', 'start_at', 'end_at']],
                ['method' => 'GET', 'path' => '/api/v1/dashboard/income', 'ability' => 'dashboard:read', 'query' => ['start_at', 'end_at']],
                ['method' => 'GET', 'path' => '/api/v1/dashboard/expense', 'ability' => 'dashboard:read', 'query' => ['start_at', 'end_at']],
            ],
            'resource_endpoints' => [
                ['method' => 'GET', 'path' => '/api/v1/washing-machines', 'ability' => 'resources:read', 'query' => ['service_status', 'in_use', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/washing-machines/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/dryer-machines', 'ability' => 'resources:read', 'query' => ['service_status', 'in_use', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/dryer-machines/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/trucks', 'ability' => 'resources:read', 'query' => ['service_status', 'in_use', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/trucks/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/customers', 'ability' => 'resources:read', 'query' => ['customer_group_id', 'q', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/customers/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/customer-groups', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/customer-groups/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/employees', 'ability' => 'resources:read', 'query' => ['department_id', 'q', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/employees/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/departments', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/departments/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/linen-types', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/linen-types/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/linen-products', 'ability' => 'resources:read', 'query' => ['linen_type_id', 'q', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/linen-products/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/operations', 'ability' => 'resources:read', 'query' => ['status', 'operation_type', 'customer_id', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/operations/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/energy-resources', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/energy-resources/{id}', 'ability' => 'resources:read'],
                ['method' => 'GET', 'path' => '/api/v1/inventories', 'ability' => 'resources:read', 'query' => ['inventory_group_id', 'q', 'per_page', 'page']],
                ['method' => 'GET', 'path' => '/api/v1/inventories/{id}', 'ability' => 'resources:read'],
            ],
            'machine_status' => [
                'service_status' => ['available', 'broken'],
                'operation_id' => 'non-null means currently assigned to a job',
                'status_text' => ['พร้อมใช้งาน', 'กำลังใช้งาน', 'เสีย/ซ่อม'],
            ],
            'errors' => [
                '401' => 'Missing/invalid/revoked token or session-only auth',
                '403' => 'Token missing required ability',
                '404' => 'Resource id not found',
                '422' => 'Invalid query params',
            ],
        ];
    }
}
