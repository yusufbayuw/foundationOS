<?php

namespace Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Enrollment\Database\Factories\ApplicantFactory;

class Applicant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ApplicantFactory
    // {
    //     // return ApplicantFactory::new();
    // }
}
