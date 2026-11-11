<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $query = Inventory::query()->with('inventoryGroup:id,name')->orderBy('id');

        if ($request->filled('inventory_group_id')) {
            $query->where('inventory_group_id', $request->query('inventory_group_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->query('q'));
            $query->where('name', 'like', '%' . $q . '%');
        }

        return $this->paginated($request, $query);
    }

    public function show(int $id): JsonResponse
    {
        $item = Inventory::with('inventoryGroup:id,name')->find($id);

        if (!$item) {
            return $this->notFound('Inventory item not found.');
        }

        return $this->ok($item->toArray());
    }
}
