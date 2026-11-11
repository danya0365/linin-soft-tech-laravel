<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\EnergyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnergyResourceController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        return $this->paginated(
            $request,
            EnergyResource::query()->orderBy('id')
        );
    }

    public function show(int $id): JsonResponse
    {
        $resource = EnergyResource::query()->find($id);

        if (!$resource) {
            return $this->notFound('Energy resource not found.');
        }

        return $this->ok($resource->toArray());
    }
}
