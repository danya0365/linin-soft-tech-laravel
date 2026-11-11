<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $query = Customer::query()->with('customerGroup:id,name')->orderBy('id');

        if ($request->filled('customer_group_id')) {
            $query->where('customer_group_id', $request->query('customer_group_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->query('q'));
            $query->where('name', 'like', '%' . $q . '%');
        }

        return $this->paginated($request, $query);
    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::with('customerGroup:id,name')->find($id);

        if (!$customer) {
            return $this->notFound('Customer not found.');
        }

        return $this->ok($customer->toArray());
    }
}
