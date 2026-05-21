<?php

namespace App\Concerns;

use App\Queue\Middleware\WithTenantContext;
use App\Support\CurrentTenant;
use Modules\Core\Models\Tenant;

/**
 * Trait for queued jobs (and other queue-able classes) that must run in a
 * tenant context.
 *
 * Adds a `$tenantId` property that survives serialization, plus job middleware
 * that re-binds CurrentTenant around handle() and restores prior state.
 *
 * Usage:
 *   class MyJob implements ShouldQueue
 *   {
 *       use Queueable, InteractsWithTenant;
 *
 *       public function __construct(public int $entityId)
 *       {
 *           $this->captureCurrentTenant();
 *       }
 *   }
 *
 * Or explicitly:
 *   MyJob::dispatch($id)->onTenant($tenantId);
 */
trait InteractsWithTenant
{
    public int|string|null $tenantId = null;

    /**
     * Capture the current tenant context for replay during handle().
     */
    public function captureCurrentTenant(): void
    {
        if (app()->bound(CurrentTenant::class)) {
            $this->tenantId = app(CurrentTenant::class)->id();
        }
    }

    public function onTenant(int|string|Tenant|null $tenant): static
    {
        $this->tenantId = $tenant instanceof Tenant ? $tenant->getKey() : $tenant;

        return $this;
    }

    /**
     * Job middleware: wraps handle() in the captured tenant context.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new WithTenantContext($this->tenantId)];
    }
}
