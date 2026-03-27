<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'district_id',
        'code',
        'name',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
