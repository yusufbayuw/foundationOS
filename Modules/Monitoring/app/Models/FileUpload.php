<?php

namespace Modules\Monitoring\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Monitoring\Database\Factories\FileUploadFactory;

class FileUpload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): FileUploadFactory
    // {
    //     // return FileUploadFactory::new();
    // }
}
