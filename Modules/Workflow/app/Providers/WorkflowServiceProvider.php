<?php

namespace Modules\Workflow\Providers;

use Livewire\Livewire;
use Modules\Workflow\Console\Commands\SetupApprovalLimitsCommand;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowAssigneeResolver;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Contracts\WorkflowTransitionResolver;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Modules\Workflow\Services\DatabaseWorkflowAssigneeResolver;
use Modules\Workflow\Services\DatabaseWorkflowAuditLogger;
use Modules\Workflow\Services\DatabaseWorkflowEngine;
use Modules\Workflow\Services\DatabaseWorkflowInstanceStarter;
use Modules\Workflow\Services\DatabaseWorkflowResolver;
use Modules\Workflow\Services\JsonLogicRuleEngine;
use Modules\Workflow\Services\JsonLogicWorkflowTransitionResolver;
use Modules\Workflow\Services\LaravelWorkflowFormSchemaValidator;
use Modules\Workflow\Services\QueuedWorkflowSlaService;
use Nwidart\Modules\Support\ModuleServiceProvider;

class WorkflowServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Workflow';

    protected string $nameLower = 'workflow';

    protected array $commands = [
        SetupApprovalLimitsCommand::class,
    ];

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Livewire::component('workflow-canvas', WorkflowCanvas::class);

        $this->app->bind(WorkflowResolver::class, DatabaseWorkflowResolver::class);
        $this->app->bind(RuleEngine::class, JsonLogicRuleEngine::class);
        $this->app->bind(WorkflowInstanceStarter::class, DatabaseWorkflowInstanceStarter::class);
        $this->app->bind(WorkflowEngine::class, DatabaseWorkflowEngine::class);
        $this->app->bind(WorkflowFormSchemaValidator::class, LaravelWorkflowFormSchemaValidator::class);
        $this->app->bind(WorkflowTransitionResolver::class, JsonLogicWorkflowTransitionResolver::class);
        $this->app->bind(WorkflowAssigneeResolver::class, DatabaseWorkflowAssigneeResolver::class);
        $this->app->bind(WorkflowAuditLogger::class, DatabaseWorkflowAuditLogger::class);
        $this->app->bind(WorkflowSlaService::class, QueuedWorkflowSlaService::class);
    }
}
