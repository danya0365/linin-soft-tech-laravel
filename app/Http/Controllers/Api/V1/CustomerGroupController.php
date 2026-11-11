<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        return $this->paginated(
            $request,
            CustomerGroup::query()->orderBy('id')
        );
    }

    public function show(int $id): JsonResponse
    {
        $group = CustomerGroup::query()->find($id);

        if (!$group) {
            return $this->notFound('Customer group not found.');
        }

        return $this->ok($group->toArray());
    }
}
