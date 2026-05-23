<?php

namespace Modules\Monitoring\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;

class AuditLog extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    public const CATEGORY_GENERAL = 'general';

    public const CATEGORY_SECURITY = 'security';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'organization_id',
        'auditable_type',
        'auditable_id',
        'action',
        'category',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'request_id',
        'status',
        'error_message',
        'prev_hash',
        'current_hash',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::creating(function (AuditLog $auditLog): void {
            $auditLog->status ??= 'success';

            $previousHash = self::query()
                ->where('tenant_id', $auditLog->tenant_id)
                ->latest('id')
                ->value('current_hash');

            $auditLog->prev_hash = $previousHash;
            $auditLog->current_hash = self::calculateHash($previousHash, $auditLog->hashPayload());
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function hashPayload(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            'organization_id' => $this->organization_id,
            'auditable_type' => $this->auditable_type,
            'auditable_id' => $this->auditable_id,
            'action' => $this->action,
            'category' => $this->category,
            'description' => $this->description,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'request_id' => $this->request_id,
            'status' => $this->status,
            'error_message' => $this->error_message,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function calculateHash(?string $previousHash, array $payload): string
    {
        ksort($payload);

        return hash('sha256', ($previousHash ?? '').json_encode($payload, JSON_THROW_ON_ERROR));
    }
}
