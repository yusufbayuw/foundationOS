<?php

namespace Modules\Exam\Providers;

use Illuminate\Contracts\Foundation\Application;
use Modules\Exam\Console\Commands\ProvisionExamSecurityCommand;
use Modules\Exam\Console\Commands\SyncExamResultsCommand;
use Modules\Exam\Contracts\GradeBridgeInterface;
use Modules\Exam\Services\CampusGradeBridgeService;
use Modules\Exam\Services\ExamAcademicContextService;
use Modules\Exam\Services\ExamAnalyticsService;
use Modules\Exam\Services\ExamAuditLogger;
use Modules\Exam\Services\ExamAuthorizationService;
use Modules\Exam\Services\ExamContextResolver;
use Modules\Exam\Services\ExamDefinitionScoreCalculator;
use Modules\Exam\Services\ExamGradebookEventDispatcher;
use Modules\Exam\Services\ExamGradebookExportService;
use Modules\Exam\Services\ExamManualGradingService;
use Modules\Exam\Services\ExamParticipantResolver;
use Modules\Exam\Services\ExamParticipantSyncService;
use Modules\Exam\Services\ExamPublishService;
use Modules\Exam\Services\ExamResultExportService;
use Modules\Exam\Services\ExamResultSyncService;
use Modules\Exam\Services\ExamRuntimeClient;
use Modules\Exam\Services\ExamRuntimePayloadBuilder;
use Modules\Exam\Services\ExamRuntimeSyncService;
use Modules\Exam\Services\ExamShieldProvisioner;
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

    protected array $commands = [
        SyncExamResultsCommand::class,
        ProvisionExamSecurityCommand::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(module_path('Exam', 'config/exam.php'), 'exam');

        $this->app->singleton(ExamContextResolver::class);
        $this->app->singleton(ExamTokenService::class);
        $this->app->singleton(ExamParticipantResolver::class);
        $this->app->singleton(ExamParticipantSyncService::class);
        $this->app->singleton(SchoolGradeBridgeService::class);
        $this->app->singleton(CampusGradeBridgeService::class);

        $this->app->singleton(ExamDefinitionScoreCalculator::class);
        $this->app->singleton(ExamAcademicContextService::class);
        $this->app->singleton(ExamRuntimeClient::class);
        $this->app->singleton(ExamRuntimePayloadBuilder::class);
        $this->app->singleton(ExamPublishService::class);
        $this->app->singleton(ExamResultSyncService::class);
        $this->app->singleton(ExamManualGradingService::class);
        $this->app->singleton(ExamAnalyticsService::class);
        $this->app->singleton(ExamResultExportService::class);
        $this->app->singleton(ExamGradebookEventDispatcher::class);
        $this->app->singleton(ExamGradebookExportService::class);
        $this->app->singleton(ExamAuthorizationService::class);
        $this->app->singleton(ExamAuditLogger::class);
        $this->app->singleton(ExamShieldProvisioner::class);

        $this->app->singleton(ExamRuntimeSyncService::class, function (Application $app): ExamRuntimeSyncService {
            return new ExamRuntimeSyncService([
                $this->resolveGradeBridge($app, SchoolGradeBridgeService::class),
                $this->resolveGradeBridge($app, CampusGradeBridgeService::class),
            ]);
        });
    }

    /**
     * @param  class-string<GradeBridgeInterface>  $bridgeClass
     */
    private function resolveGradeBridge(Application $app, string $bridgeClass): GradeBridgeInterface
    {
        $bridge = $app->make($bridgeClass);
        if (! $bridge instanceof GradeBridgeInterface) {
            throw new \RuntimeException('Invalid grade bridge binding.');
        }

        return $bridge;
    }
}
