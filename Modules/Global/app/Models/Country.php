<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * @return HasMany<Province, $this>
     */
    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }
}
