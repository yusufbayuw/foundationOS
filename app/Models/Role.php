<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property int|null $team_id
 */
class Role extends SpatieRole
{
    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
