<?php

namespace Modules\Helpdesk\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Helpdesk\Events\TicketCreated;
use Modules\Helpdesk\Listeners\AssignTicketToAgent;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class Ticket extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::created(function (Ticket $ticket): void {
            app(AssignTicketToAgent::class)
                ->handle(new TicketCreated($ticket));
        });
    }

    protected $table = 'tickets';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'ticket_category_id',
        'code',
        'name',
        'status',
        'priority',
        'description',
        'assigned_to_user_id',
        'escalated_at',
        'ai_suggested_category',
        'closed_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'escalated_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
