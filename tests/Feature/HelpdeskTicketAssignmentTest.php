<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\UserTenantRole;
use Modules\Helpdesk\Events\TicketCreated;
use Modules\Helpdesk\Listeners\AssignTicketToAgent;
use Modules\Helpdesk\Models\Ticket;
use Modules\Helpdesk\Services\TicketCategoryRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class HelpdeskTicketAssignmentTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_ticket_and_category_assignment_columns_are_available_after_migration(): void
    {
        foreach (['ticket_category_id', 'priority', 'assigned_to_user_id', 'escalated_at', 'closed_at', 'ai_suggested_category'] as $column) {
            $this->assertTrue(Schema::hasColumn('tickets', $column), $column);
        }

        foreach (['response_hours', 'resolution_hours', 'default_assignee_user_id'] as $column) {
            $this->assertTrue(Schema::hasColumn('ticket_categories', $column), $column);
        }
    }

    public function test_new_ticket_gets_default_assignee_and_category_sla_deadlines(): void
    {
        ['tenant' => $tenant, 'organization' => $organization, 'user' => $user] =
            $this->makeTenantContext(['core', 'helpdesk']);

        $category = app(TicketCategoryRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: 'NET',
            name: 'Network',
            responseHours: 2,
            resolutionHours: 8,
            defaultAssigneeUserId: $user->id,
        );

        $ticket = Ticket::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'ticket_category_id' => $category->id,
            'name' => 'Wi-Fi down',
            'status' => 'open',
            'priority' => 'high',
        ])->fresh();

        $this->assertSame((int) $user->id, (int) $ticket->assigned_to_user_id);
        $this->assertNotEmpty($ticket->meta['sla_due_at'] ?? null);
        $this->assertNotEmpty($ticket->meta['sla_response_due_at'] ?? null);

        $previousMeta = $ticket->meta;
        app(AssignTicketToAgent::class)->handle(new TicketCreated($ticket));

        $this->assertSame($previousMeta, $ticket->fresh()->meta);
    }

    public function test_no_available_agent_still_records_resolution_and_response_sla(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] =
            $this->makeTenantContext(['core', 'helpdesk']);

        $ticket = Ticket::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'Projector broken',
            'status' => 'open',
        ])->fresh();

        $this->assertNull($ticket->assigned_to_user_id);
        $this->assertNotEmpty($ticket->meta['sla_due_at'] ?? null);
        $this->assertNotEmpty($ticket->meta['sla_response_due_at'] ?? null);
    }

    public function test_foreign_tenant_default_agent_is_not_auto_assigned(): void
    {
        ['tenant' => $tenantA, 'user' => $userA] =
            $this->makeTenantContext(['core', 'helpdesk']);
        ['tenant' => $tenantB, 'organization' => $organizationB] =
            $this->makeTenantContext(['core', 'helpdesk']);

        $categoryB = app(TicketCategoryRegistrationService::class)->register(
            tenantId: $tenantB->id,
            organizationId: $organizationB->id,
            code: 'SEC',
            name: 'Security',
            defaultAssigneeUserId: $userA->id,
        );

        $ticket = Ticket::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'organization_id' => $organizationB->id,
            'ticket_category_id' => $categoryB->id,
            'name' => 'Security incident',
            'status' => 'open',
        ])->fresh();

        $this->assertNull($ticket->assigned_to_user_id);
        $this->assertNotEmpty($ticket->meta['sla_due_at'] ?? null);
    }

    public function test_expired_tenant_membership_cannot_receive_default_assignment(): void
    {
        ['tenant' => $tenant, 'organization' => $organization, 'user' => $user] =
            $this->makeTenantContext(['core', 'helpdesk']);

        UserTenantRole::withoutTenantScope()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->update(['expires_at' => now()->subDay()]);

        $category = app(TicketCategoryRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: 'HR',
            name: 'HR support',
            defaultAssigneeUserId: $user->id,
        );

        $ticket = Ticket::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'ticket_category_id' => $category->id,
            'name' => 'Leave approval issue',
            'status' => 'open',
        ])->fresh();

        $this->assertNull($ticket->assigned_to_user_id);
        $this->assertNotEmpty($ticket->meta['sla_response_due_at'] ?? null);
    }

    public function test_cross_tenant_category_does_not_assign_another_tenants_default_agent(): void
    {
        ['tenant' => $tenantA, 'organization' => $organizationA, 'user' => $agentA] =
            $this->makeTenantContext(['core', 'helpdesk']);
        ['tenant' => $tenantB, 'organization' => $organizationB] =
            $this->makeTenantContext(['core', 'helpdesk']);

        $categoryA = app(TicketCategoryRegistrationService::class)->register(
            tenantId: $tenantA->id,
            organizationId: $organizationA->id,
            code: 'NET',
            name: 'Network',
            defaultAssigneeUserId: $agentA->id,
        );

        $ticketB = Ticket::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'organization_id' => $organizationB->id,
            'ticket_category_id' => $categoryA->id,
            'name' => 'Cross tenant category mismatch',
            'status' => 'open',
        ])->fresh();

        $this->assertNull($ticketB->assigned_to_user_id);
        $this->assertNotEmpty($ticketB->meta['sla_due_at'] ?? null);
    }
}
