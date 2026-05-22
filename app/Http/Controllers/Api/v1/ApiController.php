<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

abstract class ApiController extends Controller
{
    protected function success(mixed $data, int $status = 200, array $meta = []): JsonResponse
    {
        $payload = ['data' => $data];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function error(string $code, string $message, int $status = 400, array $details = []): JsonResponse
    {
        $payload = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if (! empty($details)) {
            $payload['error']['details'] = $details;
        }

        return response()->json($payload, $status);
    }

    protected function errorFromException(Throwable $e, int $status = 500): JsonResponse
    {
        return $this->error(
            code: 'internal_error',
            message: $e->getMessage(),
            status: $status,
        );
    }

    /**
     * Apply filter[field]=value query parameters to a builder.
     *
     * Boolean fields in $booleanFields are cast to int comparison.
     * All other filters do LIKE '%value%' for strings listed in $searchFields,
     * or exact match otherwise.
     *
     * @param  string[]  $searchFields  Fields that use LIKE matching.
     * @param  string[]  $booleanFields  Fields that are boolean (0/1 cast).
     * @param  string[]  $exactFields  Fields that use exact match (non-boolean, non-search).
     */
    protected function applyFilters(
        Builder $query,
        Request $request,
        array $searchFields = [],
        array $booleanFields = [],
        array $exactFields = [],
    ): Builder {
        $filters = $request->query('filter', []);

        if (! is_array($filters)) {
            return $query;
        }

        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (in_array($field, $booleanFields, true)) {
                $query->where($field, filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0);
            } elseif (in_array($field, $searchFields, true)) {
                $query->where($field, 'like', "%{$value}%");
            } elseif (in_array($field, $exactFields, true)) {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Resolve ?include=rel1,rel2 into an array of allowed relationships.
     *
     * @param  string[]  $allowed
     * @return string[]
     */
    protected function resolveIncludes(Request $request, array $allowed): array
    {
        $requested = array_filter(
            explode(',', (string) $request->query('include', '')),
        );

        return array_values(array_intersect($requested, $allowed));
    }

    /**
     * Paginate and return a cursor-paginated resource collection.
     */
    protected function paginateCursor(
        Builder $query,
        Request $request,
        string $resourceClass,
        int $perPage = 20,
    ): JsonResponse {
        $perPage = min((int) $request->query('per_page', $perPage), 100);
        $paginated = $query->cursorPaginate($perPage)->withQueryString();

        return response()->json([
            'data' => $resourceClass::collection($paginated->items()),
            'meta' => [
                'per_page' => $paginated->perPage(),
                'next_cursor' => $paginated->nextCursor()?->encode(),
                'prev_cursor' => $paginated->previousCursor()?->encode(),
                'has_more' => $paginated->hasMorePages(),
            ],
            'links' => [
                'next' => $paginated->nextPageUrl(),
                'prev' => $paginated->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Convenience: resource collection from a cursor-paginated query.
     */
    protected function collectionResponse(
        Builder $query,
        Request $request,
        string $resourceClass,
        int $perPage = 20,
    ): JsonResponse {
        return $this->paginateCursor($query, $request, $resourceClass, $perPage);
    }
}
