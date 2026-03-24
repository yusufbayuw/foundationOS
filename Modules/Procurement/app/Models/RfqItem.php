<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Procurement\Database\Factories\RfqItemFactory;

class RfqItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): RfqItemFactory
    // {
    //     // return RfqItemFactory::new();
    // }
}
