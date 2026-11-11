<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\DryerMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DryerMachineController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $query = DryerMachine::query()->orderBy('id');

        if ($request->filled('service_status')) {
            $query->where('service_status', $request->query('service_status'));
        }

        if ($request->query('in_use') === '1') {
            $query->whereNotNull('operation_id');
        } elseif ($request->query('in_use') === '0') {
            $query->whereNull('operation_id');
        }

        return $this->paginated($request, $query);
    }

    public function show(int $id): JsonResponse
    {
        $machine = DryerMachine::query()->find($id);

        if (!$machine) {
            return $this->notFound('Dryer machine not found.');
        }

        return $this->ok($machine->toArray());
    }
}
