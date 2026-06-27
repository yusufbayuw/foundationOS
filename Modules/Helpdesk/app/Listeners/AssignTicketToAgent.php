<?php

namespace Modules\Helpdesk\Listeners;

use App\Support\TypedValue;
use Modules\Helpdesk\Events\TicketCreated;
use Modules\Helpdesk\Models\Ticket;
use Modules\Helpdesk\Models\TicketCategory;

class AssignTicketToAgent
{
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        if ($ticket->assigned_to_user_id) {
            return;
        }

        $category = $ticket->ticket_category_id
            ? TicketCategory::query()->find($ticket->ticket_category_id)
            : null;

        $assigneeId = $category->default_assignee_user_id
            ?? $this->leastLoadedAgentId(
                TypedValue::int($ticket->tenant_id),
                TypedValue::nullableInt($category?->getKey()),
            );

        if (! $assigneeId) {
            return;
        }

        $slaDue = now()->addHours($category->resolution_hours ?? 24);

        $ticket->update([
            'assigned_to_user_id' => $assigneeId,
            'meta' => array_merge($ticket->meta ?? [], [
                'sla_due_at' => $slaDue->toIso8601String(),
            ]),
        ]);
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

        return TypedValue::nullableInt($row?->assigned_to_user_id);
    }
}
