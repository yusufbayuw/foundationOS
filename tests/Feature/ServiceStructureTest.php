<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Finance\Services\FinanceControlService;
use Modules\Finance\Services\Support\FinanceAuditRecorder;
use Modules\Finance\Services\Support\JournalEntryBalancer;
use Modules\Finance\Services\Support\PaymentJournalPoster;
use Modules\Finance\Services\Support\StudentInvoiceRecalculator;
use Modules\Workflow\Services\WorkflowDefinitionGraphMaterializer;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;
use Modules\Workflow\Services\WorkflowDefinitionPorter;
use Tests\TestCase;

class ServiceStructureTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_finance_control_service_is_a_thin_coordinator(): void
    {
        $source = file_get_contents(base_path('Modules/Finance/app/Services/FinanceControlService.php'));

        $this->assertLessThan(210, substr_count($source, "\n"));
        $this->assertStringContainsString('FinanceAuditRecorder', $source);
        $this->assertStringContainsString('StudentInvoiceRecalculator', $source);
        $this->assertStringContainsString('PaymentJournalPoster', $source);
        $this->assertStringNotContainsString('AuditLog::query', $source);
        $this->assertStringNotContainsString('JournalEntryLine::query', $source);
    }

    public function test_finance_support_services_are_container_resolvable(): void
    {
        $this->assertInstanceOf(FinanceControlService::class, app(FinanceControlService::class));
        $this->assertInstanceOf(FinanceAuditRecorder::class, app(FinanceAuditRecorder::class));
        $this->assertInstanceOf(StudentInvoiceRecalculator::class, app(StudentInvoiceRecalculator::class));
        $this->assertInstanceOf(PaymentJournalPoster::class, app(PaymentJournalPoster::class));
        $this->assertInstanceOf(JournalEntryBalancer::class, app(JournalEntryBalancer::class));
    }

    public function test_workflow_graph_materializer_is_shared_by_porter_and_lifecycle(): void
    {
        $porterSource = file_get_contents(base_path('Modules/Workflow/app/Services/WorkflowDefinitionPorter.php'));
        $lifecycleSource = file_get_contents(base_path('Modules/Workflow/app/Services/WorkflowDefinitionLifecycleService.php'));

        $this->assertStringContainsString('WorkflowDefinitionGraphMaterializer', $porterSource);
        $this->assertStringContainsString('WorkflowDefinitionGraphMaterializer', $lifecycleSource);
        $this->assertStringNotContainsString('WorkflowStep::query()->create', $porterSource);
        $this->assertStringNotContainsString('WorkflowStep::query()->create', $lifecycleSource);

        $this->assertInstanceOf(WorkflowDefinitionGraphMaterializer::class, app(WorkflowDefinitionGraphMaterializer::class));
        $this->assertInstanceOf(WorkflowDefinitionPorter::class, app(WorkflowDefinitionPorter::class));
        $this->assertInstanceOf(WorkflowDefinitionLifecycleService::class, app(WorkflowDefinitionLifecycleService::class));
    }
}
