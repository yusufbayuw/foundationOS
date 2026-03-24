<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Global\Database\Factories\VillageFactory;

class Village extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): VillageFactory
    // {
    //     // return VillageFactory::new();
    // }
}
