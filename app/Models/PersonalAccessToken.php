<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken as SanctumToken;
use Modules\Core\Models\Tenant;

class PersonalAccessToken extends SanctumToken
{
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'scopes',
        'tenant_id',
        'expires_at',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'scopes' => 'array',
        ]);
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];

        return empty($scopes) || in_array($scope, $scopes, true) || in_array('*', $scopes, true);
    }
}
