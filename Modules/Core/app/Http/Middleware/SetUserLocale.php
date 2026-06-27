<?php

namespace Modules\Core\Http\Middleware;

use App\Support\TypedValue;
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
            $locale = TypedValue::string($user?->preferred_locale);

            if (! $locale) {
                // Fall back to tenant's default locale setting
                $tenant = $request->route('tenant');
                if ($tenant instanceof Tenant) {
                    $setting = $tenant->tenantSettings()
                        ->where('group', 'core')
                        ->where('key', 'default_locale')
                        ->first();
                    $locale = TypedValue::string($setting?->value);
                }
                $locale = $locale !== '' ? $locale : TypedValue::string(config('app.locale', 'id'), 'id');
            }

            App::setLocale($locale);
        } elseif ($tenant = $request->route('tenant')) {
            // Guest on tenant panel: use tenant's default locale setting
            if ($tenant instanceof Tenant) {
                $setting = $tenant->tenantSettings()->where('group', 'core')->where('key', 'default_locale')->first();
                if ($setting) {
                    App::setLocale(TypedValue::string($setting->value, TypedValue::string(config('app.locale', 'id'), 'id')));
                }
            }
        }

        return TypedValue::response($next($request));
    }
}
