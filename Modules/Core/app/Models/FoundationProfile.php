<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class FoundationProfile extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'vision',
        'mission',
        'core_values',
        'logo_path',
        'profile_document_path',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
