<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveApiTenant
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken && $token->tenant_id !== null) {
            $this->currentTenant->set($token->tenant_id);
        }

        return $next($request);
    }
}
