<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\LinenType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinenTypeController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        return $this->paginated(
            $request,
            LinenType::query()->orderBy('id')
        );
    }

    public function show(int $id): JsonResponse
    {
        $type = LinenType::query()->find($id);

        if (!$type) {
            return $this->notFound('Linen type not found.');
        }

        return $this->ok($type->toArray());
    }
}
