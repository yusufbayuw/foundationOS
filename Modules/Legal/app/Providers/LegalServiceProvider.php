<?php

namespace Modules\Legal\Providers;

use Modules\Legal\Services\Esign\EsignManager;
use Modules\Legal\Services\Esign\ManualUploadEsignProvider;
use Nwidart\Modules\Support\ModuleServiceProvider;

class LegalServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Legal';

    protected string $nameLower = 'legal';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->singleton(EsignManager::class, function (): EsignManager {
            $manager = new EsignManager;
            $manager->register(new ManualUploadEsignProvider);

            return $manager;
        });
    }
}
