<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class SetUserLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $locale = $user->preferred_locale;

            if (! $locale) {
                // Fall back to tenant's default locale setting
                $tenant = $request->route('tenant');
                if ($tenant instanceof Tenant) {
                    $setting = $tenant->tenantSettings()
                        ->where('group', 'core')
                        ->where('key', 'default_locale')
                        ->first();
                    $locale = $setting?->value;
                }
                $locale ??= config('app.locale', 'id');
            }

            App::setLocale($locale);
        } elseif ($tenant = $request->route('tenant')) {
            // Guest on tenant panel: use tenant's default locale setting
            if ($tenant instanceof Tenant) {
                $setting = $tenant->tenantSettings()->where('group', 'core')->where('key', 'default_locale')->first();
                if ($setting) {
                    App::setLocale($setting->value);
                }
            }
        }

        return $next($request);
    }
}
