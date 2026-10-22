<?php

namespace Modules\Helpdesk\Listeners;

use Modules\Helpdesk\Events\TicketCreated;
use Modules\Helpdesk\Models\Ticket;
use Modules\Helpdesk\Models\TicketCategory;

class AssignTicketToAgent
{
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        $category = $ticket->ticket_category_id
            ? TicketCategory::withoutTenantScope()
                ->where('tenant_id', $ticket->tenant_id)
                ->find($ticket->ticket_category_id)
            : null;

        $assigneeId = $ticket->assigned_to_user_id
            ?? $category?->default_assignee_user_id
            ?? $this->leastLoadedAgentId((int) $ticket->tenant_id, $category?->getKey());

        $meta = $ticket->meta ?? [];
        $previousMeta = $meta;

        if (! isset($meta['sla_due_at'])) {
            $meta['sla_due_at'] = now()
                ->addHours(max(1, (int) ($category?->resolution_hours ?? 24)))
                ->toIso8601String();
        }

        if (! isset($meta['sla_response_due_at'])) {
            $meta['sla_response_due_at'] = now()
                ->addHours(max(1, (int) ($category?->response_hours ?? 4)))
                ->toIso8601String();
        }

        $changes = [];

        if ($meta !== $previousMeta) {
            $changes['meta'] = $meta;
        }

        if (! $ticket->assigned_to_user_id && $assigneeId) {
            $changes['assigned_to_user_id'] = $assigneeId;
        }

        if ($changes !== []) {
            $ticket->update($changes);
        }
    }

    protected function leastLoadedAgentId(int $tenantId, ?int $categoryId): ?int
    {
        $query = Ticket::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('assigned_to_user_id');

        if ($categoryId) {
            $query->where('ticket_category_id', $categoryId);
        }

        $row = $query
            ->selectRaw('assigned_to_user_id, COUNT(*) as open_count')
            ->groupBy('assigned_to_user_id')
            ->orderBy('open_count')
            ->first();

        return $row?->assigned_to_user_id;
    }
}
