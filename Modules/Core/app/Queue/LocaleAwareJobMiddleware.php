<?php

namespace Modules\Core\Queue;

use Closure;
use Illuminate\Support\Facades\App;

/**
 * Restores the locale that was active when the job was dispatched.
 * Add to ShouldQueue jobs via middleware(): return [new LocaleAwareJobMiddleware($this->locale)];
 */
class LocaleAwareJobMiddleware
{
    public function __construct(public readonly string $locale = 'id') {}

    public function handle(object $job, Closure $next): void
    {
        App::setLocale($this->locale);

        $next($job);
    }
}
