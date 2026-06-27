<?php

use App\Support\CurrentTenant;
use Modules\Core\Models\Tenant;

if (! function_exists('current_tenant')) {
    function current_tenant(): CurrentTenant
    {
        return app(CurrentTenant::class);
    }
}

if (! function_exists('current_tenant_id')) {
    function current_tenant_id(): int|string|null
    {
        return app(CurrentTenant::class)->id();
    }
}

if (! function_exists('current_tenant_model')) {
    function current_tenant_model(): ?Tenant
    {
        return app(CurrentTenant::class)->model();
    }
}
