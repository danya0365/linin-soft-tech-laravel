<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        return $this->paginated(
            $request,
            Department::query()->orderBy('id')
        );
    }

    public function show(int $id): JsonResponse
    {
        $department = Department::query()->find($id);

        if (!$department) {
            return $this->notFound('Department not found.');
        }

        return $this->ok($department->toArray());
    }
}
