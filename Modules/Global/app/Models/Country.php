<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
    ];

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }
}
