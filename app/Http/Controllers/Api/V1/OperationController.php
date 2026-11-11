<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Operation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Read API: plant operations / jobs
 */
class OperationController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:in-progress,close'],
            'operation_type' => ['nullable', 'string'],
            'customer_id' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Operation::query()
            ->with([
                'customer:id,name,customer_group_id',
                'washingMachine:id,name,service_status',
                'dryerMachine:id,name,service_status',
                'truck:id,name,plate_number,service_status',
            ])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('operation_type')) {
            $query->where('operation_type', $request->query('operation_type'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->query('customer_id'));
        }

        return $this->paginated($request, $query);
    }

    public function show(int $id): JsonResponse
    {
        $operation = Operation::query()
            ->with([
                'customer:id,name,customer_group_id',
                'employee:id,code,name,department_id',
                'washEmployee:id,code,name',
                'dryEmployee:id,code,name',
                'ironEmployee:id,code,name',
                'packingEmployee:id,code,name',
                'collectEmployee:id,code,name',
                'deliverEmployee:id,code,name',
                'washingMachine:id,name,service_status,operation_id',
                'dryerMachine:id,name,service_status',
                'truck:id,name,plate_number,service_status',
                'linenProducts:id,name,linen_type_id',
            ])
            ->find($id);

        if (!$operation) {
            return $this->notFound('Operation not found.');
        }

        return $this->ok($operation->toArray());
    }
}
