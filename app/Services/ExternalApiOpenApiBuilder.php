<?php

namespace App\Services;

/**
 * Build OpenAPI 3.0 document for External API (Swagger UI / Try it out).
 */
class ExternalApiOpenApiBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $base = rtrim((string) config('app.url'), '/');

        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'LinenSoftTech External API',
                'version' => 'v1',
                'description' => $this->descriptionMarkdown(),
            ],
            'servers' => [
                ['url' => $base, 'description' => 'Application base URL (APP_URL)'],
            ],
            'tags' => [
                ['name' => 'Dashboard', 'description' => 'สรุปรายงาน (ability: dashboard:read)'],
                ['name' => 'Machines', 'description' => 'เครื่องซัก / อบ / รถ (ability: resources:read)'],
                ['name' => 'Customers', 'description' => 'ลูกค้า / กลุ่มลูกค้า'],
                ['name' => 'Workforce', 'description' => 'พนักงาน / แผนก'],
                ['name' => 'Linen', 'description' => 'ชนิดผ้า / ผ้า'],
                ['name' => 'Operations', 'description' => 'รายการงาน'],
                ['name' => 'Stock & Energy', 'description' => 'สต็อก / พลังงาน'],
            ],
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'Sanctum personal access token ({id}|{plainText})',
                        'description' => 'วาง plainTextToken จากหน้า Admin → API Tokens (ไม่สร้าง token จาก Swagger)',
                    ],
                ],
                'schemas' => [
                    'SuccessEnvelope' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => true],
                            'data' => ['description' => 'Payload ตาม endpoint'],
                        ],
                    ],
                    'PaginatedList' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => true],
                            'data' => [
                                'type' => 'object',
                                'properties' => [
                                    'items' => ['type' => 'array', 'items' => ['type' => 'object']],
                                    'meta' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'current_page' => ['type' => 'integer'],
                                            'per_page' => ['type' => 'integer'],
                                            'total' => ['type' => 'integer'],
                                            'last_page' => ['type' => 'integer'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'ErrorBody' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => false],
                            'error' => ['type' => 'string'],
                            'code' => ['type' => 'integer'],
                            'message' => ['type' => 'string'],
                        ],
                    ],
                ],
                'parameters' => [
                    'Page' => [
                        'name' => 'page',
                        'in' => 'query',
                        'schema' => ['type' => 'integer', 'minimum' => 1, 'default' => 1],
                    ],
                    'PerPage' => [
                        'name' => 'per_page',
                        'in' => 'query',
                        'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20],
                    ],
                    'StartAt' => [
                        'name' => 'start_at',
                        'in' => 'query',
                        'schema' => ['type' => 'string', 'format' => 'date', 'example' => '2026-11-01'],
                    ],
                    'EndAt' => [
                        'name' => 'end_at',
                        'in' => 'query',
                        'schema' => ['type' => 'string', 'format' => 'date', 'example' => '2026-11-11'],
                    ],
                    'DateStartAt' => [
                        'name' => 'date_start_at',
                        'in' => 'query',
                        'schema' => ['type' => 'string', 'format' => 'date'],
                    ],
                    'DateEndAt' => [
                        'name' => 'date_end_at',
                        'in' => 'query',
                        'schema' => ['type' => 'string', 'format' => 'date'],
                    ],
                    'ServiceStatus' => [
                        'name' => 'service_status',
                        'in' => 'query',
                        'schema' => ['type' => 'string', 'enum' => ['available', 'broken']],
                    ],
                    'InUse' => [
                        'name' => 'in_use',
                        'in' => 'query',
                        'description' => '1 = มี operation_id, 0 = ว่าง',
                        'schema' => ['type' => 'string', 'enum' => ['0', '1']],
                    ],
                    'SearchQ' => [
                        'name' => 'q',
                        'in' => 'query',
                        'schema' => ['type' => 'string'],
                    ],
                    'IdPath' => [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer'],
                    ],
                ],
                'responses' => [
                    'Unauthorized' => [
                        'description' => 'Missing/invalid token',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ErrorBody'],
                            ],
                        ],
                    ],
                    'Forbidden' => [
                        'description' => 'Token missing required ability',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ErrorBody'],
                            ],
                        ],
                    ],
                    'NotFound' => [
                        'description' => 'Resource not found',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ErrorBody'],
                            ],
                        ],
                    ],
                ],
            ],
            'security' => [
                ['BearerAuth' => []],
            ],
            'paths' => $this->paths(),
        ];
    }

    private function descriptionMarkdown(): string
    {
        $admin = url('/admin/api-tokens');
        $guide = route('docs.external-api');

        return <<<MD
## วิธีใช้งาน Try it out

1. สร้าง token ที่ [Admin → API Tokens]({$admin}) เลือกระดับ **Read only**
2. คัดลอก plainTextToken (แสดงครั้งเดียว) รูปแบบ `{id}|{secret}`
3. กดปุ่ม **Authorize** ในหน้านี้ → วาง token → Authorize
4. กด **Try it out** บน endpoint ที่ต้องการ → Execute

## ทำไมไม่มีปุ่ม “ขอ token” ใน Swagger?

- Token คือ secret ระดับระบบ — ถ้ามี endpoint สาธารณะออก token ได้ ใครก็เรียก API ได้
- แนวทางที่ถูกต้อง: admin ออก token → วางใน Authorize (มาตรฐาน OAuth/Swagger)
- ไม่จำเป็นต้องมี “login แลก token” สำหรับ client ภายนอกใน v1

คู่มือละเอียด: {$guide}
MD;
    }

    /**
     * @return array<string, mixed>
     */
    private function paths(): array
    {
        $paths = [];

        // Dashboard
        $paths['/api/v1/dashboard'] = [
            'get' => $this->op(
                'Dashboard',
                'dashboardOverview',
                'Overview: overall + operation stats',
                'dashboard:read',
                ['DateStartAt', 'DateEndAt', 'StartAt', 'EndAt']
            ),
        ];
        $paths['/api/v1/dashboard/operations'] = [
            'get' => $this->op('Dashboard', 'dashboardOperations', 'Operation stats summary', 'dashboard:read', ['StartAt', 'EndAt']),
        ];
        $paths['/api/v1/dashboard/sales'] = [
            'get' => $this->op('Dashboard', 'dashboardSales', 'Sales year + latest days', 'dashboard:read', ['StartAt', 'EndAt']),
        ];
        $paths['/api/v1/dashboard/energy'] = [
            'get' => $this->op('Dashboard', 'dashboardEnergy', 'Energy summary + days', 'dashboard:read', ['DateStartAt', 'DateEndAt', 'StartAt', 'EndAt']),
        ];
        $paths['/api/v1/dashboard/income'] = [
            'get' => $this->op('Dashboard', 'dashboardIncome', 'Income daily series', 'dashboard:read', ['StartAt', 'EndAt']),
        ];
        $paths['/api/v1/dashboard/expense'] = [
            'get' => $this->op('Dashboard', 'dashboardExpense', 'Expense daily series', 'dashboard:read', ['StartAt', 'EndAt']),
        ];

        // Machines
        foreach (['washing-machines' => 'Washing', 'dryer-machines' => 'Dryer', 'trucks' => 'Truck'] as $slug => $label) {
            $paths['/api/v1/' . $slug] = [
                'get' => $this->listOp('Machines', $slug . 'List', "List {$label}", ['ServiceStatus', 'InUse', 'Page', 'PerPage']),
            ];
            $paths['/api/v1/' . $slug . '/{id}'] = [
                'get' => $this->showOp('Machines', $slug . 'Show', "Show {$label}"),
            ];
        }

        // Customers
        $paths['/api/v1/customers'] = [
            'get' => array_merge(
                $this->listOp('Customers', 'customersList', 'List customers', ['SearchQ', 'Page', 'PerPage']),
                [
                    'parameters' => array_merge(
                        $this->paramRefs(['SearchQ', 'Page', 'PerPage']),
                        [[
                            'name' => 'customer_group_id',
                            'in' => 'query',
                            'schema' => ['type' => 'integer'],
                        ]]
                    ),
                ]
            ),
        ];
        $paths['/api/v1/customers/{id}'] = [
            'get' => $this->showOp('Customers', 'customersShow', 'Show customer'),
        ];
        $paths['/api/v1/customer-groups'] = [
            'get' => $this->listOp('Customers', 'customerGroupsList', 'List customer groups', ['Page', 'PerPage']),
        ];
        $paths['/api/v1/customer-groups/{id}'] = [
            'get' => $this->showOp('Customers', 'customerGroupsShow', 'Show customer group'),
        ];

        // Workforce
        $paths['/api/v1/employees'] = [
            'get' => array_merge(
                $this->listOp('Workforce', 'employeesList', 'List employees (password hidden)', ['SearchQ', 'Page', 'PerPage']),
                [
                    'parameters' => array_merge(
                        $this->paramRefs(['SearchQ', 'Page', 'PerPage']),
                        [[
                            'name' => 'department_id',
                            'in' => 'query',
                            'schema' => ['type' => 'integer'],
                        ]]
                    ),
                ]
            ),
        ];
        $paths['/api/v1/employees/{id}'] = [
            'get' => $this->showOp('Workforce', 'employeesShow', 'Show employee'),
        ];
        $paths['/api/v1/departments'] = [
            'get' => $this->listOp('Workforce', 'departmentsList', 'List departments', ['Page', 'PerPage']),
        ];
        $paths['/api/v1/departments/{id}'] = [
            'get' => $this->showOp('Workforce', 'departmentsShow', 'Show department'),
        ];

        // Linen
        $paths['/api/v1/linen-types'] = [
            'get' => $this->listOp('Linen', 'linenTypesList', 'List linen types', ['Page', 'PerPage']),
        ];
        $paths['/api/v1/linen-types/{id}'] = [
            'get' => $this->showOp('Linen', 'linenTypesShow', 'Show linen type'),
        ];
        $paths['/api/v1/linen-products'] = [
            'get' => array_merge(
                $this->listOp('Linen', 'linenProductsList', 'List linen products', ['SearchQ', 'Page', 'PerPage']),
                [
                    'parameters' => array_merge(
                        $this->paramRefs(['SearchQ', 'Page', 'PerPage']),
                        [[
                            'name' => 'linen_type_id',
                            'in' => 'query',
                            'schema' => ['type' => 'integer'],
                        ]]
                    ),
                ]
            ),
        ];
        $paths['/api/v1/linen-products/{id}'] = [
            'get' => $this->showOp('Linen', 'linenProductsShow', 'Show linen product'),
        ];

        // Operations
        $paths['/api/v1/operations'] = [
            'get' => [
                'tags' => ['Operations'],
                'operationId' => 'operationsList',
                'summary' => 'List operations / jobs',
                'description' => 'ability: resources:read — คนละเส้นกับ /api/v1/dashboard/operations',
                'parameters' => [
                    [
                        'name' => 'status',
                        'in' => 'query',
                        'schema' => ['type' => 'string', 'enum' => ['in-progress', 'close']],
                    ],
                    [
                        'name' => 'operation_type',
                        'in' => 'query',
                        'schema' => [
                            'type' => 'string',
                            'enum' => ['wash', 'dry', 'iron', 'packing', 'collect', 'deliver', 'payment'],
                        ],
                    ],
                    [
                        'name' => 'customer_id',
                        'in' => 'query',
                        'schema' => ['type' => 'integer'],
                    ],
                    ['$ref' => '#/components/parameters/Page'],
                    ['$ref' => '#/components/parameters/PerPage'],
                ],
                'responses' => $this->listResponses(),
            ],
        ];
        $paths['/api/v1/operations/{id}'] = [
            'get' => $this->showOp('Operations', 'operationsShow', 'Show operation with relations'),
        ];

        // Stock & energy
        $paths['/api/v1/energy-resources'] = [
            'get' => $this->listOp('Stock & Energy', 'energyResourcesList', 'List energy resources', ['Page', 'PerPage']),
        ];
        $paths['/api/v1/energy-resources/{id}'] = [
            'get' => $this->showOp('Stock & Energy', 'energyResourcesShow', 'Show energy resource'),
        ];
        $paths['/api/v1/inventories'] = [
            'get' => array_merge(
                $this->listOp('Stock & Energy', 'inventoriesList', 'List inventories', ['SearchQ', 'Page', 'PerPage']),
                [
                    'parameters' => array_merge(
                        $this->paramRefs(['SearchQ', 'Page', 'PerPage']),
                        [[
                            'name' => 'inventory_group_id',
                            'in' => 'query',
                            'schema' => ['type' => 'integer'],
                        ]]
                    ),
                ]
            ),
        ];
        $paths['/api/v1/inventories/{id}'] = [
            'get' => $this->showOp('Stock & Energy', 'inventoriesShow', 'Show inventory item'),
        ];

        return $paths;
    }

    /**
     * @param  array<int, string>  $paramNames
     * @return array<string, mixed>
     */
    private function op(string $tag, string $operationId, string $summary, string $ability, array $paramNames): array
    {
        return [
            'tags' => [$tag],
            'operationId' => $operationId,
            'summary' => $summary,
            'description' => 'Required ability: `' . $ability . '`',
            'parameters' => $this->paramRefs($paramNames),
            'responses' => [
                '200' => [
                    'description' => 'OK',
                    'content' => [
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/SuccessEnvelope'],
                        ],
                    ],
                ],
                '401' => ['$ref' => '#/components/responses/Unauthorized'],
                '403' => ['$ref' => '#/components/responses/Forbidden'],
                '422' => ['description' => 'Validation error'],
            ],
        ];
    }

    /**
     * @param  array<int, string>  $paramNames
     * @return array<string, mixed>
     */
    private function listOp(string $tag, string $operationId, string $summary, array $paramNames): array
    {
        return [
            'tags' => [$tag],
            'operationId' => $operationId,
            'summary' => $summary,
            'description' => 'Required ability: `resources:read`',
            'parameters' => $this->paramRefs($paramNames),
            'responses' => $this->listResponses(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function showOp(string $tag, string $operationId, string $summary): array
    {
        return [
            'tags' => [$tag],
            'operationId' => $operationId,
            'summary' => $summary,
            'description' => 'Required ability: `resources:read`',
            'parameters' => [
                ['$ref' => '#/components/parameters/IdPath'],
            ],
            'responses' => [
                '200' => [
                    'description' => 'OK',
                    'content' => [
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/SuccessEnvelope'],
                        ],
                    ],
                ],
                '401' => ['$ref' => '#/components/responses/Unauthorized'],
                '403' => ['$ref' => '#/components/responses/Forbidden'],
                '404' => ['$ref' => '#/components/responses/NotFound'],
            ],
        ];
    }

    /**
     * @param  array<int, string>  $names
     * @return array<int, array<string, string>>
     */
    private function paramRefs(array $names): array
    {
        return array_map(fn ($name) => ['$ref' => '#/components/parameters/' . $name], $names);
    }

    /**
     * @return array<string, mixed>
     */
    private function listResponses(): array
    {
        return [
            '200' => [
                'description' => 'OK paginated list',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/PaginatedList'],
                    ],
                ],
            ],
            '401' => ['$ref' => '#/components/responses/Unauthorized'],
            '403' => ['$ref' => '#/components/responses/Forbidden'],
        ];
    }
}
