<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\LinenProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinenProductController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $query = LinenProduct::query()->with('linenType:id,name')->orderBy('id');

        if ($request->filled('linen_type_id')) {
            $query->where('linen_type_id', $request->query('linen_type_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->query('q'));
            $query->where('name', 'like', '%' . $q . '%');
        }

        return $this->paginated($request, $query);
    }

    public function show(int $id): JsonResponse
    {
        $product = LinenProduct::with('linenType:id,name')->find($id);

        if (!$product) {
            return $this->notFound('Linen product not found.');
        }

        return $this->ok($product->toArray());
    }
}
