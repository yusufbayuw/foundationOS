<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\School\Database\Factories\ClassStudentFactory;

class ClassStudent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ClassStudentFactory
    // {
    //     // return ClassStudentFactory::new();
    // }
}
