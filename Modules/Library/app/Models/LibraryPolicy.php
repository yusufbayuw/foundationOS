<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Concerns\BelongsToTenant;

class LibraryPolicy extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'name',
        'max_books',
        'loan_period_days',
        'fine_per_day',
        'max_extensions',
        'grace_period_days',
        'reservation_pickup_days',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'max_books' => 'integer',
            'loan_period_days' => 'integer',
            'fine_per_day' => 'decimal:2',
            'max_extensions' => 'integer',
            'grace_period_days' => 'integer',
            'reservation_pickup_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
