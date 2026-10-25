<?php

namespace Tests\Tenant;

use App\Exceptions\MissingTenantContextException;
use App\Queue\Middleware\WithTenantContext;
use App\Support\CurrentTenant;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\Tenant;
use Modules\Enrollment\Events\ApplicantAccepted;
use Modules\Enrollment\Models\Applicant;
use Modules\Finance\Listeners\CreateInitialInvoiceFromAcceptedApplicant;
use Modules\Procurement\Events\PurchaseRequisitionApproved;
use Modules\Procurement\Listeners\CreateRfqFromApprovedPurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Listeners\RunWorkflowAutomatedActions;
use Modules\Workflow\Models\WorkflowInstance;
use RuntimeException;
use Tests\TestCase;

class TenantQueuedListenerIsolationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_arbitrary_queued_listener_is_denied_even_if_framework_wrapper_was_allowlisted(): void
    {
        $tenant = Tenant::factory()->create();
        $event = new ApplicantAccepted($this->applicantFor($tenant));
        $job = new CallQueuedListener(RuntimeException::class, 'handle', [$event]);
        config(['tenancy.queue_tenantless_job_types' => [CallQueuedListener::class]]);
        $ran = false;

        try {
            (new WithTenantContext(null))->handle($job, function () use (&$ran): void {
                $ran = true;
            });

            $this->fail('Unknown listener must never run with a null tenant.');
        } catch (MissingTenantContextException) {
            $this->assertFalse($ran);
        }
    }

    public function test_allowlisted_listener_rejects_an_unexpected_event_type(): void
    {
        $tenant = Tenant::factory()->create();
        $job = new CallQueuedListener(
            CreateInitialInvoiceFromAcceptedApplicant::class,
            'handle',
            [new WorkflowSlaBreached($this->workflowInstanceFor($tenant))],
        );
        $ran = false;

        try {
            (new WithTenantContext(null))->handle($job, function () use (&$ran): void {
                $ran = true;
            });

            $this->fail('Mismatched listener and event must be rejected.');
        } catch (MissingTenantContextException) {
            $this->assertFalse($ran);
        }
    }

    public function test_known_listener_event_pairs_resolve_tenant_without_inheriting_previous_worker_state(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $context = app(CurrentTenant::class);

        $jobs = [
            new CallQueuedListener(
                RunWorkflowAutomatedActions::class,
                'handle',
                [new WorkflowSlaBreached($this->workflowInstanceFor($tenantB))],
            ),
            new CallQueuedListener(
                CreateInitialInvoiceFromAcceptedApplicant::class,
                'handle',
                [new ApplicantAccepted($this->applicantFor($tenantB))],
            ),
            new CallQueuedListener(
                CreateRfqFromApprovedPurchaseRequisition::class,
                'handle',
                [new PurchaseRequisitionApproved($this->requisitionFor($tenantB))],
            ),
        ];

        $priorTeam = getPermissionsTeamId();
        $context->set($tenantA);
        setPermissionsTeamId($tenantA->id);

        try {
            foreach ($jobs as $job) {
                $result = (new WithTenantContext(null))->handle($job, function () use ($context, $tenantB): string {
                    $this->assertEquals($tenantB->id, $context->id());
                    $this->assertEquals($tenantB->id, getPermissionsTeamId());

                    return 'handled';
                });

                $this->assertSame('handled', $result);
                $this->assertEquals($tenantA->id, $context->id());
                $this->assertEquals($tenantA->id, getPermissionsTeamId());
            }
        } finally {
            $context->forget();
            setPermissionsTeamId($priorTeam);
        }
    }

    public function test_queued_listener_cannot_override_a_mismatched_captured_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $event = new ApplicantAccepted($this->applicantFor($tenantB));
        $job = new CallQueuedListener(CreateInitialInvoiceFromAcceptedApplicant::class, 'handle', [$event]);
        $ran = false;

        try {
            (new WithTenantContext($tenantA->id))->handle($job, function () use (&$ran): void {
                $ran = true;
            });

            $this->fail('Job context must match the event tenant.');
        } catch (MissingTenantContextException) {
            $this->assertFalse($ran);
        }
    }

    public function test_listener_exception_restores_prior_tenant_and_permission_team(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $job = new CallQueuedListener(
            CreateInitialInvoiceFromAcceptedApplicant::class,
            'handle',
            [new ApplicantAccepted($this->applicantFor($tenantB))],
        );

        $context = app(CurrentTenant::class);
        $priorTeam = getPermissionsTeamId();
        $context->set($tenantA);
        setPermissionsTeamId($tenantA->id);

        try {
            try {
                (new WithTenantContext(null))->handle($job, function () use ($context, $tenantB): void {
                    $this->assertEquals($tenantB->id, $context->id());
                    throw new RuntimeException('listener failure');
                });

                $this->fail('The job exception must propagate.');
            } catch (RuntimeException $exception) {
                $this->assertSame('listener failure', $exception->getMessage());
                $this->assertEquals($tenantA->id, $context->id());
                $this->assertEquals($tenantA->id, getPermissionsTeamId());
            }
        } finally {
            $context->forget();
            setPermissionsTeamId($priorTeam);
        }
    }

    private function applicantFor(Tenant $tenant): Applicant
    {
        $applicant = new Applicant;
        $applicant->forceFill(['tenant_id' => $tenant->id]);
        $applicant->exists = true;

        return $applicant;
    }

    private function workflowInstanceFor(Tenant $tenant): WorkflowInstance
    {
        $instance = new WorkflowInstance;
        $instance->forceFill(['tenant_id' => $tenant->id]);
        $instance->exists = true;

        return $instance;
    }

    private function requisitionFor(Tenant $tenant): PurchaseRequisition
    {
        $requisition = new PurchaseRequisition;
        $requisition->forceFill(['tenant_id' => $tenant->id]);
        $requisition->exists = true;

        return $requisition;
    }
}
