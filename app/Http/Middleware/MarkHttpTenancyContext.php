<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marks the active request as an HTTP tenancy context so TenantScope can
 * fail-closed outside of console/queue execution.
 */
class MarkHttpTenancyContext
{
    public const ATTRIBUTE = 'tenancy.http';

    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set(self::ATTRIBUTE, true);

        return $next($request);
    }
}
