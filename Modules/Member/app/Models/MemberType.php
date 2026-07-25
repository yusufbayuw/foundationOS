<?php

namespace Modules\Member\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
