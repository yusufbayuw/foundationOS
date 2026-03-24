<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Campus\Database\Factories\StudyPlanFactory;

class StudyPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): StudyPlanFactory
    // {
    //     // return StudyPlanFactory::new();
    // }
}
