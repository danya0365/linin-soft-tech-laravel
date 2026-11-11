<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait RespondsWithJson
{
    protected function ok(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message,
            'code' => 404,
        ], 404);
    }

    /**
     * Paginate a query and return a stable list envelope.
     *
     * @param  callable|null  $mapItem  fn(Model): array
     */
    protected function paginated(Request $request, Builder $query, ?callable $mapItem = null): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        /** @var LengthAwarePaginator $page */
        $page = $query->paginate($perPage)->appends($request->query());

        $items = $page->getCollection()->map(function ($item) use ($mapItem) {
            if ($mapItem) {
                return $mapItem($item);
            }

            return $item instanceof Model ? $item->toArray() : $item;
        })->values();

        return $this->ok([
            'items' => $items,
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }
}
