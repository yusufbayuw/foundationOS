<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Campus\Database\Factories\ThesisFactory;

class Thesis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ThesisFactory
    // {
    //     // return ThesisFactory::new();
    // }
}
