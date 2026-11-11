<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Truck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $query = Truck::query()->orderBy('id');

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
        $truck = Truck::query()->find($id);

        if (!$truck) {
            return $this->notFound('Truck not found.');
        }

        return $this->ok($truck->toArray());
    }
}
