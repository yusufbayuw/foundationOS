<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Modules\Core\Models\Tenant;

/**
 * Independent machine principal: no human user's roles/permissions.
 * Only explicitly allowlisted read-only APIs accept this principal.
 */
class TenantServiceIntegration extends Authenticatable
{
    use HasApiTokens;

    protected $attributes = ['status' => 'active'];

    protected $fillable = [
        'tenant_id',
        'name',
        'status',
        'expires_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->revoked_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Operator-only provisioning; read-only tokens expire after 30 days.
     */
    public function issueReadToken(string $name): NewAccessToken
    {
        if (! $this->exists || ! $this->isActive() || $this->tenant_id === null) {
            throw new \LogicException('Cannot issue tokens for an inactive service integration.');
        }

        return DB::transaction(function () use ($name): NewAccessToken {
            $issued = $this->createToken($name, ['api:read'], now()->addDays(30));
            $issued->accessToken->forceFill(['tenant_id' => $this->tenant_id])->save();

            return $issued;
        });
    }
}
