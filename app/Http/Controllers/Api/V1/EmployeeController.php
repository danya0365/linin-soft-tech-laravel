<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $query = Employee::query()->with('department:id,name,var_name')->orderBy('id');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->query('q'));
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', '%' . $q . '%')
                    ->orWhere('code', 'like', '%' . $q . '%');
            });
        }

        return $this->paginated($request, $query);
    }

    public function show(int $id): JsonResponse
    {
        $employee = Employee::with('department:id,name,var_name')->find($id);

        if (!$employee) {
            return $this->notFound('Employee not found.');
        }

        return $this->ok($employee->toArray());
    }
}
