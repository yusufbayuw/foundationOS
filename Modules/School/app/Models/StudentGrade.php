<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\School\Database\Factories\StudentGradeFactory;

class StudentGrade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): StudentGradeFactory
    // {
    //     // return StudentGradeFactory::new();
    // }
}
