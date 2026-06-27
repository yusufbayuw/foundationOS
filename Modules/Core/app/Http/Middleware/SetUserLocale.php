<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\Tenant;
use Modules\Core\Support\TenantSettingsResolver;
use Symfony\Component\HttpFoundation\Response;

class SetUserLocale
{
    public function __construct(
        private readonly TenantSettingsResolver $tenantSettings,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $locale = $user->preferred_locale;

            if (! $locale) {
                $locale = $this->resolveTenantDefaultLocale($request->route('tenant'));
                $locale ??= config('app.locale', 'id');
            }

            App::setLocale($locale);
        } elseif ($tenant = $request->route('tenant')) {
            $locale = $this->resolveTenantDefaultLocale($tenant);
            if ($locale) {
                App::setLocale($locale);
            }
        }

        return $next($request);
    }

    protected function resolveTenantDefaultLocale(mixed $tenant): ?string
    {
        if (! $tenant instanceof Tenant) {
            return null;
        }

        $value = $this->tenantSettings->value($tenant->getKey(), 'core', 'default_locale');

        return is_string($value) && $value !== '' ? $value : null;
    }
}
