<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Poll extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'question',
        'options',
        'closes_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'closes_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function responses(): HasMany
    {
        return $this->hasMany(PollResponse::class);
    }
}
