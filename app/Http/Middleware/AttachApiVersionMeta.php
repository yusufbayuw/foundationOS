<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachApiVersionMeta
{
    public function handle(Request $request, Closure $next, string $version = 'v2'): Response
    {
        $response = $next($request);

        if (! $response instanceof JsonResponse) {
            return $response;
        }

        $data = $response->getData(true);

        if (! is_array($data)) {
            return $response;
        }

        $meta = $data['meta'] ?? [];

        if (! is_array($meta)) {
            $meta = [];
        }

        $meta['api_version'] = $version;
        $data['meta'] = $meta;

        $response->setData($data);

        return $response;
    }
}
