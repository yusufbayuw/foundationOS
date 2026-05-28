<?php

namespace Modules\Exam\Providers;

use Modules\Exam\Services\CampusGradeBridgeService;
use Modules\Exam\Services\ExamContextResolver;
use Modules\Exam\Services\ExamPublishService;
use Modules\Exam\Services\ExamRuntimeSyncService;
use Modules\Exam\Services\ExamTokenService;
use Modules\Exam\Services\SchoolGradeBridgeService;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ExamServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Exam';

    protected string $nameLower = 'exam';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(module_path('Exam', 'config/exam.php'), 'exam');

        $this->app->singleton(ExamContextResolver::class);
        $this->app->singleton(ExamTokenService::class);
        $this->app->singleton(SchoolGradeBridgeService::class);
        $this->app->singleton(CampusGradeBridgeService::class);

        $this->app->singleton(ExamPublishService::class, function ($app): ExamPublishService {
            return new ExamPublishService(
                $app->make(ExamContextResolver::class),
            );
        });

        $this->app->singleton(ExamRuntimeSyncService::class, function ($app): ExamRuntimeSyncService {
            return new ExamRuntimeSyncService([
                $app->make(SchoolGradeBridgeService::class),
                $app->make(CampusGradeBridgeService::class),
            ]);
        });
    }
}
