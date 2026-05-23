<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Wisuda extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'yudisium_id',
        'name',
        'held_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'held_at' => 'date',
        ];
    }

    public function yudisium(): BelongsTo
    {
        return $this->belongsTo(Yudisium::class);
    }
}
