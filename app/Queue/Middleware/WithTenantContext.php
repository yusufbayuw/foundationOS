<?php

namespace App\Queue\Middleware;

use App\Support\CurrentTenant;
use Closure;

/**
 * Job middleware that binds CurrentTenant around the wrapped job's handle()
 * and restores the previous state afterwards.
 */
class WithTenantContext
{
    public function __construct(public int|string|null $tenantId) {}

    public function handle(object $job, Closure $next): mixed
    {
        if ($this->tenantId === null) {
            return $next($job);
        }

        return app(CurrentTenant::class)->forTenant($this->tenantId, fn () => $next($job));
    }
}
