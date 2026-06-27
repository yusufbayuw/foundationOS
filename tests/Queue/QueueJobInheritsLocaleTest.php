<?php

namespace Tests\Queue;

use Illuminate\Support\Facades\App;
use Modules\Core\Queue\LocaleAwareJobMiddleware;
use Tests\TestCase;

class QueueJobInheritsLocaleTest extends TestCase
{
    public function test_middleware_sets_locale_before_job_executes(): void
    {
        App::setLocale('en');

        $middleware = new LocaleAwareJobMiddleware('id');

        $localeInsideJob = null;

        $middleware->handle(new \stdClass, function () use (&$localeInsideJob) {
            $localeInsideJob = App::getLocale();
        });

        $this->assertSame('id', $localeInsideJob);
    }

    public function test_middleware_restores_en_locale(): void
    {
        App::setLocale('id');

        $middleware = new LocaleAwareJobMiddleware('en');

        $localeInsideJob = null;

        $middleware->handle(new \stdClass, function () use (&$localeInsideJob) {
            $localeInsideJob = App::getLocale();
        });

        $this->assertSame('en', $localeInsideJob);
    }

    public function test_middleware_defaults_to_id_locale(): void
    {
        $middleware = new LocaleAwareJobMiddleware;

        $localeInsideJob = null;

        $middleware->handle(new \stdClass, function () use (&$localeInsideJob) {
            $localeInsideJob = App::getLocale();
        });

        $this->assertSame('id', $localeInsideJob);
    }
}
